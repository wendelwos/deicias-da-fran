<?php
// Admin Orders Page View
ob_start();
?>

<div>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--spacing-xl);">
        <div>
            <h1>📋 Pedidos</h1>
            <p class="text-secondary">Gerencie todos os pedidos do dia</p>
        </div>
        <a href="<?= url('cozinha') ?>" class="btn btn-primary">
            👨‍🍳 Ir para Cozinha
        </a>
    </div>

    <!-- Stats Cards -->
    <div
        style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: var(--spacing-lg); margin-bottom: var(--spacing-xl);">
        <div class="card">
            <div class="card-body text-center">
                <div style="font-size: 2.5rem; font-weight: 800; color: var(--accent-primary);">
                    <?= $stats['total_orders'] ?? 0 ?>
                </div>
                <p class="text-secondary">Pedidos Hoje</p>
            </div>
        </div>
        <div class="card">
            <div class="card-body text-center">
                <div style="font-size: 2.5rem; font-weight: 800; color: var(--accent-green);">
                    <?= money($stats['total_revenue'] ?? 0) ?>
                </div>
                <p class="text-secondary">Faturamento</p>
            </div>
        </div>
        <div class="card">
            <div class="card-body text-center">
                <div style="font-size: 2.5rem; font-weight: 800; color: var(--accent-blue);">
                    <?= $stats['completed'] ?? 0 ?>
                </div>
                <p class="text-secondary">Finalizados</p>
            </div>
        </div>
        <div class="card">
            <div class="card-body text-center">
                <div style="font-size: 2.5rem; font-weight: 800; color: var(--accent-red);">
                    <?= $stats['cancelled'] ?? 0 ?>
                </div>
                <p class="text-secondary">Cancelados</p>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-xl">
        <div class="card-body">
            <form method="GET" action="<?= url('admin/pedidos') ?>"
                style="display: flex; gap: var(--spacing-md); flex-wrap: wrap; align-items: flex-end;">
                <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 150px;">
                    <label class="form-label">Data</label>
                    <input type="date" name="date" value="<?= $currentDate ?>" class="form-input">
                </div>
                <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 150px;">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Todos</option>
                        <option value="RECEBIDO" <?= $currentStatus === 'RECEBIDO' ? 'selected' : '' ?>>Recebido</option>
                        <option value="PREPARANDO" <?= $currentStatus === 'PREPARANDO' ? 'selected' : '' ?>>Em Preparo
                        </option>
                        <option value="PRONTO" <?= $currentStatus === 'PRONTO' ? 'selected' : '' ?>>Pronto</option>
                        <option value="FINALIZADO" <?= $currentStatus === 'FINALIZADO' ? 'selected' : '' ?>>Finalizado
                        </option>
                        <option value="CANCELADO" <?= $currentStatus === 'CANCELADO' ? 'selected' : '' ?>>Cancelado
                        </option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Filtrar</button>
            </form>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="card">
        <div class="card-body">
            <?php if (empty($orders)): ?>
                <div class="empty-state">
                    <div class="empty-state-icon">📭</div>
                    <p>Nenhum pedido encontrado</p>
                </div>
            <?php else: ?>
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid var(--border-color);">
                            <th style="text-align: left; padding: var(--spacing-md);">Pedido</th>
                            <th style="text-align: left; padding: var(--spacing-md);">Cliente</th>
                            <th style="text-align: left; padding: var(--spacing-md);">Tipo</th>
                            <th style="text-align: left; padding: var(--spacing-md);">Itens</th>
                            <th style="text-align: left; padding: var(--spacing-md);">Total</th>
                            <th style="text-align: left; padding: var(--spacing-md);">Status</th>
                            <th style="text-align: left; padding: var(--spacing-md);">Horário</th>
                            <th style="text-align: center; padding: var(--spacing-md);">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr style="border-bottom: 1px solid var(--border-color);">
                                <td style="padding: var(--spacing-md);">
                                    <span
                                        style="font-weight: 700; color: var(--accent-primary);">#<?= $order['order_number'] ?></span>
                                </td>
                                <td style="padding: var(--spacing-md);">
                                    <div style="font-weight: 600;"><?= e($order['customer_name']) ?></div>
                                    <div style="font-size: 0.75rem; color: var(--text-muted);">
                                        <?= e($order['customer_phone']) ?></div>
                                </td>
                                <td style="padding: var(--spacing-md);">
                                    <?= $order['delivery_type'] === 'ENTREGA' ? '🛵 Entrega' : '🏪 Retirada' ?>
                                </td>
                                <td style="padding: var(--spacing-md);">
                                    <span class="text-secondary"><?= $order['item_count'] ?> item(s)</span>
                                </td>
                                <td style="padding: var(--spacing-md); font-weight: 600;">
                                    <?= money($order['total']) ?>
                                </td>
                                <td style="padding: var(--spacing-md);">
                                    <span class="badge badge-<?= strtolower($order['status']) ?>">
                                        <?= statusText($order['status']) ?>
                                    </span>
                                </td>
                                <td style="padding: var(--spacing-md); color: var(--text-muted);">
                                    <?= date('H:i', strtotime($order['created_at'])) ?>
                                </td>
                                <td style="padding: var(--spacing-md); text-align: center;">
                                    <a href="<?= url('admin/pedidos/' . $order['id']) ?>" class="btn btn-sm btn-secondary">
                                        Ver
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
$title = 'Pedidos';
require VIEWS_PATH . '/layouts/admin.php';
?>