<?php
// Menu Page View
ob_start();
?>

<div x-data="menuPage()">
    <section class="section" style="padding-top: var(--spacing-lg);">
        <div class="container">
            <div class="section-header">
                <div>
                    <h1 class="section-title">Cardápio</h1>
                    <p class="section-subtitle">Escolha seus favoritos</p>
                </div>
            </div>

            <!-- Category Navigation -->
            <nav class="category-nav">
                <button @click="activeCategory = null" :class="{ 'active': !activeCategory }" class="category-pill">
                    Todos
                </button>
                <?php foreach ($categories as $cat): ?>
                    <button @click="activeCategory = '<?= $cat['slug'] ?>'; scrollToCategory('<?= $cat['slug'] ?>')"
                        :class="{ 'active': activeCategory === '<?= $cat['slug'] ?>' }" class="category-pill">
                        <?= e($cat['name']) ?>
                    </button>
                <?php endforeach; ?>
            </nav>

            <!-- Products by Category -->
            <?php foreach ($categories as $category): ?>
                <?php if (isset($products[$category['slug']]) && count($products[$category['slug']]) > 0): ?>
                    <section id="<?= $category['slug'] ?>" class="mb-xl" style="scroll-margin-top: 100px;">
                        <h2
                            style="font-size: 1.5rem; margin-bottom: var(--spacing-lg); display: flex; align-items: center; gap: var(--spacing-sm);">
                            <?php
                            $icons = [
                                'macarrao-na-chapa' => '🍝',
                                'burgers' => '🍔',
                                'hot-dog' => '🌭',
                                'batatas' => '🍟',
                                'caldos' => '🍲',
                                'bebidas' => '🥤',
                                'sucos' => '🧃',
                            ];
                            echo $icons[$category['slug']] ?? '🍽️';
                            ?>
                            <?= e($category['name']) ?>
                        </h2>

                        <div class="products-grid">
                            <?php foreach ($products[$category['slug']] as $product): ?>
                                <?php
                                $options = $productOptions[$product['id']] ?? [];
                                $hasMassa = false;
                                $hasSabor = false;
                                $hasIngrediente = false;
                                $hasTempero = false;

                                foreach ($options as $opt) {
                                    if ($opt['option_type'] === 'massa')
                                        $hasMassa = true;
                                    if ($opt['option_type'] === 'sabor')
                                        $hasSabor = true;
                                    if ($opt['option_type'] === 'ingrediente')
                                        $hasIngrediente = true;
                                    if ($opt['option_type'] === 'tempero')
                                        $hasTempero = true;
                                }
                                ?>

                                <?php if ($product['is_buildable'] && $hasMassa): ?>
                                    <!-- Macarrão Buildable (full builder) -->
                                    <div class="product-card product-card-buildable">
                                        <div class="buildable-layout">
                                            <div class="product-image product-image-macarrao">
                                                <img src="<?= asset('img/banner_macarrao .png') ?>" alt="Monte seu Macarrão">
                                                <span class="product-badge">MONTE O SEU</span>
                                            </div>
                                            <div class="product-content"
                                                x-data="buildableMacarrao(<?= $product['id'] ?>, '<?= e($product['name']) ?>', <?= $product['price'] ?>, <?= $product['max_ingredients'] ?>, <?= $product['max_seasonings'] ?>)">
                                                <h3 class="product-name" style="font-size: 1.5rem;"><?= e($product['name']) ?></h3>
                                                <p class="product-description" style="margin-bottom: var(--spacing-lg);">
                                                    <?= e($product['description']) ?>
                                                </p>

                                                <!-- Massa Selection -->
                                                <div class="mb-lg">
                                                    <label class="form-label">🍝 Escolha a Massa (1)</label>
                                                    <div class="options-grid">
                                                        <?php foreach ($options as $opt): ?>
                                                            <?php if ($opt['option_type'] === 'massa'): ?>
                                                                <button type="button" @click="selectedMassa = '<?= e($opt['name']) ?>'"
                                                                    :class="{ 'btn-primary': selectedMassa === '<?= e($opt['name']) ?>', 'btn-secondary': selectedMassa !== '<?= e($opt['name']) ?>' }"
                                                                    class="btn btn-sm">
                                                                    <?= e($opt['name']) ?>
                                                                </button>
                                                            <?php endif; ?>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>

                                                <!-- Ingredientes Selection -->
                                                <div class="mb-lg">
                                                    <label class="form-label">
                                                        🥓 Ingredientes (até <?= $product['max_ingredients'] ?>)
                                                        <span x-text="'(' + selectedIngredientes.length + '/' + maxIngredientes + ')'"
                                                            class="text-muted"></span>
                                                    </label>
                                                    <div class="options-grid">
                                                        <?php foreach ($options as $opt): ?>
                                                            <?php if ($opt['option_type'] === 'ingrediente'): ?>
                                                                <button type="button" @click="toggleIngrediente('<?= e($opt['name']) ?>')"
                                                                    :class="{ 'btn-primary': selectedIngredientes.includes('<?= e($opt['name']) ?>'), 'btn-secondary': !selectedIngredientes.includes('<?= e($opt['name']) ?>') }"
                                                                    :disabled="!selectedIngredientes.includes('<?= e($opt['name']) ?>') && selectedIngredientes.length >= maxIngredientes"
                                                                    class="btn btn-sm">
                                                                    <?= e($opt['name']) ?>
                                                                </button>
                                                            <?php endif; ?>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>

                                                <!-- Temperos Selection -->
                                                <div class="mb-lg">
                                                    <label class="form-label">
                                                        🌿 Temperos (até <?= $product['max_seasonings'] ?>)
                                                        <span x-text="'(' + selectedTemperos.length + '/' + maxTemperos + ')'"
                                                            class="text-muted"></span>
                                                    </label>
                                                    <div class="options-grid">
                                                        <?php foreach ($options as $opt): ?>
                                                            <?php if ($opt['option_type'] === 'tempero'): ?>
                                                                <button type="button" @click="toggleTempero('<?= e($opt['name']) ?>')"
                                                                    :class="{ 'btn-primary': selectedTemperos.includes('<?= e($opt['name']) ?>'), 'btn-secondary': !selectedTemperos.includes('<?= e($opt['name']) ?>') }"
                                                                    :disabled="!selectedTemperos.includes('<?= e($opt['name']) ?>') && selectedTemperos.length >= maxTemperos"
                                                                    class="btn btn-sm">
                                                                    <?= e($opt['name']) ?>
                                                                </button>
                                                            <?php endif; ?>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>

                                                <div class="product-footer">
                                                    <span class="price" style="font-size: 2rem;"><?= money($product['price']) ?></span>
                                                    <button @click="addToCart()" :disabled="!selectedMassa"
                                                        class="btn btn-primary btn-lg">
                                                        Adicionar
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                <?php elseif ($product['is_buildable'] && $hasSabor): ?>
                                    <!-- Suco/Creme Buildable (flavor selection only) -->
                                    <div class="product-card"
                                        x-data="buildableSuco(<?= $product['id'] ?>, '<?= e($product['name']) ?>', <?= $product['price'] ?>)">
                                        <div class="product-image">
                                            🧃
                                            <span class="product-badge">ESCOLHA O SABOR</span>
                                        </div>
                                        <div class="product-content">
                                            <h3 class="product-name"><?= e($product['name']) ?></h3>
                                            <p class="product-description"><?= nl2br(e($product['description'])) ?></p>

                                            <!-- Sabor Selection -->
                                            <div class="mb-md">
                                                <label class="form-label">Escolha o Sabor:</label>
                                                <select x-model="selectedSabor" class="form-select">
                                                    <option value="">Selecione...</option>
                                                    <?php foreach ($options as $opt): ?>
                                                        <?php if ($opt['option_type'] === 'sabor'): ?>
                                                            <option value="<?= e($opt['name']) ?>"><?= e($opt['name']) ?></option>
                                                        <?php endif; ?>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>

                                            <div class="product-prices">
                                                <span class="price"><?= money($product['price']) ?></span>
                                            </div>
                                            <button @click="addToCart()" :disabled="!selectedSabor" class="btn btn-primary btn-block">
                                                Adicionar
                                            </button>
                                        </div>
                                    </div>

                                <?php else: ?>
                                    <!-- Regular Product -->
                                    <div class="product-card <?= !$product['active'] ? 'product-unavailable' : '' ?>">
                                        <div class="product-image">
                                            <?php if (!empty($product['image'])): ?>
                                                <img src="<?= asset('img/' . $product['image']) ?>" alt="<?= e($product['name']) ?>"
                                                    class="product-img">
                                            <?php else: ?>
                                                <?php
                                                $catIcons = [
                                                    'burgers' => '🍔',
                                                    'hot-dog' => '🌭',
                                                    'batatas' => '🍟',
                                                    'caldos' => '🍲',
                                                    'bebidas' => '🥤',
                                                    'sucos' => '🧃',
                                                ];
                                                echo $catIcons[$category['slug']] ?? '🍽️';
                                                ?>
                                            <?php endif; ?>
                                            <?php if (!$product['active']): ?>
                                                <span class="product-badge badge-unavailable">INDISPONÍVEL</span>
                                            <?php elseif ($product['is_combo']): ?>
                                                <span class="product-badge">COMBO</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="product-content">
                                            <h3 class="product-name"><?= e($product['name']) ?></h3>
                                            <p class="product-description"><?= e($product['description']) ?></p>
                                            <div class="product-prices">
                                                <?php if ($product['is_combo'] && $product['price_combo']): ?>
                                                    <span class="price"><?= money($product['price']) ?></span>
                                                    <span class="price-combo">Combo: <?= money($product['price_combo']) ?></span>
                                                <?php else: ?>
                                                    <span class="price"><?= money($product['price']) ?></span>
                                                <?php endif; ?>
                                            </div>
                                            <?php if (!$product['active']): ?>
                                                <button class="btn btn-secondary btn-block" disabled>
                                                    Indisponível
                                                </button>
                                            <?php elseif ($product['is_combo'] && $product['price_combo']): ?>
                                                <div x-data="{ isCombo: true }" class="combo-section">
                                                    <div class="combo-toggle-wrapper">
                                                        <span class="combo-label" :class="{ 'active': !isCombo }">Simples</span>
                                                        <label class="toggle-switch">
                                                            <input type="checkbox" x-model="isCombo">
                                                            <span class="toggle-slider"></span>
                                                        </label>
                                                        <span class="combo-label" :class="{ 'active': isCombo }">🍟 Combo</span>
                                                    </div>
                                                    <div class="combo-price" x-show="isCombo" x-transition>
                                                        <span class="price-highlight"><?= money($product['price_combo']) ?></span>
                                                    </div>
                                                    <button @click="$dispatch('add-to-cart', {
                                                    id: <?= $product['id'] ?>,
                                                    name: '<?= e($product['name']) ?>' + (isCombo ? ' (Combo)' : ''),
                                                    price: isCombo ? <?= $product['price_combo'] ?> : <?= $product['price'] ?>,
                                                    quantity: 1
                                                })" class="btn btn-primary btn-block mt-sm">
                                                        Adicionar
                                                    </button>
                                                </div>
                                            <?php else: ?>
                                                <button @click="$dispatch('add-to-cart', {
                                                id: <?= $product['id'] ?>,
                                                name: '<?= e($product['name']) ?>',
                                                price: <?= $product['price'] ?>,
                                                quantity: 1
                                            })" class="btn btn-primary btn-block">
                                                    Adicionar
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endif; ?>
            <?php endforeach; ?>

        </div>
    </section>
</div>

<script>
    function menuPage() {
        return {
            activeCategory: null,

            scrollToCategory(slug) {
                document.getElementById(slug)?.scrollIntoView({ behavior: 'smooth' });
            },

            init() {
                // Listen for add-to-cart events
                this.$el.addEventListener('add-to-cart', (e) => {
                    this.addSimple(e.detail);
                });
            },

            addSimple(product) {
                let cart = JSON.parse(localStorage.getItem('cart') || '[]');
                const existing = cart.findIndex(i => i.id === product.id && !i.customizations && i.name === product.name);
                if (existing > -1) {
                    cart[existing].quantity++;
                } else {
                    cart.push({ ...product, quantity: 1 });
                }
                localStorage.setItem('cart', JSON.stringify(cart));
                window.dispatchEvent(new Event('cart-updated'));

                // Show feedback
                this.showToast('Adicionado ao carrinho!');
            },

            showToast(message) {
                const toast = document.createElement('div');
                toast.className = 'toast-notification';
                toast.textContent = message;
                document.body.appendChild(toast);
                setTimeout(() => toast.remove(), 2000);
            }
        }
    }

    function buildableMacarrao(productId, productName, price, maxIng, maxTemp) {
        return {
            productId: productId,
            productName: productName,
            price: price,
            maxIngredientes: maxIng,
            maxTemperos: maxTemp,
            selectedMassa: null,
            selectedIngredientes: [],
            selectedTemperos: [],

            toggleIngrediente(name) {
                const idx = this.selectedIngredientes.indexOf(name);
                if (idx > -1) {
                    this.selectedIngredientes.splice(idx, 1);
                } else if (this.selectedIngredientes.length < this.maxIngredientes) {
                    this.selectedIngredientes.push(name);
                }
            },

            toggleTempero(name) {
                const idx = this.selectedTemperos.indexOf(name);
                if (idx > -1) {
                    this.selectedTemperos.splice(idx, 1);
                } else if (this.selectedTemperos.length < this.maxTemperos) {
                    this.selectedTemperos.push(name);
                }
            },

            addToCart() {
                if (!this.selectedMassa) {
                    alert('Escolha uma massa!');
                    return;
                }

                const product = {
                    id: this.productId,
                    name: this.productName,
                    price: this.price,
                    quantity: 1,
                    customizations: {
                        massa: this.selectedMassa,
                        ingredientes: [...this.selectedIngredientes],
                        temperos: [...this.selectedTemperos]
                    }
                };

                let cart = JSON.parse(localStorage.getItem('cart') || '[]');
                cart.push(product);
                localStorage.setItem('cart', JSON.stringify(cart));
                window.dispatchEvent(new Event('cart-updated'));

                // Reset
                this.selectedMassa = null;
                this.selectedIngredientes = [];
                this.selectedTemperos = [];

                // Show feedback
                const toast = document.createElement('div');
                toast.className = 'toast-notification';
                toast.textContent = 'Macarrão adicionado!';
                document.body.appendChild(toast);
                setTimeout(() => toast.remove(), 2000);
            }
        }
    }

    function buildableSuco(productId, productName, price) {
        return {
            productId: productId,
            productName: productName,
            price: price,
            selectedSabor: '',

            addToCart() {
                if (!this.selectedSabor) {
                    alert('Escolha um sabor!');
                    return;
                }

                const product = {
                    id: this.productId,
                    name: this.productName + ' - ' + this.selectedSabor,
                    price: this.price,
                    quantity: 1,
                    customizations: {
                        sabor: this.selectedSabor
                    }
                };

                let cart = JSON.parse(localStorage.getItem('cart') || '[]');
                cart.push(product);
                localStorage.setItem('cart', JSON.stringify(cart));
                window.dispatchEvent(new Event('cart-updated'));

                // Reset
                this.selectedSabor = '';

                // Show feedback
                const toast = document.createElement('div');
                toast.className = 'toast-notification';
                toast.textContent = 'Suco adicionado!';
                document.body.appendChild(toast);
                setTimeout(() => toast.remove(), 2000);
            }
        }
    }
</script>

<style>
    .options-grid {
        display: flex;
        flex-wrap: wrap;
        gap: var(--spacing-sm);
    }

    .buildable-layout {
        display: grid;
        grid-template-columns: 200px 1fr;
        gap: var(--spacing-lg);
    }

    .product-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-top: var(--spacing-lg);
        border-top: 1px solid var(--border-color);
        margin-top: var(--spacing-lg);
    }

    .price-combo {
        font-size: 0.875rem;
        color: var(--accent-green);
        display: block;
    }

    /* Macarrão Card - Larger */
    .product-card-buildable {
        grid-column: 1 / -1 !important;
        background: var(--bg-card);
    }

    .buildable-layout {
        display: grid;
        grid-template-columns: 180px 1fr;
        gap: var(--spacing-lg);
        padding: var(--spacing-md);
    }

    .product-image-macarrao {
        position: relative;
        border-radius: var(--radius-lg);
        overflow: hidden;
        width: 180px;
        height: 180px;
        flex-shrink: 0;
    }

    .product-image-macarrao img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: var(--radius-lg);
    }

    .buildable-layout .product-name {
        font-size: 1.75rem !important;
        margin-bottom: var(--spacing-sm);
    }

    .buildable-layout .product-description {
        font-size: 1rem;
        margin-bottom: var(--spacing-lg);
    }

    .buildable-layout .form-label {
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: var(--spacing-sm);
        display: block;
    }

    .buildable-layout .mb-lg {
        margin-bottom: var(--spacing-xl);
    }

    .buildable-layout .btn-sm {
        padding: var(--spacing-sm) var(--spacing-md);
        font-size: 0.9rem;
    }

    .buildable-layout .product-footer .price {
        font-size: 2.5rem !important;
    }

    .buildable-layout .product-footer .btn {
        padding: var(--spacing-md) var(--spacing-xl);
        font-size: 1.1rem;
    }

    .toast-notification {
        position: fixed;
        bottom: 100px;
        left: 50%;
        transform: translateX(-50%);
        background: var(--accent-green);
        color: white;
        padding: var(--spacing-md) var(--spacing-lg);
        border-radius: var(--radius-full);
        font-weight: 600;
        z-index: 9999;
        animation: toastIn 0.3s ease, toastOut 0.3s ease 1.7s;
    }

    @keyframes toastIn {
        from {
            opacity: 0;
            transform: translateX(-50%) translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }
    }

    @keyframes toastOut {
        from {
            opacity: 1;
        }

        to {
            opacity: 0;
        }
    }

    /* Toggle Switch for Combo */
    .combo-section {
        margin-top: var(--spacing-md);
    }

    .combo-toggle-wrapper {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: var(--spacing-md);
        margin-bottom: var(--spacing-sm);
    }

    .combo-label {
        font-size: 0.875rem;
        color: var(--text-muted);
        transition: all 0.3s ease;
        font-weight: 500;
    }

    .combo-label.active {
        color: var(--accent-primary);
        font-weight: 700;
    }

    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 56px;
        height: 28px;
    }

    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, #4ade80, #22c55e);
        transition: 0.4s;
        border-radius: 28px;
        box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .toggle-slider:before {
        position: absolute;
        content: "";
        height: 22px;
        width: 22px;
        left: 3px;
        bottom: 3px;
        background: white;
        transition: 0.4s;
        border-radius: 50%;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }

    .toggle-switch input:not(:checked)+.toggle-slider {
        background: linear-gradient(135deg, #94a3b8, #64748b);
    }

    .toggle-switch input:checked+.toggle-slider:before {
        transform: translateX(28px);
    }

    .combo-price {
        text-align: center;
        margin-bottom: var(--spacing-sm);
    }

    .price-highlight {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--accent-primary);
        background: rgba(255, 107, 53, 0.1);
        padding: var(--spacing-xs) var(--spacing-md);
        border-radius: var(--radius-full);
    }

    /* Unavailable Products */
    .product-unavailable {
        position: relative;
        opacity: 0.7;
    }

    .product-unavailable::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.3);
        border-radius: var(--radius-lg);
        pointer-events: none;
    }

    .product-unavailable .product-image img {
        filter: grayscale(50%);
    }

    .badge-unavailable {
        background: linear-gradient(135deg, #ef4444, #dc2626) !important;
        font-size: 0.7rem !important;
        padding: var(--spacing-xs) var(--spacing-md) !important;
        animation: pulse-unavailable 2s infinite;
    }

    @keyframes pulse-unavailable {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: 0.8;
        }
    }

    @media (max-width: 768px) {
        .product-card-buildable {
            grid-column: span 1 !important;
        }

        .buildable-layout {
            grid-template-columns: 1fr !important;
        }

        .buildable-layout .product-image {
            height: 120px;
        }
    }
</style>

<?php
$content = ob_get_clean();
$title = 'Cardápio';
require VIEWS_PATH . '/layouts/app.php';
?>