<?php
/**
 * Admin Controller
 */

class AdminController
{

    public function __construct()
    {
        // Check authentication
        if (!auth()) {
            redirect('login');
            return;
        }
    }

    public function orders(): void
    {
        $status = $_GET['status'] ?? null;
        $date = $_GET['date'] ?? date('Y-m-d');

        $where = "DATE(o.created_at) = ?";
        $params = [$date];

        if ($status) {
            $where .= " AND o.status = ?";
            $params[] = $status;
        }

        $orders = Database::fetchAll("
            SELECT o.*, 
                   (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as item_count
            FROM orders o 
            WHERE $where 
            ORDER BY o.created_at DESC
        ", $params);

        // Get items for each order
        foreach ($orders as &$order) {
            $order['items'] = Database::fetchAll("
                SELECT * FROM order_items WHERE order_id = ?
            ", [$order['id']]);
        }

        // Stats for the day
        $stats = Database::fetch("
            SELECT 
                COUNT(*) as total_orders,
                SUM(total) as total_revenue,
                SUM(CASE WHEN status = 'FINALIZADO' THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN status = 'CANCELADO' THEN 1 ELSE 0 END) as cancelled
            FROM orders 
            WHERE DATE(created_at) = ?
        ", [$date]);

        view('admin/pedidos', [
            'orders' => $orders,
            'stats' => $stats,
            'currentStatus' => $status,
            'currentDate' => $date,
        ]);
    }

    public function orderDetails(string $id): void
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

        $logs = Database::fetchAll("
            SELECT al.*, u.name as user_name 
            FROM audit_logs al 
            LEFT JOIN users u ON u.id = al.user_id 
            WHERE al.model_type = 'orders' AND al.model_id = ? 
            ORDER BY al.created_at DESC
        ", [$id]);

        view('admin/pedido-detalhes', [
            'order' => $order,
            'items' => $items,
            'logs' => $logs,
        ]);
    }
}
