<?php
// Kitchen KDS (Kitchen Display System) View
ob_start();
?>

<div x-data="kitchenKDS()" x-init="init()">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--spacing-xl);">
        <div>
            <h1>👨‍🍳 Cozinha (KDS)</h1>
            <p class="text-secondary">Gerencie os pedidos em tempo real</p>
        </div>
        <div style="display: flex; align-items: center; gap: var(--spacing-md);">
            <div style="display: flex; align-items: center; gap: var(--spacing-sm);">
                <div
                    style="width: 8px; height: 8px; background: var(--accent-green); border-radius: 50%; animation: blink 1s infinite;">
                </div>
                <span class="text-secondary text-sm">Atualização automática</span>
            </div>
            <a href="<?= url('ready') ?>" target="_blank" class="btn btn-secondary btn-sm">
                📺 Abrir Tela Prontos
            </a>
        </div>
    </div>

    <!-- KDS Kanban Board -->
    <div class="kds-container">
        <!-- Column: Recebidos -->
        <div class="kds-column recebidos">
            <div class="kds-column-header">
                <div class="kds-column-title">
                    📥 Recebidos
                </div>
                <span class="kds-column-count" x-text="recebidos.length"></span>
            </div>
            <div class="kds-orders">
                <template x-for="order in recebidos" :key="order.id">
                    <div class="kds-order-card">
                        <div class="kds-order-header">
                            <div>
                                <div class="kds-order-number" x-text="'#' + order.order_number"></div>
                                <div class="kds-order-name" x-text="order.customer_name"></div>
                            </div>
                            <div class="kds-order-time">
                                ⏱️ <span x-text="timeAgo(order.created_at)"></span>
                            </div>
                        </div>

                        <div class="kds-order-items">
                            <template x-for="item in order.items" :key="item.product_name">
                                <div class="kds-order-item">
                                    <span class="kds-order-item-qty" x-text="item.quantity + 'x'"></span>
                                    <div>
                                        <div class="kds-order-item-name" x-text="item.product_name"></div>
                                        <template x-if="item.customizations">
                                            <div class="kds-order-item-customs"
                                                x-text="formatCustomizations(item.customizations)"></div>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <template x-if="order.notes">
                            <div class="kds-order-notes" x-text="'📝 ' + order.notes"></div>
                        </template>

                        <div class="kds-order-actions">
                            <button @click="updateStatus(order.id, 'PREPARANDO')"
                                class="btn btn-secondary btn-sm btn-block">
                                👨‍🍳 Iniciar Preparo
                            </button>
                        </div>
                    </div>
                </template>

                <template x-if="recebidos.length === 0">
                    <div class="empty-state">
                        <div class="empty-state-icon">📭</div>
                        <p>Nenhum pedido novo</p>
                    </div>
                </template>
            </div>
        </div>

        <!-- Column: Preparando -->
        <div class="kds-column preparando">
            <div class="kds-column-header">
                <div class="kds-column-title">
                    🔥 Em Preparo
                </div>
                <span class="kds-column-count" x-text="preparando.length"></span>
            </div>
            <div class="kds-orders">
                <template x-for="order in preparando" :key="order.id">
                    <div class="kds-order-card preparando">
                        <div class="kds-order-header">
                            <div>
                                <div class="kds-order-number" x-text="'#' + order.order_number"></div>
                                <div class="kds-order-name" x-text="order.customer_name"></div>
                            </div>
                            <div class="kds-order-time">
                                ⏱️ <span x-text="timeAgo(order.prepared_at || order.created_at)"></span>
                            </div>
                        </div>

                        <div class="kds-order-items">
                            <template x-for="item in order.items" :key="item.product_name">
                                <div class="kds-order-item">
                                    <span class="kds-order-item-qty" x-text="item.quantity + 'x'"></span>
                                    <div>
                                        <div class="kds-order-item-name" x-text="item.product_name"></div>
                                        <template x-if="item.customizations">
                                            <div class="kds-order-item-customs"
                                                x-text="formatCustomizations(item.customizations)"></div>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <template x-if="order.notes">
                            <div class="kds-order-notes" x-text="'📝 ' + order.notes"></div>
                        </template>

                        <div class="kds-order-actions">
                            <button @click="updateStatus(order.id, 'PRONTO')" class="btn btn-success btn-sm btn-block">
                                ✅ Marcar como Pronto
                            </button>
                        </div>
                    </div>
                </template>

                <template x-if="preparando.length === 0">
                    <div class="empty-state">
                        <div class="empty-state-icon">🍳</div>
                        <p>Nenhum pedido em preparo</p>
                    </div>
                </template>
            </div>
        </div>

        <!-- Column: Prontos -->
        <div class="kds-column prontos">
            <div class="kds-column-header">
                <div class="kds-column-title">
                    ✅ Prontos
                </div>
                <span class="kds-column-count" x-text="prontos.length"></span>
            </div>
            <div class="kds-orders">
                <template x-for="order in prontos" :key="order.id">
                    <div class="kds-order-card pronto">
                        <div class="kds-order-header">
                            <div>
                                <div class="kds-order-number" x-text="'#' + order.order_number"></div>
                                <div class="kds-order-name" x-text="order.customer_name"></div>
                            </div>
                            <div class="badge badge-pronto">
                                PRONTO
                            </div>
                        </div>

                        <div style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: var(--spacing-md);">
                            <span x-text="order.delivery_type === 'ENTREGA' ? '🛵 Entrega' : '🏪 Retirada'"></span>
                        </div>

                        <div class="kds-order-actions" style="flex-direction: column;">
                            <button @click="updateStatus(order.id, 'FINALIZADO')"
                                class="btn btn-secondary btn-sm btn-block">
                                ✓ Finalizar
                            </button>
                        </div>
                    </div>
                </template>

                <template x-if="prontos.length === 0">
                    <div class="empty-state">
                        <div class="empty-state-icon">✨</div>
                        <p>Nenhum pedido pronto</p>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes blink {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: 0.4;
        }
    }

    .text-sm {
        font-size: 0.875rem;
    }
</style>

<script>
    function kitchenKDS() {
        return {
            recebidos: [],
            preparando: [],
            prontos: [],

            init() {
                this.fetchOrders();
                // Poll every 2 seconds
                setInterval(() => this.fetchOrders(), 2000);
            },

            async fetchOrders() {
                try {
                    const res = await fetch('<?= url('api/kitchen/orders') ?>');
                    const data = await res.json();

                    if (data.success) {
                        this.recebidos = data.recebidos || [];
                        this.preparando = data.preparando || [];
                        this.prontos = data.prontos || [];
                    }
                } catch (e) {
                    console.error('Failed to fetch orders:', e);
                }
            },

            async updateStatus(orderId, newStatus) {
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
                        // Refresh orders
                        this.fetchOrders();
                    } else {
                        alert('Erro ao atualizar status: ' + (data.error || 'Unknown error'));
                    }
                } catch (e) {
                    console.error('Failed to update status:', e);
                    alert('Erro ao atualizar status. Tente novamente.');
                }
            },

            timeAgo(datetime) {
                if (!datetime) return '-';

                const now = new Date();
                const then = new Date(datetime);
                const diffMs = now - then;
                const diffMins = Math.floor(diffMs / 60000);

                if (diffMins < 1) return 'agora';
                if (diffMins < 60) return diffMins + ' min';

                const diffHours = Math.floor(diffMins / 60);
                return diffHours + 'h ' + (diffMins % 60) + 'm';
            },

            formatCustomizations(customizations) {
                if (!customizations) return '';

                try {
                    const data = typeof customizations === 'string'
                        ? JSON.parse(customizations)
                        : customizations;

                    const parts = [];
                    if (data.massa) parts.push('Massa: ' + data.massa);
                    if (data.ingredientes?.length) parts.push('Ing: ' + data.ingredientes.slice(0, 3).join(', ') + (data.ingredientes.length > 3 ? '...' : ''));
                    if (data.temperos?.length) parts.push('Temp: ' + data.temperos.join(', '));

                    return parts.join(' | ');
                } catch (e) {
                    return '';
                }
            }
        }
    }
</script>

<?php
$content = ob_get_clean();
$title = 'Cozinha (KDS)';
require VIEWS_PATH . '/layouts/admin.php';
?>