<?php
// Admin Financial Report View
ob_start();
?>

<div class="section-header">
    <div>
        <h1 class="section-title">💰 Financeiro</h1>
        <p class="section-subtitle">Controle de pagamentos</p>
    </div>
</div>

<!-- Summary Cards -->
<div class="stats-grid" style="margin-bottom: var(--spacing-xl);">
    <div class="stat-card">
        <div class="stat-icon" style="background: var(--accent-blue);">📊</div>
        <div class="stat-content">
            <p class="stat-label">Total Geral</p>
            <h3 class="stat-value"><?= money($totalGeral) ?></h3>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: var(--accent-green);">✅</div>
        <div class="stat-content">
            <p class="stat-label">Total Pago</p>
            <h3 class="stat-value" id="total-pago" style="color: var(--accent-green);"><?= money($totalPago) ?></h3>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: var(--accent-red);">⏳</div>
        <div class="stat-content">
            <p class="stat-label">Pendente</p>
            <h3 class="stat-value" id="total-pendente" style="color: var(--accent-red);"><?= money($totalPendente) ?>
            </h3>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: var(--accent-secondary);">📋</div>
        <div class="stat-content">
            <p class="stat-label">Pedidos</p>
            <h3 class="stat-value"><?= count($orders) ?></h3>
        </div>
    </div>
</div>

<!-- Payment Methods Summary -->
<div class="card mb-xl">
    <div class="card-body">
        <h3 class="mb-lg">Por Forma de Pagamento</h3>
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: var(--spacing-md);">
            <div
                style="padding: var(--spacing-md); background: var(--bg-elevated); border-radius: var(--radius-lg); text-align: center;">
                <span style="font-size: 1.5rem;">📱</span>
                <p class="text-secondary text-sm mt-sm">PIX</p>
                <p style="font-weight: 700; color: var(--accent-primary);"><?= money($ordersByPayment['PIX']) ?></p>
            </div>
            <div
                style="padding: var(--spacing-md); background: var(--bg-elevated); border-radius: var(--radius-lg); text-align: center;">
                <span style="font-size: 1.5rem;">💵</span>
                <p class="text-secondary text-sm mt-sm">Dinheiro</p>
                <p style="font-weight: 700; color: var(--accent-primary);"><?= money($ordersByPayment['DINHEIRO']) ?>
                </p>
            </div>
            <div
                style="padding: var(--spacing-md); background: var(--bg-elevated); border-radius: var(--radius-lg); text-align: center;">
                <span style="font-size: 1.5rem;">💳</span>
                <p class="text-secondary text-sm mt-sm">Crédito</p>
                <p style="font-weight: 700; color: var(--accent-primary);">
                    <?= money($ordersByPayment['CARTAO_CREDITO']) ?>
                </p>
            </div>
            <div
                style="padding: var(--spacing-md); background: var(--bg-elevated); border-radius: var(--radius-lg); text-align: center;">
                <span style="font-size: 1.5rem;">💳</span>
                <p class="text-secondary text-sm mt-sm">Débito</p>
                <p style="font-weight: 700; color: var(--accent-primary);">
                    <?= money($ordersByPayment['CARTAO_DEBITO']) ?>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-xl">
    <div class="card-body">
        <form method="GET" style="display: flex; gap: var(--spacing-md); align-items: flex-end; flex-wrap: wrap;">
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Data Início</label>
                <input type="date" name="start" value="<?= $startDate ?>" class="form-input">
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Data Fim</label>
                <input type="date" name="end" value="<?= $endDate ?>" class="form-input">
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Status Pagamento</label>
                <select name="payment_status" class="form-select">
                    <option value="">Todos</option>
                    <option value="PENDENTE" <?= $paymentStatus === 'PENDENTE' ? 'selected' : '' ?>>Pendente</option>
                    <option value="PAGO" <?= $paymentStatus === 'PAGO' ? 'selected' : '' ?>>Pago</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Filtrar</button>
        </form>
    </div>
</div>

<!-- Orders Table -->
<div class="card">
    <div class="card-body">
        <h3 class="mb-lg">Pedidos</h3>

        <?php if (empty($orders)): ?>
            <p class="text-secondary">Nenhum pedido encontrado no período.</p>
        <?php else: ?>
            <div style="overflow-x: auto;">
                <table class="table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Cliente</th>
                            <th>Telefone</th>
                            <th>Data/Hora</th>
                            <th>Pagamento</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr id="order-<?= $order['id'] ?>">
                                <td><strong>#<?= $order['order_number'] ?></strong></td>
                                <td><?= e($order['customer_name']) ?></td>
                                <td><?= e($order['customer_phone']) ?></td>
                                <td><?= date('d/m H:i', strtotime($order['created_at'])) ?></td>
                                <td>
                                    <?php
                                    $paymentIcons = [
                                        'PIX' => '📱',
                                        'DINHEIRO' => '💵',
                                        'CARTAO_CREDITO' => '💳',
                                        'CARTAO_DEBITO' => '💳',
                                    ];
                                    echo ($paymentIcons[$order['payment_method'] ?? 'DINHEIRO'] ?? '💵') . ' ' . ($order['payment_method'] ?? 'Dinheiro');
                                    ?>
                                </td>
                                <td><strong><?= money($order['total']) ?></strong></td>
                                <td>
                                    <span
                                        class="payment-badge payment-<?= strtolower($order['payment_status'] ?? 'pendente') ?>"
                                        id="status-<?= $order['id'] ?>">
                                        <?= ($order['payment_status'] ?? 'PENDENTE') === 'PAGO' ? '✅ PAGO' : '⏳ PENDENTE' ?>
                                    </span>
                                </td>
                                <td>
                                    <button onclick="togglePayment(<?= $order['id'] ?>)"
                                        class="btn btn-sm <?= ($order['payment_status'] ?? 'PENDENTE') === 'PAGO' ? 'btn-secondary' : 'btn-success' ?>"
                                        id="btn-<?= $order['id'] ?>">
                                        <?= ($order['payment_status'] ?? 'PENDENTE') === 'PAGO' ? 'Estornar' : 'Marcar Pago' ?>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: var(--spacing-md);
    }

    .stat-card {
        background: var(--bg-card);
        border-radius: var(--radius-lg);
        padding: var(--spacing-lg);
        display: flex;
        align-items: center;
        gap: var(--spacing-md);
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: var(--radius-lg);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .stat-label {
        color: var(--text-secondary);
        font-size: 0.875rem;
    }

    .stat-value {
        font-size: 1.5rem;
        margin-top: var(--spacing-xs);
    }

    .table {
        border-collapse: collapse;
    }

    .table th,
    .table td {
        padding: var(--spacing-md);
        text-align: left;
        border-bottom: 1px solid var(--border-color);
    }

    .table th {
        color: var(--text-secondary);
        font-weight: 600;
        font-size: 0.875rem;
        text-transform: uppercase;
    }

    .table tbody tr:hover {
        background: var(--bg-elevated);
    }

    .payment-badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: var(--radius-full);
        font-size: 0.75rem;
        font-weight: 600;
    }

    .payment-pago {
        background: rgba(34, 197, 94, 0.2);
        color: var(--accent-green);
    }

    .payment-pendente {
        background: rgba(239, 68, 68, 0.2);
        color: var(--accent-red);
    }

    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
</style>

<script>
    async function togglePayment(orderId) {
        const btn = document.getElementById('btn-' + orderId);
        const originalText = btn.textContent;
        btn.disabled = true;
        btn.textContent = 'Aguarde...';

        try {
            const formData = new FormData();
            formData.append('order_id', orderId);
            formData.append('_token', '<?= csrf_token() ?>');

            const res = await fetch('<?= url('admin/financeiro/toggle') ?>', {
                method: 'POST',
                body: formData
            });

            const text = await res.text();
            console.log('Response:', text);

            let data;
            try {
                data = JSON.parse(text);
            } catch (parseErr) {
                alert('Erro: Resposta inválida do servidor:\n' + text.substring(0, 500));
                btn.textContent = originalText;
                btn.disabled = false;
                return;
            }

            if (data.success) {
                const statusEl = document.getElementById('status-' + orderId);

                // Get the order total from the table row
                const orderRow = document.getElementById('order-' + orderId);
                const totalCell = orderRow.querySelector('td:nth-child(6) strong');
                const orderTotal = parseFloat(totalCell.textContent.replace('R$ ', '').replace('.', '').replace(',', '.'));

                // Update totals
                const totalPagoEl = document.getElementById('total-pago');
                const totalPendenteEl = document.getElementById('total-pendente');

                let totalPago = parseFloat(totalPagoEl.textContent.replace('R$ ', '').replace('.', '').replace(',', '.'));
                let totalPendente = parseFloat(totalPendenteEl.textContent.replace('R$ ', '').replace('.', '').replace(',', '.'));

                if (data.newStatus === 'PAGO') {
                    statusEl.textContent = '✅ PAGO';
                    statusEl.className = 'payment-badge payment-pago';
                    btn.textContent = 'Estornar';
                    btn.className = 'btn btn-sm btn-secondary';
                    totalPago += orderTotal;
                    totalPendente -= orderTotal;
                } else {
                    statusEl.textContent = '⏳ PENDENTE';
                    statusEl.className = 'payment-badge payment-pendente';
                    btn.textContent = 'Marcar Pago';
                    btn.className = 'btn btn-sm btn-success';
                    totalPago -= orderTotal;
                    totalPendente += orderTotal;
                }

                // Format and update display
                totalPagoEl.textContent = 'R$ ' + totalPago.toFixed(2).replace('.', ',');
                totalPendenteEl.textContent = 'R$ ' + totalPendente.toFixed(2).replace('.', ',');
            } else {
                alert('Erro: ' + data.message);
                btn.textContent = originalText;
            }
        } catch (err) {
            console.error('Fetch error:', err);
            alert('Erro de conexão: ' + err.message);
            btn.textContent = originalText;
        }

        btn.disabled = false;
    }
</script>

<?php
$content = ob_get_clean();
$title = 'Financeiro';
require VIEWS_PATH . '/layouts/admin.php';
?>