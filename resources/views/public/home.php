<?php
// Home Page View
ob_start();
?>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <div class="hero-content">
            <h1 class="hero-title">
                Sabor que <span class="text-gradient">transforma</span> seu dia!
            </h1>
            <p class="hero-subtitle">
                Macarrão na chapa, lanches artesanais e combos especiais.
                Faça seu pedido e receba rapidinho!
            </p>
            <div class="hero-cta">
                <a href="<?= url('menu') ?>" class="btn btn-primary btn-lg">
                    🍝 Ver Cardápio
                </a>
                <a href="https://wa.me/<?= config('whatsapp.number') ?>" class="btn btn-secondary btn-lg"
                    target="_blank">
                    📱 Chamar no WhatsApp
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <div>
                <h2 class="section-title">Nosso Cardápio</h2>
                <p class="section-subtitle">Escolha sua categoria favorita</p>
            </div>
            <a href="<?= url('menu') ?>" class="btn btn-secondary">Ver Tudo →</a>
        </div>

        <div class="products-grid">
            <?php foreach ($categories as $category): ?>
                <a href="<?= url('menu#' . $category['slug']) ?>" class="card">
                    <div class="card-image">
                        <?php if (!empty($category['category_image'])): ?>
                            <img src="<?= asset('img/' . $category['category_image']) ?>" alt="<?= e($category['name']) ?>"
                                style="width: 100%; height: 100%; object-fit: cover;">
                        <?php else: ?>
                            <div class="card-placeholder">
                                <?php
                                $icons = [
                                    'macarrao-na-chapa' => '🍝',
                                    'burgers' => '🍔',
                                    'hot-dog' => '🌭',
                                    'batatas' => '🍟',
                                    'caldos' => '🍲',
                                    'bebidas' => '🥤',
                                    'sucos-e-cremes' => '🍹',
                                ];
                                echo $icons[$category['slug']] ?? '🍽️';
                                ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <h3 class="card-title"><?= e($category['name']) ?></h3>
                        <p class="card-text"><?= e($category['description']) ?></p>
                        <div class="card-footer">
                            <span class="text-muted"><?= $category['product_count'] ?> itens</span>
                            <span class="text-accent">Ver →</span>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Featured Combos -->
<?php if (!empty($featured)): ?>
    <section class="section" style="background: var(--bg-secondary);">
        <div class="container">
            <div class="section-header">
                <div>
                    <h2 class="section-title">🔥 Combos em Destaque</h2>
                    <p class="section-subtitle">Os mais pedidos com preço especial</p>
                </div>
            </div>

            <div class="products-grid" x-data>
                <?php foreach ($featured as $product): ?>
                    <div class="product-card">
                        <div class="product-image">
                            <?php if (!empty($product['image'])): ?>
                                <img src="<?= asset('img/' . $product['image']) ?>" alt="<?= e($product['name']) ?>"
                                    style="width: 100%; height: 100%; object-fit: cover;">
                            <?php else: ?>
                                🍔
                            <?php endif; ?>
                            <span class="product-badge">COMBO</span>
                        </div>
                        <div class="product-content">
                            <h3 class="product-name"><?= e($product['name']) ?></h3>
                            <p class="product-description"><?= e($product['description']) ?></p>
                            <div class="product-prices">
                                <?php if ($product['price_combo']): ?>
                                    <span class="price"><?= money($product['price_combo']) ?></span>
                                    <span class="price-old"><?= money($product['price']) ?></span>
                                <?php else: ?>
                                    <span class="price"><?= money($product['price']) ?></span>
                                <?php endif; ?>
                            </div>
                            <button @click="$dispatch('add-to-cart', { 
                        id: <?= $product['id'] ?>,
                        name: '<?= e($product['name']) ?>',
                        price: <?= $product['price_combo'] ?? $product['price'] ?>,
                        quantity: 1
                    })" class="btn btn-primary btn-block">
                                Adicionar ao Carrinho
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- Info Section -->
<section class="section">
    <div class="container">
        <div
            style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: var(--spacing-lg);">
            <div class="card">
                <div class="card-body text-center">
                    <div style="font-size: 3rem; margin-bottom: var(--spacing-md);">🚀</div>
                    <h4>Entrega Rápida</h4>
                    <p class="text-secondary mt-sm">Receba seu pedido quentinho em até 40 minutos</p>
                </div>
            </div>
            <div class="card">
                <div class="card-body text-center">
                    <div style="font-size: 3rem; margin-bottom: var(--spacing-md);">📱</div>
                    <h4>Acompanhe Online</h4>
                    <p class="text-secondary mt-sm">Veja o status do seu pedido em tempo real</p>
                </div>
            </div>
            <div class="card">
                <div class="card-body text-center">
                    <div style="font-size: 3rem; margin-bottom: var(--spacing-md);">⭐</div>
                    <h4>Qualidade Premium</h4>
                    <p class="text-secondary mt-sm">Ingredientes frescos e selecionados</p>
                </div>
            </div>
        </div>
</section>

<!-- Contact & Location Section -->
<section class="section" style="background: var(--bg-secondary);">
    <div class="container">
        <div class="section-header" style="margin-bottom: var(--spacing-xl);">
            <div>
                <h2 class="section-title">📍 Onde Estamos</h2>
                <p class="section-subtitle">Venha nos visitar ou peça delivery!</p>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--spacing-xl); align-items: start;">
            <!-- Contact Info -->
            <div class="card" style="height: 100%;">
                <div class="card-body" style="padding: var(--spacing-xl);">
                    <h3 style="margin-bottom: var(--spacing-lg);">🏪
                        <?= e(setting('business_name', 'Delícias da Fran')) ?>
                    </h3>

                    <div style="margin-bottom: var(--spacing-lg);">
                        <div
                            style="display: flex; align-items: flex-start; gap: var(--spacing-md); margin-bottom: var(--spacing-md);">
                            <span style="font-size: 1.5rem;">📍</span>
                            <div>
                                <strong>Endereço</strong>
                                <p class="text-secondary"><?= e(setting('address', 'Seu endereço aqui')) ?></p>
                            </div>
                        </div>

                        <div
                            style="display: flex; align-items: flex-start; gap: var(--spacing-md); margin-bottom: var(--spacing-md);">
                            <span style="font-size: 1.5rem;">📞</span>
                            <div>
                                <strong>Telefone</strong>
                                <p class="text-secondary">
                                    <a href="tel:<?= preg_replace('/[^0-9]/', '', setting('phone', '(61) 99193-0671')) ?>"
                                        style="color: var(--accent-primary);">
                                        <?= e(setting('phone', '(61) 99193-0671')) ?>
                                    </a>
                                </p>
                            </div>
                        </div>

                        <div
                            style="display: flex; align-items: flex-start; gap: var(--spacing-md); margin-bottom: var(--spacing-md);">
                            <span style="font-size: 1.5rem;">🕐</span>
                            <div>
                                <strong>Horário</strong>
                                <p class="text-secondary"><?= e(setting('hours', '11:00 - 22:00')) ?></p>
                            </div>
                        </div>

                        <div style="display: flex; align-items: flex-start; gap: var(--spacing-md);">
                            <span style="font-size: 1.5rem;">📱</span>
                            <div>
                                <strong>WhatsApp</strong>
                                <p>
                                    <a href="https://wa.me/<?= setting('whatsapp', '5561991930671') ?>" target="_blank"
                                        class="btn btn-primary" style="margin-top: var(--spacing-sm);">
                                        💬 Chamar no WhatsApp
                                    </a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Map -->
            <div class="card" style="height: 100%; overflow: hidden;">
                <?php
                $mapEmbed = setting('map_embed', '');
                if (!empty($mapEmbed)):
                    // Extract src from iframe if full embed code
                    if (preg_match('/src=["\']([^"\']+)["\']/', $mapEmbed, $matches)) {
                        $mapSrc = $matches[1];
                    } else {
                        $mapSrc = $mapEmbed;
                    }
                    ?>
                    <iframe src="<?= e($mapSrc) ?>" width="100%" height="400"
                        style="border:0; border-radius: var(--radius-lg);" allowfullscreen="" loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                <?php else: ?>
                    <div class="card-body text-center" style="padding: var(--spacing-2xl);">
                        <div style="font-size: 4rem; margin-bottom: var(--spacing-md);">🗺️</div>
                        <p class="text-secondary">Mapa ainda não configurado</p>
                        <small class="text-muted">Configure nas Configurações do Admin</small>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer
    style="background: var(--bg-secondary); padding: var(--spacing-2xl) 0; border-top: 1px solid var(--border-color);">
    <div class="container text-center">
        <div class="logo" style="justify-content: center; margin-bottom: var(--spacing-lg);">
            <img src="<?= asset('img/logo.png') ?>" alt="<?= e(setting('business_name', 'Delícias da Fran')) ?>"
                style="height: 60px;">
        </div>
        <p class="text-secondary mb-md"><?= e(setting('address', 'Seu endereço aqui')) ?></p>
        <p class="text-secondary mb-md">Horário: <?= e(setting('hours', '11:00 - 22:00')) ?></p>
        <p class="text-secondary">
            <a
                href="tel:<?= preg_replace('/[^0-9]/', '', setting('phone', '(61) 99193-0671')) ?>"><?= e(setting('phone', '(61) 99193-0671')) ?></a>
        </p>
        <p class="text-muted mt-lg" style="font-size: 0.875rem;">
            © <?= date('Y') ?> <?= e(setting('business_name', 'Delícias da Fran')) ?>. Todos os direitos reservados.
        </p>
    </div>
</footer>

<script>
    // Listen for add-to-cart events
    document.addEventListener('alpine:init', () => {
        Alpine.data('app', () => ({
            ...app()
        }));
    });

    window.addEventListener('add-to-cart', (e) => {
        const appInstance = document.body._x_dataStack?.[0];
        if (appInstance && appInstance.addToCart) {
            appInstance.addToCart(e.detail);
        }
    });
</script>

<?php
$content = ob_get_clean();
$title = 'Início';
require VIEWS_PATH . '/layouts/app.php';
?>