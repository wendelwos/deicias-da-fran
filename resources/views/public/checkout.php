<?php
// Checkout Page View
ob_start();
?>

<section class="section" style="padding-top: var(--spacing-lg);">
    <div class="container container-sm">
        <h1 class="section-title mb-xl">Finalizar Pedido</h1>

        <div x-data="checkoutPage()">
            <!-- Cart Summary -->
            <div class="card mb-xl">
                <div class="card-body">
                    <h3 class="mb-lg">Seu Pedido</h3>

                    <template x-if="cart.length === 0">
                        <div class="empty-state">
                            <div class="empty-state-icon">🛒</div>
                            <p>Seu carrinho está vazio</p>
                            <a href="<?= url('menu') ?>" class="btn btn-primary mt-lg">Ver Cardápio</a>
                        </div>
                    </template>

                    <template x-if="cart.length > 0">
                        <div>
                            <template x-for="(item, index) in cart" :key="index">
                                <div
                                    style="display: flex; gap: 1rem; padding: 1rem 0; border-bottom: 1px solid var(--border-color);">
                                    <div style="flex: 1;">
                                        <h4 x-text="item.quantity + 'x ' + item.name" style="margin-bottom: 0.25rem;">
                                        </h4>
                                        <template x-if="item.customizations">
                                            <div style="font-size: 0.75rem; color: var(--text-muted);">
                                                <template x-if="item.customizations.massa">
                                                    <p x-text="'Massa: ' + item.customizations.massa"></p>
                                                </template>
                                                <template
                                                    x-if="item.customizations.ingredientes && item.customizations.ingredientes.length">
                                                    <p
                                                        x-text="'Ingredientes: ' + item.customizations.ingredientes.join(', ')">
                                                    </p>
                                                </template>
                                                <template
                                                    x-if="item.customizations.temperos && item.customizations.temperos.length">
                                                    <p x-text="'Temperos: ' + item.customizations.temperos.join(', ')">
                                                    </p>
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                    <div style="text-align: right;">
                                        <p class="text-accent" style="font-weight: 600;"
                                            x-text="formatMoney(item.price * item.quantity)"></p>
                                        <button @click="removeItem(index)"
                                            class="btn btn-sm btn-secondary mt-sm">Remover</button>
                                    </div>
                                </div>
                            </template>

                            <div style="padding-top: 1rem;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                                    <span class="text-secondary">Subtotal</span>
                                    <span x-text="formatMoney(subtotal)"></span>
                                </div>
                                <div x-show="deliveryType === 'ENTREGA'"
                                    style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                                    <span class="text-secondary">Taxa de Entrega</span>
                                    <span x-text="formatMoney(deliveryFee)"></span>
                                </div>
                                <div
                                    style="display: flex; justify-content: space-between; font-size: 1.25rem; font-weight: 700; padding-top: 1rem; border-top: 2px solid var(--border-color);">
                                    <span>Total</span>
                                    <span class="text-accent" x-text="formatMoney(total)"></span>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Checkout Form -->
            <template x-if="cart.length > 0">
                <form @submit.prevent="submitOrder" class="card">
                    <div class="card-body">
                        <h3 class="mb-lg">Seus Dados</h3>

                        <!-- Delivery Type -->
                        <div class="form-group">
                            <label class="form-label">Tipo de Pedido</label>
                            <div class="form-radio-group">
                                <label class="form-radio" :class="{ 'selected': deliveryType === 'RETIRADA' }">
                                    <input type="radio" name="delivery_type" value="RETIRADA" x-model="deliveryType">
                                    <span class="form-radio-icon"></span>
                                    <span>🏪 Retirada</span>
                                </label>
                                <label class="form-radio" :class="{ 'selected': deliveryType === 'ENTREGA' }">
                                    <input type="radio" name="delivery_type" value="ENTREGA" x-model="deliveryType">
                                    <span class="form-radio-icon"></span>
                                    <span>🛵 Entrega</span>
                                </label>
                            </div>
                        </div>

                        <!-- Name -->
                        <div class="form-group">
                            <label class="form-label" for="customer_name">Nome *</label>
                            <input type="text" id="customer_name" name="customer_name" x-model="customerName"
                                class="form-input" placeholder="Seu nome" required>
                            <?php if ($err = error('customer_name')): ?>
                                <p class="form-error"><?= e($err) ?></p>
                            <?php endif; ?>
                        </div>

                        <!-- Phone -->
                        <div class="form-group">
                            <label class="form-label" for="customer_phone">Telefone (WhatsApp) *</label>
                            <input type="tel" id="customer_phone" name="customer_phone" x-model="customerPhone"
                                class="form-input" placeholder="(61) 99999-9999" required>
                            <?php if ($err = error('customer_phone')): ?>
                                <p class="form-error"><?= e($err) ?></p>
                            <?php endif; ?>
                        </div>

                        <!-- Address (only for delivery) -->
                        <div class="form-group" x-show="deliveryType === 'ENTREGA'" x-transition>
                            <label class="form-label" for="address">Endereço Completo *</label>
                            <textarea id="address" name="address" x-model="address" class="form-textarea"
                                placeholder="Rua, número, bairro, complemento..." rows="3"
                                :required="deliveryType === 'ENTREGA'"></textarea>
                            <?php if ($err = error('address')): ?>
                                <p class="form-error"><?= e($err) ?></p>
                            <?php endif; ?>
                        </div>

                        <!-- Notes -->
                        <div class="form-group">
                            <label class="form-label" for="notes">Observações</label>
                            <textarea id="notes" name="notes" x-model="notes" class="form-textarea"
                                placeholder="Alguma observação sobre seu pedido?" rows="2"></textarea>
                        </div>

                        <!-- Payment Method -->
                        <div class="form-group">
                            <label class="form-label">Forma de Pagamento *</label>
                            <div class="payment-options">
                                <label class="payment-option" :class="{ 'selected': paymentMethod === 'PIX' }">
                                    <input type="radio" name="payment_method" value="PIX" x-model="paymentMethod">
                                    <div class="payment-option-content">
                                        <span class="payment-icon">📱</span>
                                        <span class="payment-label">PIX</span>
                                    </div>
                                </label>
                                <label class="payment-option" :class="{ 'selected': paymentMethod === 'DINHEIRO' }">
                                    <input type="radio" name="payment_method" value="DINHEIRO" x-model="paymentMethod">
                                    <div class="payment-option-content">
                                        <span class="payment-icon">💵</span>
                                        <span class="payment-label">Dinheiro</span>
                                    </div>
                                </label>
                                <label class="payment-option"
                                    :class="{ 'selected': paymentMethod === 'CARTAO_CREDITO' }">
                                    <input type="radio" name="payment_method" value="CARTAO_CREDITO"
                                        x-model="paymentMethod">
                                    <div class="payment-option-content">
                                        <span class="payment-icon">💳</span>
                                        <span class="payment-label">Crédito</span>
                                    </div>
                                </label>
                                <label class="payment-option"
                                    :class="{ 'selected': paymentMethod === 'CARTAO_DEBITO' }">
                                    <input type="radio" name="payment_method" value="CARTAO_DEBITO"
                                        x-model="paymentMethod">
                                    <div class="payment-option-content">
                                        <span class="payment-icon">💳</span>
                                        <span class="payment-label">Débito</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Change For (only for cash) -->
                        <div class="form-group" x-show="paymentMethod === 'DINHEIRO'" x-transition>
                            <label class="form-label" for="change_for">Troco para (R$)</label>
                            <input type="number" id="change_for" name="change_for" x-model="changeFor"
                                class="form-input" placeholder="Ex: 50" min="0" step="0.01" style="max-width: 150px;">
                            <small class="text-muted" x-show="changeFor && changeFor > total">
                                Troco: <span x-text="formatMoney(changeFor - total)"></span>
                            </small>
                        </div>

                        <!-- Submit -->
                        <button type="submit" class="btn btn-primary btn-block btn-lg" :disabled="isSubmitting">
                            <span x-show="!isSubmitting">Confirmar Pedido</span>
                            <span x-show="isSubmitting">Enviando...</span>
                        </button>
                    </div>
                </form>
            </template>
        </div>
    </div>
</section>

<script>
    function checkoutPage() {
        return {
            cart: JSON.parse(localStorage.getItem('cart') || '[]'),
            deliveryType: 'RETIRADA',
            customerName: '<?= old('customer_name') ?>',
            customerPhone: '<?= old('customer_phone') ?>',
            address: '<?= old('address') ?>',
            notes: '',
            paymentMethod: 'PIX',
            changeFor: '',
            deliveryFee: <?= config('orders.delivery_fee', 5) ?>,
            isSubmitting: false,

            get subtotal() {
                return this.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            },

            get total() {
                return this.subtotal + (this.deliveryType === 'ENTREGA' ? this.deliveryFee : 0);
            },

            formatMoney(value) {
                return 'R$ ' + value.toFixed(2).replace('.', ',');
            },

            removeItem(index) {
                this.cart.splice(index, 1);
                localStorage.setItem('cart', JSON.stringify(this.cart));
            },

            async submitOrder() {
                if (this.cart.length === 0) {
                    alert('Seu carrinho está vazio!');
                    return;
                }

                if (!this.customerName.trim()) {
                    alert('Por favor, informe seu nome.');
                    return;
                }

                if (!this.customerPhone.trim()) {
                    alert('Por favor, informe seu telefone.');
                    return;
                }

                if (this.deliveryType === 'ENTREGA' && !this.address.trim()) {
                    alert('Por favor, informe o endereço para entrega.');
                    return;
                }

                this.isSubmitting = true;

                // Create form data
                const formData = new FormData();
                formData.append('_token', '<?= csrf_token() ?>');
                formData.append('customer_name', this.customerName);
                formData.append('customer_phone', this.customerPhone);
                formData.append('delivery_type', this.deliveryType);
                formData.append('address', this.address);
                formData.append('notes', this.notes);
                formData.append('payment_method', this.paymentMethod);
                formData.append('change_for', this.changeFor || '');
                formData.append('cart_items', JSON.stringify(this.cart));

                try {
                    const response = await fetch('<?= url('order') ?>', {
                        method: 'POST',
                        body: formData
                    });

                    if (response.redirected) {
                        // Clear cart and redirect
                        localStorage.removeItem('cart');
                        window.location.href = response.url;
                    } else {
                        const text = await response.text();
                        if (response.ok) {
                            localStorage.removeItem('cart');
                            // Check if there's a redirect in the response
                            if (text.includes('Location:')) {
                                window.location.reload();
                            } else {
                                window.location.href = '<?= url('menu') ?>';
                            }
                        } else {
                            alert('Erro ao criar pedido. Tente novamente.');
                            this.isSubmitting = false;
                        }
                    }
                } catch (err) {
                    console.error(err);
                    alert('Erro ao enviar pedido. Verifique sua conexão.');
                    this.isSubmitting = false;
                }
            }
        }
    }
</script>

<style>
    .payment-options {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: var(--spacing-sm);
    }
    
    .payment-option {
        cursor: pointer;
    }
    
    .payment-option input {
        display: none;
    }
    
    .payment-option-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: var(--spacing-md);
        background: var(--bg-card);
        border: 2px solid var(--border-color);
        border-radius: var(--radius-lg);
        transition: all var(--transition-fast);
    }
    
    .payment-option:hover .payment-option-content {
        border-color: var(--accent-primary);
    }
    
    .payment-option.selected .payment-option-content {
        border-color: var(--accent-primary);
        background: rgba(255, 87, 34, 0.1);
    }
    
    .payment-icon {
        font-size: 1.5rem;
        margin-bottom: var(--spacing-xs);
    }
    
    .payment-label {
        font-size: 0.75rem;
        font-weight: 600;
    }
    
    @media (max-width: 480px) {
        .payment-options {
            grid-template-columns: repeat(2, 1fr);
        }
    }
</style>

<?php
// Clear old input and errors
unset($_SESSION['old_input'], $_SESSION['errors']);

$content = ob_get_clean();
$title = 'Checkout';
require VIEWS_PATH . '/layouts/app.php';
?>