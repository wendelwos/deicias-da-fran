<?php
/**
 * Kitchen Controller - KDS (Kitchen Display System)
 */

class KitchenController
{

    public function __construct()
    {
        // Check authentication
        if (!auth()) {
            redirect('login');
            return;
        }
    }

    public function index(): void
    {
        // Get orders grouped by status
        $recebidos = Database::fetchAll("
            SELECT o.*, 
                   (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as item_count
            FROM orders o 
            WHERE o.status = 'RECEBIDO' 
            ORDER BY o.created_at ASC
        ");

        $preparando = Database::fetchAll("
            SELECT o.*, 
                   (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as item_count
            FROM orders o 
            WHERE o.status = 'PREPARANDO' 
            ORDER BY o.prepared_at ASC
        ");

        $prontos = Database::fetchAll("
            SELECT o.*, 
                   (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as item_count
            FROM orders o 
            WHERE o.status = 'PRONTO' 
            ORDER BY o.ready_at DESC
        ");

        // Get items for each order
        foreach (['recebidos', 'preparando', 'prontos'] as $var) {
            foreach ($$var as &$order) {
                $order['items'] = Database::fetchAll("
                    SELECT * FROM order_items WHERE order_id = ?
                ", [$order['id']]);
            }
        }

        view('admin/cozinha', [
            'recebidos' => $recebidos,
            'preparando' => $preparando,
            'prontos' => $prontos,
        ]);
    }
}
