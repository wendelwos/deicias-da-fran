<?php
// Admin Order Details Page View
ob_start();
?>

<div>
    <div style="margin-bottom: var(--spacing-xl);">
        <a href="<?= url('admin/pedidos') ?>" class="btn btn-secondary btn-sm mb-md">
            ← Voltar aos Pedidos
        </a>
        <h1>Pedido #<?= $order['order_number'] ?></h1>
    </div>
    
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: var(--spacing-xl);">
        <!-- Main Info -->
        <div>
            <!-- Order Details -->
            <div class="card mb-xl">
                <div class="card-body">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: var(--spacing-lg);">
                        <div>
                            <h2 style="color: var(--accent-primary); margin-bottom: var(--spacing-sm);">#<?= $order['order_number'] ?></h2>
                            <p class="text-secondary"><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></p>
                        </div>
                        <span class="badge badge-<?= strtolower($order['status']) ?>" style="font-size: 1rem; padding: 0.5rem 1rem;">
                            <?= statusText($order['status']) ?>
                        </span>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: var(--spacing-lg); margin-bottom: var(--spacing-lg);">
                        <div>
                            <p class="text-secondary text-sm">Cliente</p>
                            <p style="font-weight: 600; font-size: 1.125rem;"><?= e($order['customer_name']) ?></p>
                        </div>
                        <div>
                            <p class="text-secondary text-sm">Telefone</p>
                            <p style="font-weight: 600; font-size: 1.125rem;">
                                <a href="tel:<?= e($order['customer_phone']) ?>"><?= e($order['customer_phone']) ?></a>
                            </p>
                        </div>
                        <div>
                            <p class="text-secondary text-sm">Tipo</p>
                            <p style="font-weight: 600; font-size: 1.125rem;">
                                <?= $order['delivery_type'] === 'ENTREGA' ? '🛵 Entrega' : '🏪 Retirada' ?>
                            </p>
                        </div>
                        <div>
                            <p class="text-secondary text-sm">Total</p>
                            <p style="font-weight: 700; font-size: 1.5rem; color: var(--accent-primary);">
                                <?= money($order['total']) ?>
                            </p>
                        </div>
                    </div>
                    
                    <?php if ($order['delivery_type'] === 'ENTREGA' && $order['address']): ?>
                    <div style="margin-bottom: var(--spacing-lg);">
                        <p class="text-secondary text-sm">Endereço de Entrega</p>
                        <p style="font-weight: 600;"><?= e($order['address']) ?></p>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($order['notes']): ?>
                    <div style="background: rgba(255, 159, 28, 0.1); padding: var(--spacing-md); border-radius: var(--radius-lg);">
                        <p class="text-secondary text-sm">Observações</p>
                        <p><?= e($order['notes']) ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Items -->
            <div class="card">
                <div class="card-body">
                    <h3 class="mb-lg">Itens do Pedido</h3>
                    
                    <?php foreach ($items as $item): ?>
                    <div style="display: flex; justify-content: space-between; padding: var(--spacing-md) 0; border-bottom: 1px solid var(--border-color);">
                        <div>
                            <span style="font-weight: 700; color: var(--accent-primary);"><?= $item['quantity'] ?>x</span>
                            <span style="font-weight: 600;"><?= e($item['product_name']) ?></span>
                            <?php if ($item['customizations']): ?>
                                <?php $customs = json_decode($item['customizations'], true); ?>
                                <?php if ($customs): ?>
                                <div style="font-size: 0.875rem; color: var(--text-muted); margin-top: 4px;">
                                    <?php if (isset($customs['massa'])): ?>
                                        Massa: <?= e($customs['massa']) ?><br>
                                    <?php endif; ?>
                                    <?php if (isset($customs['ingredientes']) && $customs['ingredientes']): ?>
                                        Ingredientes: <?= e(implode(', ', $customs['ingredientes'])) ?><br>
                                    <?php endif; ?>
                                    <?php if (isset($customs['temperos']) && $customs['temperos']): ?>
                                        Temperos: <?= e(implode(', ', $customs['temperos'])) ?>
                                    <?php endif; ?>
                                </div>
                                <?php endif; ?>
                            <?php endif; ?>
                            <?php if ($item['notes']): ?>
                            <div style="font-size: 0.875rem; color: var(--accent-secondary); margin-top: 4px;">
                                📝 <?= e($item['notes']) ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div style="text-align: right;">
                            <p style="color: var(--text-muted);"><?= money($item['unit_price']) ?> cada</p>
                            <p style="font-weight: 600;"><?= money($item['subtotal']) ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    
                    <div style="padding-top: var(--spacing-lg);">
                        <div style="display: flex; justify-content: space-between; margin-bottom: var(--spacing-sm);">
                            <span class="text-secondary">Subtotal</span>
                            <span><?= money($order['subtotal']) ?></span>
                        </div>
                        <?php if ($order['delivery_fee'] > 0): ?>
                        <div style="display: flex; justify-content: space-between; margin-bottom: var(--spacing-sm);">
                            <span class="text-secondary">Taxa de Entrega</span>
                            <span><?= money($order['delivery_fee']) ?></span>
                        </div>
                        <?php endif; ?>
                        <div style="display: flex; justify-content: space-between; font-size: 1.25rem; font-weight: 700; padding-top: var(--spacing-md); border-top: 2px solid var(--border-color);">
                            <span>Total</span>
                            <span class="text-accent"><?= money($order['total']) ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div>
            <!-- Actions -->
            <div class="card mb-xl" x-data="{ updating: false }">
                <div class="card-body">
                    <h4 class="mb-lg">Ações</h4>
                    
                    <?php if ($order['status'] !== 'FINALIZADO' && $order['status'] !== 'CANCELADO'): ?>
                    <form method="POST" action="<?= url('api/orders/' . $order['id'] . '/status') ?>" 
                          @submit.prevent="updateStatus($el)"
                          style="display: flex; flex-direction: column; gap: var(--spacing-sm);">
                        
                        <?php if ($order['status'] === 'RECEBIDO'): ?>
                        <button type="button" @click="changeStatus(<?= $order['id'] ?>, 'PREPARANDO')" class="btn btn-secondary btn-block" :disabled="updating">
                            👨‍🍳 Iniciar Preparo
                        </button>
                        <?php endif; ?>
                        
                        <?php if ($order['status'] === 'PREPARANDO'): ?>
                        <button type="button" @click="changeStatus(<?= $order['id'] ?>, 'PRONTO')" class="btn btn-success btn-block" :disabled="updating">
                            ✅ Marcar como Pronto
                        </button>
                        <?php endif; ?>
                        
                        <?php if ($order['status'] === 'PRONTO'): ?>
                        <button type="button" @click="changeStatus(<?= $order['id'] ?>, 'FINALIZADO')" class="btn btn-primary btn-block" :disabled="updating">
                            ✓ Finalizar Pedido
                        </button>
                        <?php endif; ?>
                        
                        <button type="button" @click="changeStatus(<?= $order['id'] ?>, 'CANCELADO')" class="btn btn-danger btn-block" :disabled="updating">
                            ✕ Cancelar Pedido
                        </button>
                    </form>
                    <?php else: ?>
                    <p class="text-secondary text-center">Este pedido foi <?= $order['status'] === 'FINALIZADO' ? 'finalizado' : 'cancelado' ?>.</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Timeline -->
            <div class="card">
                <div class="card-body">
                    <h4 class="mb-lg">Histórico</h4>
                    
                    <div style="display: flex; flex-direction: column; gap: var(--spacing-md);">
                        <div style="display: flex; gap: var(--spacing-md); align-items: flex-start;">
                            <div style="width: 10px; height: 10px; border-radius: 50%; background: var(--accent-blue); margin-top: 6px;"></div>
                            <div>
                                <p style="font-weight: 600;">Pedido criado</p>
                                <p class="text-muted text-sm"><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></p>
                            </div>
                        </div>
                        
                        <?php if ($order['prepared_at']): ?>
                        <div style="display: flex; gap: var(--spacing-md); align-items: flex-start;">
                            <div style="width: 10px; height: 10px; border-radius: 50%; background: var(--accent-secondary); margin-top: 6px;"></div>
                            <div>
                                <p style="font-weight: 600;">Início do preparo</p>
                                <p class="text-muted text-sm"><?= date('d/m/Y H:i', strtotime($order['prepared_at'])) ?></p>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($order['ready_at']): ?>
                        <div style="display: flex; gap: var(--spacing-md); align-items: flex-start;">
                            <div style="width: 10px; height: 10px; border-radius: 50%; background: var(--accent-green); margin-top: 6px;"></div>
                            <div>
                                <p style="font-weight: 600;">Pedido pronto</p>
                                <p class="text-muted text-sm"><?= date('d/m/Y H:i', strtotime($order['ready_at'])) ?></p>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($order['finished_at']): ?>
                        <div style="display: flex; gap: var(--spacing-md); align-items: flex-start;">
                            <div style="width: 10px; height: 10px; border-radius: 50%; background: var(--text-muted); margin-top: 6px;"></div>
                            <div>
                                <p style="font-weight: 600;">Pedido finalizado</p>
                                <p class="text-muted text-sm"><?= date('d/m/Y H:i', strtotime($order['finished_at'])) ?></p>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script>
async function changeStatus(orderId, newStatus) {
    if (!confirm('Tem certeza que deseja alterar o status?')) return;
    
    try {
        const res = await fetch('<?= url('api/orders/') ?>' + orderId + '/status', {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?= csrf_token() ?>'
            },
            body: JSON.stringify({ status: newStatus })
        });
        
        const data = await res.json();
        
        if (data.success) {
            window.location.reload();
        } else {
            alert('Erro: ' + (data.error || 'Unknown error'));
        }
    } catch (e) {
        console.error(e);
        alert('Erro ao atualizar status');
    }
}
</script>

<style>
.text-sm { font-size: 0.875rem; }
</style>

<?php
$content = ob_get_clean();
$title = 'Pedido #' . $order['order_number'];
require VIEWS_PATH . '/layouts/admin.php';
?>
