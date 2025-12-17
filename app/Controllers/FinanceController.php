<?php
/**
 * Financial Admin Controller
 * Manage payment status and view financial reports
 */

class FinanceController
{
    public function __construct()
    {
        if (!auth()) {
            redirect('login');
            exit;
        }

        if (!hasRole('admin')) {
            http_response_code(403);
            echo "Acesso negado";
            exit;
        }
    }

    /**
     * Financial report page
     */
    public function index(): void
    {
        // Get date filter
        $startDate = $_GET['start'] ?? date('Y-m-01'); // First of month
        $endDate = $_GET['end'] ?? date('Y-m-d');
        $paymentStatus = $_GET['payment_status'] ?? '';

        // Build query
        $query = "SELECT * FROM orders WHERE DATE(created_at) BETWEEN ? AND ?";
        $params = [$startDate, $endDate];

        if ($paymentStatus) {
            $query .= " AND payment_status = ?";
            $params[] = $paymentStatus;
        }

        $query .= " ORDER BY created_at DESC";

        $orders = Database::fetchAll($query, $params);

        // Calculate totals
        $totalGeral = 0;
        $totalPago = 0;
        $totalPendente = 0;
        $ordersByPayment = ['PIX' => 0, 'DINHEIRO' => 0, 'CARTAO_CREDITO' => 0, 'CARTAO_DEBITO' => 0];

        foreach ($orders as $order) {
            $totalGeral += $order['total'];

            if ($order['payment_status'] === 'PAGO') {
                $totalPago += $order['total'];
            } else {
                $totalPendente += $order['total'];
            }

            $method = $order['payment_method'] ?? 'DINHEIRO';
            if (isset($ordersByPayment[$method])) {
                $ordersByPayment[$method] += $order['total'];
            }
        }

        view('admin/financeiro', [
            'orders' => $orders,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'paymentStatus' => $paymentStatus,
            'totalGeral' => $totalGeral,
            'totalPago' => $totalPago,
            'totalPendente' => $totalPendente,
            'ordersByPayment' => $ordersByPayment,
        ]);
    }

    /**
     * Toggle payment status (AJAX)
     */
    public function togglePayment(): void
    {
        header('Content-Type: application/json');

        $orderId = $_POST['order_id'] ?? null;

        if (!$orderId) {
            echo json_encode(['success' => false, 'message' => 'ID do pedido não informado']);
            return;
        }

        $order = Database::fetch("SELECT * FROM orders WHERE id = ?", [$orderId]);

        if (!$order) {
            echo json_encode(['success' => false, 'message' => 'Pedido não encontrado']);
            return;
        }

        $newStatus = $order['payment_status'] === 'PAGO' ? 'PENDENTE' : 'PAGO';
        $paidAt = $newStatus === 'PAGO' ? date('Y-m-d H:i:s') : null;

        try {
            Database::update('orders', [
                'payment_status' => $newStatus,
                'paid_at' => $paidAt,
            ], 'id = ?', [$orderId]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Erro ao atualizar: ' . $e->getMessage()]);
            return;
        }

        echo json_encode([
            'success' => true,
            'newStatus' => $newStatus,
            'message' => $newStatus === 'PAGO' ? 'Marcado como PAGO!' : 'Marcado como PENDENTE',
        ]);
    }
}
