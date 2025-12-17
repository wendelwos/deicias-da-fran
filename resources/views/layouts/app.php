<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Delícias da Fran' ?> | Delícias da Fran</title>
    <meta name="description"
        content="<?= $description ?? 'Peça agora os melhores lanches, macarrão na chapa e combos. Entrega rápida!' ?>">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;700;800&display=swap"
        rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body x-data="app()" x-init="init()">

    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="header-content">
                <a href="<?= url('/') ?>" class="logo">
                    <img src="<?= asset('img/logo.png') ?>" alt="Delícias da Fran" class="logo-img">
                </a>

                <nav class="nav">
                    <a href="<?= url('/') ?>" class="nav-link">Início</a>
                    <a href="<?= url('menu') ?>" class="nav-link">Cardápio</a>
                    <a href="<?= url('ready') ?>" class="nav-link">Pedidos Prontos</a>
                </nav>

                <button @click="showCart = true" class="btn btn-secondary cart-badge">
                    🛒 Carrinho
                    <span x-show="cart.length > 0" x-text="cartCount" class="cart-count" x-cloak></span>
                </button>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <?php if (isset($content)): ?>
            <?= $content ?>
        <?php endif; ?>
    </main>

    <!-- Fixed Cart Bar -->
    <div class="cart-bar" :class="{ 'visible': cart.length > 0 }">
        <div class="cart-bar-content">
            <div class="cart-summary">
                <div class="cart-summary-icon">🛒</div>
                <div class="cart-summary-info">
                    <h4 x-text="cartCount + ' ' + (cartCount === 1 ? 'item' : 'itens')"></h4>
                    <p x-text="formatMoney(cartTotal)"></p>
                </div>
            </div>
            <a href="<?= url('checkout') ?>" class="btn btn-primary btn-lg">
                Finalizar Pedido →
            </a>
        </div>
    </div>

    <!-- WhatsApp FAB -->
    <a href="https://wa.me/<?= config('whatsapp.number') ?>" target="_blank" class="whatsapp-fab"
        title="Fale conosco no WhatsApp">
        📱
    </a>

    <!-- Cart Sidebar Modal -->
    <div x-show="showCart" x-cloak class="cart-modal" style="position: fixed; inset: 0; z-index: 200;">

        <!-- Backdrop -->
        <div @click="showCart = false" style="position: absolute; inset: 0; background: rgba(0,0,0,0.7);"></div>

        <!-- Sidebar -->
        <div x-show="showCart" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform translate-x-full"
            x-transition:enter-end="opacity-100 transform translate-x-0"
            style="position: absolute; right: 0; top: 0; bottom: 0; width: 100%; max-width: 400px; background: var(--bg-primary); overflow-y: auto;">

            <div style="padding: 1.5rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <h2>Seu Carrinho</h2>
                    <button @click="showCart = false" class="btn btn-icon btn-secondary">✕</button>
                </div>

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
                                    <h4 x-text="item.name" style="margin-bottom: 0.25rem;"></h4>
                                    <template x-if="item.customizations">
                                        <p style="font-size: 0.75rem; color: var(--text-muted);">
                                            <template x-for="(vals, type) in item.customizations" :key="type">
                                                <span
                                                    x-text="type + ': ' + (Array.isArray(vals) ? vals.join(', ') : vals)"></span>
                                            </template>
                                        </p>
                                    </template>
                                    <p class="text-accent" style="font-weight: 600;"
                                        x-text="formatMoney(item.price * item.quantity)"></p>
                                </div>
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <button @click="updateQuantity(index, item.quantity - 1)"
                                        class="btn btn-sm btn-secondary">−</button>
                                    <span x-text="item.quantity" style="min-width: 24px; text-align: center;"></span>
                                    <button @click="updateQuantity(index, item.quantity + 1)"
                                        class="btn btn-sm btn-secondary">+</button>
                                </div>
                            </div>
                        </template>

                        <div style="padding: 1.5rem 0; border-top: 2px solid var(--border-color); margin-top: 1rem;">
                            <div
                                style="display: flex; justify-content: space-between; font-size: 1.25rem; font-weight: 700;">
                                <span>Total</span>
                                <span class="text-accent" x-text="formatMoney(cartTotal)"></span>
                            </div>
                        </div>

                        <a href="<?= url('checkout') ?>" class="btn btn-primary btn-block btn-lg">
                            Finalizar Pedido
                        </a>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        function app() {
            return {
                cart: JSON.parse(localStorage.getItem('cart') || '[]'),
                showCart: false,

                init() {
                    // Watch for cart changes
                    this.$watch('cart', (value) => {
                        localStorage.setItem('cart', JSON.stringify(value));
                    });

                    // Listen for cart updates from other components
                    window.addEventListener('cart-updated', () => {
                        this.cart = JSON.parse(localStorage.getItem('cart') || '[]');
                    });

                    // Listen for storage changes from other tabs
                    window.addEventListener('storage', (e) => {
                        if (e.key === 'cart') {
                            this.cart = JSON.parse(e.newValue || '[]');
                        }
                    });
                },

                get cartCount() {
                    return this.cart.reduce((sum, item) => sum + item.quantity, 0);
                },

                get cartTotal() {
                    return this.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
                },

                addToCart(product) {
                    // Check if product already in cart (without customizations)
                    const existingIndex = this.cart.findIndex(item =>
                        item.id === product.id && !item.customizations && !product.customizations
                    );

                    if (existingIndex > -1) {
                        this.cart[existingIndex].quantity += product.quantity || 1;
                    } else {
                        this.cart.push({
                            id: product.id,
                            name: product.name,
                            price: product.price,
                            quantity: product.quantity || 1,
                            customizations: product.customizations || null,
                            notes: product.notes || null,
                        });
                    }

                    // Show feedback
                    this.showCart = true;
                },

                updateQuantity(index, quantity) {
                    if (quantity <= 0) {
                        this.cart.splice(index, 1);
                    } else {
                        this.cart[index].quantity = quantity;
                    }
                },

                removeFromCart(index) {
                    this.cart.splice(index, 1);
                },

                clearCart() {
                    this.cart = [];
                },

                formatMoney(value) {
                    return 'R$ ' + value.toFixed(2).replace('.', ',');
                }
            }
        }
    </script>
</body>

</html>