<?php
// Order Status Page View
ob_start();
?>

<section class="section" style="padding-top: var(--spacing-lg);">
    <div class="container container-sm">

        <!-- Success Message -->
        <div class="card mb-xl"
            style="background: linear-gradient(135deg, rgba(34, 197, 94, 0.2), rgba(34, 197, 94, 0.05)); border-color: var(--accent-green);">
            <div class="card-body text-center">
                <div style="font-size: 4rem; margin-bottom: var(--spacing-md);">✅</div>
                <h1 style="color: var(--accent-green); margin-bottom: var(--spacing-sm);">Pedido Confirmado!</h1>
                <p class="text-secondary">Seu pedido foi recebido e está sendo preparado.</p>
            </div>
        </div>

        <!-- Order Details -->
        <div class="card mb-xl">
            <div class="card-body">
                <div
                    style="display: flex; align-items: center; justify-content: space-between; margin-bottom: var(--spacing-lg);">
                    <div>
                        <p class="text-secondary">Número do Pedido</p>
                        <h2 style="font-size: 2.5rem; color: var(--accent-primary);">#<?= $order['order_number'] ?></h2>
                    </div>
                    <div class="badge badge-<?= strtolower($order['status']) ?>">
                        <?= statusText($order['status']) ?>
                    </div>
                </div>

                <div
                    style="display: grid; grid-template-columns: repeat(2, 1fr); gap: var(--spacing-md); margin-bottom: var(--spacing-lg);">
                    <div>
                        <p class="text-secondary text-sm">Cliente</p>
                        <p style="font-weight: 600;"><?= e($order['customer_name']) ?></p>
                    </div>
                    <div>
                        <p class="text-secondary text-sm">Telefone</p>
                        <p style="font-weight: 600;"><?= e($order['customer_phone']) ?></p>
                    </div>
                    <div>
                        <p class="text-secondary text-sm">Tipo</p>
                        <p style="font-weight: 600;">
                            <?= $order['delivery_type'] === 'ENTREGA' ? '🛵 Entrega' : '🏪 Retirada' ?>
                        </p>
                    </div>
                    <div>
                        <p class="text-secondary text-sm">Horário</p>
                        <p style="font-weight: 600;"><?= date('H:i', strtotime($order['created_at'])) ?></p>
                    </div>
                    <div>
                        <p class="text-secondary text-sm">Pagamento</p>
                        <p style="font-weight: 600;">
                            <?php
                            $paymentIcons = [
                                'PIX' => '📱 PIX',
                                'DINHEIRO' => '💵 Dinheiro',
                                'CARTAO_CREDITO' => '💳 Crédito',
                                'CARTAO_DEBITO' => '💳 Débito',
                            ];
                            echo $paymentIcons[$order['payment_method'] ?? 'DINHEIRO'] ?? ($order['payment_method'] ?? 'Dinheiro');
                            ?>
                        </p>
                    </div>
                    <?php if ($order['payment_method'] === 'DINHEIRO' && !empty($order['change_for'])): ?>
                        <div>
                            <p class="text-secondary text-sm">Troco para</p>
                            <p style="font-weight: 600;"><?= money($order['change_for']) ?></p>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if ($order['delivery_type'] === 'ENTREGA' && $order['address']): ?>
                    <div style="margin-bottom: var(--spacing-lg);">
                        <p class="text-secondary text-sm">Endereço de Entrega</p>
                        <p style="font-weight: 600;"><?= e($order['address']) ?></p>
                    </div>
                <?php endif; ?>

                <!-- Items -->
                <div style="border-top: 1px solid var(--border-color); padding-top: var(--spacing-lg);">
                    <h4 class="mb-md">Itens do Pedido</h4>
                    <?php foreach ($items as $item): ?>
                        <div
                            style="display: flex; justify-content: space-between; padding: var(--spacing-sm) 0; border-bottom: 1px solid var(--border-color);">
                            <div>
                                <span style="font-weight: 600;"><?= $item['quantity'] ?>x</span>
                                <?= e($item['product_name']) ?>
                                <?php if ($item['customizations']): ?>
                                    <?php $customs = json_decode($item['customizations'], true); ?>
                                    <?php if ($customs): ?>
                                        <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 2px;">
                                            <?php if (isset($customs['massa'])): ?>
                                                <span>Massa: <?= e($customs['massa']) ?></span>
                                            <?php endif; ?>
                                            <?php if (isset($customs['ingredientes']) && $customs['ingredientes']): ?>
                                                <span> | Ingred.: <?= e(implode(', ', $customs['ingredientes'])) ?></span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                            <span><?= money($item['subtotal']) ?></span>
                        </div>
                    <?php endforeach; ?>

                    <div style="padding-top: var(--spacing-md);">
                        <div style="display: flex; justify-content: space-between; margin-bottom: var(--spacing-xs);">
                            <span class="text-secondary">Subtotal</span>
                            <span><?= money($order['subtotal']) ?></span>
                        </div>
                        <?php if ($order['delivery_fee'] > 0): ?>
                            <div style="display: flex; justify-content: space-between; margin-bottom: var(--spacing-xs);">
                                <span class="text-secondary">Taxa de Entrega</span>
                                <span><?= money($order['delivery_fee']) ?></span>
                            </div>
                        <?php endif; ?>
                        <div
                            style="display: flex; justify-content: space-between; font-size: 1.25rem; font-weight: 700; padding-top: var(--spacing-md); border-top: 2px solid var(--border-color);">
                            <span>Total</span>
                            <span class="text-accent"><?= money($order['total']) ?></span>
                        </div>
                    </div>
                </div>

                <?php if ($order['notes']): ?>
                    <div
                        style="margin-top: var(--spacing-lg); padding: var(--spacing-md); background: rgba(255, 159, 28, 0.1); border-radius: var(--radius-lg);">
                        <p class="text-secondary text-sm">Observações</p>
                        <p><?= e($order['notes']) ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <!-- Action Buttons -->
        <div
            style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--spacing-md); margin-bottom: var(--spacing-lg);">
            <a href="<?= $whatsappLink ?>" target="_blank" class="btn btn-lg"
                style="background: #25d366; color: white;">
                📱 WhatsApp
            </a>
            <button onclick="printReceipt()" class="btn btn-secondary btn-lg">
                🧾 Comprovante
            </button>
        </div>

        <!-- Check Status -->
        <div class="card" x-data="statusChecker()" x-init="startPolling()">
            <div class="card-body text-center">
                <h4 class="mb-md">Acompanhe seu Pedido</h4>

                <div
                    style="display: flex; justify-content: center; align-items: center; gap: var(--spacing-lg); margin-bottom: var(--spacing-lg);">
                    <div
                        :class="{ 'opacity-100': ['RECEBIDO', 'PREPARANDO', 'PRONTO', 'FINALIZADO'].includes(currentStatus), 'opacity-30': !['RECEBIDO', 'PREPARANDO', 'PRONTO', 'FINALIZADO'].includes(currentStatus) }">
                        <div
                            style="width: 60px; height: 60px; background: var(--accent-blue); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin: 0 auto;">
                            ✓</div>
                        <p class="text-sm mt-sm">Recebido</p>
                    </div>
                    <div style="width: 40px; height: 2px; background: var(--border-color);"></div>
                    <div
                        :class="{ 'opacity-100': ['PREPARANDO', 'PRONTO', 'FINALIZADO'].includes(currentStatus), 'opacity-30': !['PREPARANDO', 'PRONTO', 'FINALIZADO'].includes(currentStatus) }">
                        <div
                            style="width: 60px; height: 60px; background: var(--accent-secondary); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin: 0 auto;">
                            👨‍🍳</div>
                        <p class="text-sm mt-sm">Preparando</p>
                    </div>
                    <div style="width: 40px; height: 2px; background: var(--border-color);"></div>
                    <div
                        :class="{ 'opacity-100': ['PRONTO', 'FINALIZADO'].includes(currentStatus), 'opacity-30': !['PRONTO', 'FINALIZADO'].includes(currentStatus) }">
                        <div
                            style="width: 60px; height: 60px; background: var(--accent-green); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin: 0 auto;">
                            ✅</div>
                        <p class="text-sm mt-sm">Pronto!</p>
                    </div>
                </div>

                <p class="text-secondary">Status atual: <strong x-text="statusText"></strong></p>
                <p class="text-muted text-sm">Atualizado automaticamente a cada 5 segundos</p>
            </div>
        </div>

        <!-- Back to Menu -->
        <div class="text-center mt-xl">
            <a href="<?= url('menu') ?>" class="btn btn-secondary">← Fazer Novo Pedido</a>
        </div>
    </div>
</section>

<style>
    .opacity-30 {
        opacity: 0.3;
    }

    .opacity-100 {
        opacity: 1;
    }

    .text-sm {
        font-size: 0.875rem;
    }
</style>

<script>
    function statusChecker() {
        return {
            currentStatus: '<?= $order['status'] ?>',
            orderId: <?= $order['id'] ?>,

            get statusText() {
                const texts = {
                    'RECEBIDO': 'Pedido Recebido',
                    'PREPARANDO': 'Em Preparo',
                    'PRONTO': 'Pronto para Retirada!',
                    'FINALIZADO': 'Finalizado',
                    'CANCELADO': 'Cancelado'
                };
                return texts[this.currentStatus] || this.currentStatus;
            },

            startPolling() {
                setInterval(() => this.checkStatus(), 5000);
            },

            async checkStatus() {
                try {
                    const res = await fetch('<?= url('api/order/') ?>' + this.orderId);
                    const data = await res.json();
                    if (data.success && data.order) {
                        const oldStatus = this.currentStatus;
                        this.currentStatus = data.order.status;

                        // Play sound if status changed to PRONTO
                        if (this.currentStatus === 'PRONTO' && oldStatus !== 'PRONTO') {
                            this.playNotification();
                        }
                    }
                } catch (e) {
                    console.error('Failed to check status:', e);
                }
            },

            playNotification() {
                // Try to use speech synthesis
                if ('speechSynthesis' in window) {
                    const msg = new SpeechSynthesisUtterance('<?= e($order['customer_name']) ?>, seu pedido está pronto!');
                    msg.lang = 'pt-BR';
                    speechSynthesis.speak(msg);
                }

                // Also play a beep
                const audioContext = new (window.AudioContext || window.webkitAudioContext)();
                const oscillator = audioContext.createOscillator();
                const gainNode = audioContext.createGain();

                oscillator.connect(gainNode);
                gainNode.connect(audioContext.destination);

                oscillator.frequency.value = 800;
                gainNode.gain.value = 0.3;

                oscillator.start();
                setTimeout(() => oscillator.stop(), 300);
            }
        }
    }

    function printReceipt() {
        const receiptWindow = window.open('', '_blank', 'width=400,height=600');
        receiptWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Comprovante - Pedido #<?= $order['order_number'] ?></title>
                <meta charset="UTF-8">
                <style>
                    * { margin: 0; padding: 0; box-sizing: border-box; }
                    body { font-family: 'Courier New', monospace; font-size: 12px; padding: 20px; max-width: 300px; margin: 0 auto; }
                    .header { text-align: center; margin-bottom: 20px; border-bottom: 2px dashed #000; padding-bottom: 15px; }
                    .header h1 { font-size: 18px; margin-bottom: 5px; }
                    .header p { font-size: 10px; }
                    .order-num { font-size: 24px; font-weight: bold; margin: 10px 0; }
                    .info { margin-bottom: 15px; }
                    .info-row { display: flex; justify-content: space-between; margin-bottom: 5px; }
                    .items { border-top: 1px dashed #000; border-bottom: 1px dashed #000; padding: 10px 0; margin-bottom: 15px; }
                    .item { margin-bottom: 8px; }
                    .item-name { font-weight: bold; }
                    .item-detail { font-size: 10px; color: #666; margin-left: 10px; }
                    .item-price { text-align: right; }
                    .totals { margin-bottom: 15px; }
                    .totals .row { display: flex; justify-content: space-between; margin-bottom: 5px; }
                    .totals .total { font-size: 16px; font-weight: bold; border-top: 2px solid #000; padding-top: 10px; margin-top: 10px; }
                    .footer { text-align: center; font-size: 10px; border-top: 2px dashed #000; padding-top: 15px; }
                    .payment { background: #f5f5f5; padding: 10px; margin-bottom: 15px; text-align: center; }
                    @media print { body { padding: 0; } }
                </style>
            </head>
            <body>
                <div class="header">
                    <h1>🍝 Delícias da Fran</h1>
                    <p><?= config('business.address', 'Brasília - DF') ?></p>
                    <p>Tel: <?= config('whatsapp.number', '61991930671') ?></p>
                    <div class="order-num">Pedido #<?= $order['order_number'] ?></div>
                    <p><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></p>
                </div>
                
                <div class="info">
                    <div class="info-row"><span>Cliente:</span><span><?= e($order['customer_name']) ?></span></div>
                    <div class="info-row"><span>Tel:</span><span><?= e($order['customer_phone']) ?></span></div>
                    <div class="info-row"><span>Tipo:</span><span><?= $order['delivery_type'] === 'ENTREGA' ? 'Entrega' : 'Retirada' ?></span></div>
                    <?php if ($order['delivery_type'] === 'ENTREGA' && $order['address']): ?>
                    <div class="info-row"><span>Endereço:</span></div>
                    <p style="font-size: 10px;"><?= e($order['address']) ?></p>
                    <?php endif; ?>
                </div>
                
                <div class="items">
                    <?php foreach ($items as $item): ?>
                    <div class="item">
                        <div class="item-name"><?= $item['quantity'] ?>x <?= e($item['product_name']) ?></div>
                        <?php if ($item['customizations']): ?>
                        <?php $customs = json_decode($item['customizations'], true); ?>
                        <?php if ($customs): ?>
                        <div class="item-detail">
                            <?php foreach ($customs as $type => $val): ?>
                            <?= ucfirst($type) ?>: <?= is_array($val) ? implode(', ', $val) : $val ?><br>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                        <?php endif; ?>
                        <div class="item-price"><?= money($item['subtotal']) ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="totals">
                    <div class="row"><span>Subtotal:</span><span><?= money($order['subtotal']) ?></span></div>
                    <?php if ($order['delivery_fee'] > 0): ?>
                    <div class="row"><span>Taxa Entrega:</span><span><?= money($order['delivery_fee']) ?></span></div>
                    <?php endif; ?>
                    <div class="row total"><span>TOTAL:</span><span><?= money($order['total']) ?></span></div>
                </div>
                
                <div class="payment">
                    <strong>Pagamento: <?php
                    $paymentTexts = ['PIX' => 'PIX', 'DINHEIRO' => 'Dinheiro', 'CARTAO_CREDITO' => 'Cartão Crédito', 'CARTAO_DEBITO' => 'Cartão Débito'];
                    echo $paymentTexts[$order['payment_method'] ?? 'DINHEIRO'] ?? 'Dinheiro';
                    ?></strong>
                    <?php if ($order['payment_method'] === 'DINHEIRO' && !empty($order['change_for'])): ?>
                    <br>Troco para: <?= money($order['change_for']) ?>
                    <br>Troco: <?= money($order['change_for'] - $order['total']) ?>
                    <?php endif; ?>
                </div>
                
                <?php if ($order['notes']): ?>
                <div style="margin-bottom: 15px; font-size: 10px;">
                    <strong>Obs:</strong> <?= e($order['notes']) ?>
                </div>
                <?php endif; ?>
                
                <div class="footer">
                    <p>Obrigado pela preferência!</p>
                    <p>@deliciasdafran</p>
                </div>
                
                <script>
                    window.onload = function() {
                        window.print();
                    }
</script>
</body>

</html>
`);
receiptWindow.document.close();
}
</script>

<?php
$content = ob_get_clean();
$title = 'Pedido #' . $order['order_number'];
require VIEWS_PATH . '/layouts/app.php';
?>