<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Painel' ?> | Delícias da Fran</title>

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

        .admin-layout {
            display: flex;
            min-height: 100vh;
        }

        .admin-sidebar {
            width: 250px;
            background: var(--bg-secondary);
            border-right: 1px solid var(--border-color);
            padding: var(--spacing-lg);
            display: flex;
            flex-direction: column;
        }

        .admin-main {
            flex: 1;
            padding: var(--spacing-xl);
            overflow-y: auto;
        }

        .admin-nav {
            display: flex;
            flex-direction: column;
            gap: var(--spacing-sm);
            margin-top: var(--spacing-xl);
        }

        .admin-nav-link {
            display: flex;
            align-items: center;
            gap: var(--spacing-sm);
            padding: var(--spacing-md);
            border-radius: var(--radius-lg);
            color: var(--text-secondary);
            transition: all var(--transition-fast);
        }

        .admin-nav-link:hover,
        .admin-nav-link.active {
            background: var(--bg-card);
            color: var(--text-primary);
        }

        .admin-nav-link.active {
            border-left: 3px solid var(--accent-primary);
        }

        .admin-user {
            margin-top: auto;
            padding-top: var(--spacing-lg);
            border-top: 1px solid var(--border-color);
        }

        @media (max-width: 768px) {
            .admin-sidebar {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <a href="<?= url('/') ?>" class="logo" style="display: flex; align-items: center; gap: var(--spacing-sm);">
                <img src="<?= asset('img/logo.png') ?>" alt="Delícias da Fran" style="height: 50px;">
            </a>

            <nav class="admin-nav">
                <a href="<?= url('admin/pedidos') ?>"
                    class="admin-nav-link <?= str_contains($_SERVER['REQUEST_URI'], 'admin/pedidos') ? 'active' : '' ?>">
                    📋 Pedidos
                </a>
                <?php if (hasRole('admin')): ?>
                    <a href="<?= url('admin/produtos') ?>"
                        class="admin-nav-link <?= str_contains($_SERVER['REQUEST_URI'], 'admin/produtos') ? 'active' : '' ?>">
                        📦 Produtos
                    </a>
                    <a href="<?= url('admin/categorias') ?>"
                        class="admin-nav-link <?= str_contains($_SERVER['REQUEST_URI'], 'admin/categorias') ? 'active' : '' ?>">
                        📁 Categorias
                    </a>
                    <a href="<?= url('admin/financeiro') ?>"
                        class="admin-nav-link <?= str_contains($_SERVER['REQUEST_URI'], 'admin/financeiro') ? 'active' : '' ?>">
                        💰 Financeiro
                    </a>
                    <a href="<?= url('admin/configuracoes') ?>"
                        class="admin-nav-link <?= str_contains($_SERVER['REQUEST_URI'], 'admin/configuracoes') ? 'active' : '' ?>">
                        ⚙️ Configurações
                    </a>
                <?php endif; ?>
                <a href="<?= url('cozinha') ?>"
                    class="admin-nav-link <?= str_contains($_SERVER['REQUEST_URI'], 'cozinha') ? 'active' : '' ?>">
                    👨‍🍳 Cozinha (KDS)
                </a>
                <a href="<?= url('ready') ?>" class="admin-nav-link" target="_blank">
                    📺 Tela Prontos
                </a>
            </nav>

            <div class="admin-user">
                <?php if ($user = auth()): ?>
                    <p style="color: var(--text-secondary); font-size: 0.875rem;">Logado como</p>
                    <p style="font-weight: 600;"><?= e($user['name']) ?></p>
                    <a href="<?= url('logout') ?>" class="btn btn-secondary btn-sm btn-block mt-md">Sair</a>
                <?php endif; ?>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="admin-main">
            <?php if (isset($content)): ?>
                <?= $content ?>
            <?php endif; ?>
        </main>
    </div>
</body>

</html>