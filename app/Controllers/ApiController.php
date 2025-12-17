<?php
/**
 * API Controller - Handles AJAX requests
 */

class ApiController
{

    /**
     * Get ready orders for public display
     */
    public function readyOrders(): void
    {
        // Get PREPARANDO orders
        $preparando = Database::fetchAll("
            SELECT o.id, o.order_number, o.customer_name, o.prepared_at, 'PREPARANDO' as status
            FROM orders o 
            WHERE o.status = 'PREPARANDO' 
            ORDER BY o.prepared_at ASC
            LIMIT 20
        ");

        // Get PRONTO orders
        $prontos = Database::fetchAll("
            SELECT o.id, o.order_number, o.customer_name, o.ready_at, 'PRONTO' as status
            FROM orders o 
            WHERE o.status = 'PRONTO' 
            ORDER BY o.ready_at DESC
            LIMIT 20
        ");

        json([
            'success' => true,
            'preparando' => $preparando,
            'prontos' => $prontos,
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Get orders for kitchen display
     */
    public function kitchenOrders(): void
    {
        if (!auth()) {
            json(['success' => false, 'error' => 'Unauthorized'], 401);
            return;
        }

        $recebidos = Database::fetchAll("
            SELECT o.id, o.order_number, o.customer_name, o.delivery_type, 
                   o.notes, o.created_at, o.status
            FROM orders o 
            WHERE o.status = 'RECEBIDO' 
            ORDER BY o.created_at ASC
        ");

        $preparando = Database::fetchAll("
            SELECT o.id, o.order_number, o.customer_name, o.delivery_type, 
                   o.notes, o.created_at, o.prepared_at, o.status
            FROM orders o 
            WHERE o.status = 'PREPARANDO' 
            ORDER BY o.prepared_at ASC
        ");

        $prontos = Database::fetchAll("
            SELECT o.id, o.order_number, o.customer_name, o.delivery_type, 
                   o.notes, o.created_at, o.ready_at, o.status
            FROM orders o 
            WHERE o.status = 'PRONTO' 
            ORDER BY o.ready_at DESC
            LIMIT 10
        ");

        // Get items for each order
        foreach (['recebidos', 'preparando', 'prontos'] as $var) {
            foreach ($$var as &$order) {
                $order['items'] = Database::fetchAll("
                    SELECT product_name, quantity, customizations, notes 
                    FROM order_items 
                    WHERE order_id = ?
                ", [$order['id']]);
            }
        }

        json([
            'success' => true,
            'recebidos' => $recebidos,
            'preparando' => $preparando,
            'prontos' => $prontos,
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Update order status
     */
    public function updateOrderStatus(string $id): void
    {
        if (!auth()) {
            json(['success' => false, 'error' => 'Unauthorized'], 401);
            return;
        }

        // Get raw input for PATCH requests
        $input = json_decode(file_get_contents('php://input'), true);
        $newStatus = $input['status'] ?? $_POST['status'] ?? null;

        $validStatuses = ['RECEBIDO', 'PREPARANDO', 'PRONTO', 'FINALIZADO', 'CANCELADO'];

        if (!$newStatus || !in_array($newStatus, $validStatuses)) {
            json(['success' => false, 'error' => 'Invalid status'], 400);
            return;
        }

        $order = Database::fetch("SELECT * FROM orders WHERE id = ?", [$id]);

        if (!$order) {
            json(['success' => false, 'error' => 'Order not found'], 404);
            return;
        }

        $oldStatus = $order['status'];

        // Prepare update data
        $updateData = [
            'status' => $newStatus,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        // Set timestamps based on status
        if ($newStatus === 'PREPARANDO' && $oldStatus !== 'PREPARANDO') {
            $updateData['prepared_at'] = date('Y-m-d H:i:s');
        } elseif ($newStatus === 'PRONTO' && $oldStatus !== 'PRONTO') {
            $updateData['ready_at'] = date('Y-m-d H:i:s');
        } elseif ($newStatus === 'FINALIZADO' && $oldStatus !== 'FINALIZADO') {
            $updateData['finished_at'] = date('Y-m-d H:i:s');
        }

        Database::update('orders', $updateData, 'id = ?', [$id]);

        // Log the change
        $user = auth();
        Database::insert('audit_logs', [
            'user_id' => $user['id'],
            'action' => 'STATUS_CHANGE',
            'model_type' => 'orders',
            'model_id' => $id,
            'old_values' => json_encode(['status' => $oldStatus]),
            'new_values' => json_encode(['status' => $newStatus]),
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
        ]);

        json([
            'success' => true,
            'order_id' => $id,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
        ]);
    }

    /**
     * Get menu data
     */
    public function menu(): void
    {
        $categories = Database::fetchAll("
            SELECT * FROM categories WHERE active = 1 ORDER BY display_order
        ");

        $products = Database::fetchAll("
            SELECT p.*, c.slug as category_slug
            FROM products p 
            JOIN categories c ON c.id = p.category_id 
            WHERE p.active = 1 
            ORDER BY p.display_order
        ");

        // Get options for buildable products
        $productOptions = [];
        $buildables = Database::fetchAll("SELECT id FROM products WHERE is_buildable = 1 AND active = 1");
        foreach ($buildables as $bp) {
            $productOptions[$bp['id']] = Database::fetchAll("
                SELECT * FROM product_options 
                WHERE product_id = ? AND active = 1 
                ORDER BY option_type, display_order
            ", [$bp['id']]);
        }

        json([
            'success' => true,
            'categories' => $categories,
            'products' => $products,
            'productOptions' => $productOptions,
        ]);
    }

    /**
     * Get order details
     */
    public function orderDetails(string $id): void
    {
        $order = Database::fetch("SELECT * FROM orders WHERE id = ?", [$id]);

        if (!$order) {
            json(['success' => false, 'error' => 'Order not found'], 404);
            return;
        }

        $items = Database::fetchAll("SELECT * FROM order_items WHERE order_id = ?", [$id]);

        json([
            'success' => true,
            'order' => $order,
            'items' => $items,
        ]);
    }
}
