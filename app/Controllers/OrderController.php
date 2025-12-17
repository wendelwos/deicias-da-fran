<?php
/**
 * Order Controller
 */

class OrderController
{

    public function store(): void
    {
        // Validate input
        $errors = [];

        $name = trim($_POST['customer_name'] ?? '');
        $phone = trim($_POST['customer_phone'] ?? '');
        $deliveryType = $_POST['delivery_type'] ?? 'RETIRADA';
        $address = trim($_POST['address'] ?? '');
        $notes = trim($_POST['notes'] ?? '');
        $paymentMethod = $_POST['payment_method'] ?? 'PIX';
        $changeFor = !empty($_POST['change_for']) ? floatval($_POST['change_for']) : null;
        $cartItems = json_decode($_POST['cart_items'] ?? '[]', true);

        if (empty($name)) {
            $errors['customer_name'] = 'Nome é obrigatório';
        }

        if (empty($phone)) {
            $errors['customer_phone'] = 'Telefone é obrigatório';
        }

        if ($deliveryType === 'ENTREGA' && empty($address)) {
            $errors['address'] = 'Endereço é obrigatório para entrega';
        }

        if (empty($cartItems)) {
            $errors['cart'] = 'Carrinho está vazio';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_input'] = $_POST;
            redirect('checkout');
            return;
        }

        // Calculate totals
        $subtotal = 0;
        foreach ($cartItems as $item) {
            $subtotal += ($item['price'] ?? 0) * ($item['quantity'] ?? 1);
        }

        $deliveryFee = 0;
        if ($deliveryType === 'ENTREGA') {
            $deliveryFee = config('orders.delivery_fee', 5.00);
        }

        $total = $subtotal + $deliveryFee;

        // Get next order number
        $lastOrder = Database::fetch("SELECT MAX(order_number) as max_num FROM orders");
        $orderNumber = ($lastOrder['max_num'] ?? 0) + 1;

        // Create order
        $orderId = Database::insert('orders', [
            'order_number' => $orderNumber,
            'customer_name' => $name,
            'customer_phone' => $phone,
            'delivery_type' => $deliveryType,
            'address' => $address,
            'notes' => $notes,
            'subtotal' => $subtotal,
            'delivery_fee' => $deliveryFee,
            'total' => $total,
            'status' => 'RECEBIDO',
            'payment_method' => $paymentMethod,
            'change_for' => $changeFor,
        ]);

        // Create order items
        foreach ($cartItems as $item) {
            Database::insert('order_items', [
                'order_id' => $orderId,
                'product_id' => $item['id'] ?? null,
                'product_name' => $item['name'] ?? 'Item',
                'quantity' => $item['quantity'] ?? 1,
                'unit_price' => $item['price'] ?? 0,
                'subtotal' => ($item['price'] ?? 0) * ($item['quantity'] ?? 1),
                'customizations' => isset($item['customizations']) ? json_encode($item['customizations']) : null,
                'notes' => $item['notes'] ?? null,
            ]);
        }

        // Log the order
        Database::insert('audit_logs', [
            'action' => 'ORDER_CREATED',
            'model_type' => 'orders',
            'model_id' => $orderId,
            'new_values' => json_encode(['order_number' => $orderNumber, 'total' => $total]),
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
        ]);

        // Clear session errors
        unset($_SESSION['errors'], $_SESSION['old_input']);

        // Redirect to order status page
        redirect("pedido/$orderId");
    }

    public function status(string $id): void
    {
        $order = Database::fetch("SELECT * FROM orders WHERE id = ?", [$id]);

        if (!$order) {
            http_response_code(404);
            view('errors/404');
            return;
        }

        $items = Database::fetchAll("
            SELECT * FROM order_items WHERE order_id = ?
        ", [$id]);

        // Generate WhatsApp message
        $whatsappMessage = $this->generateWhatsAppMessage($order, $items);
        $whatsappLink = whatsappLink($whatsappMessage);

        view('public/order-status', [
            'order' => $order,
            'items' => $items,
            'whatsappLink' => $whatsappLink,
        ]);
    }

    public function ready(): void
    {
        view('public/ready');
    }

    private function generateWhatsAppMessage(array $order, array $items): string
    {
        $paymentLabels = [
            'PIX' => 'PIX',
            'DINHEIRO' => 'Dinheiro',
            'CARTAO_CREDITO' => 'Cartão de Crédito',
            'CARTAO_DEBITO' => 'Cartão de Débito',
        ];

        $message = "🍔🍝 *PEDIDO #{$order['order_number']}* - Delícias da Fran\n";
        $message .= "━━━━━━━━━━━━━━━━━━━━━\n\n";

        $message .= "📋 *DADOS DO CLIENTE*\n";
        $message .= "👤 Nome: {$order['customer_name']}\n";
        $message .= "📞 Telefone: {$order['customer_phone']}\n";
        $message .= "📅 Data: " . date('d/m/Y', strtotime($order['created_at'])) . "\n";
        $message .= "⏰ Horário: " . date('H:i', strtotime($order['created_at'])) . "\n";
        $message .= "🏪 Tipo: " . ($order['delivery_type'] === 'ENTREGA' ? 'Entrega' : 'Retirada') . "\n";

        if ($order['delivery_type'] === 'ENTREGA' && !empty($order['address'])) {
            $message .= "📍 Endereço: {$order['address']}\n";
        }

        $message .= "\n━━━━━━━━━━━━━━━━━━━━━\n";
        $message .= "🛒 *ITENS DO PEDIDO*\n\n";

        foreach ($items as $item) {
            $subtotal = money($item['subtotal']);
            $message .= "• {$item['quantity']}x {$item['product_name']} - {$subtotal}\n";

            if (!empty($item['customizations'])) {
                $customs = json_decode($item['customizations'], true);
                if ($customs) {
                    foreach ($customs as $type => $values) {
                        if (is_array($values)) {
                            $message .= "   ↳ " . ucfirst($type) . ": " . implode(', ', $values) . "\n";
                        } else {
                            $message .= "   ↳ " . ucfirst($type) . ": $values\n";
                        }
                    }
                }
            }
        }

        $message .= "\n━━━━━━━━━━━━━━━━━━━━━\n";
        $message .= "💰 *PAGAMENTO*\n";
        $message .= "💳 Forma: " . ($paymentLabels[$order['payment_method'] ?? 'PIX'] ?? $order['payment_method']) . "\n";

        if ($order['payment_method'] === 'DINHEIRO' && !empty($order['change_for'])) {
            $message .= "💵 Troco para: " . money($order['change_for']) . "\n";
        }

        $message .= "\n📊 Subtotal: " . money($order['subtotal']) . "\n";
        if ($order['delivery_fee'] > 0) {
            $message .= "🚚 Taxa de entrega: " . money($order['delivery_fee']) . "\n";
        }
        $message .= "✅ *TOTAL: " . money($order['total']) . "*\n";

        if (!empty($order['notes'])) {
            $message .= "\n📝 *OBSERVAÇÕES:*\n{$order['notes']}\n";
        }

        $message .= "\n━━━━━━━━━━━━━━━━━━━━━";

        return $message;
    }
}
