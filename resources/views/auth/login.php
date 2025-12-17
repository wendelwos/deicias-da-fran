<?php
// Login Page View
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Delícias da Fran</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;700;800&display=swap"
        rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">

    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--bg-base);
        }

        .login-container {
            width: 100%;
            max-width: 400px;
            padding: var(--spacing-lg);
        }

        .login-header {
            text-align: center;
            margin-bottom: var(--spacing-2xl);
        }

        .login-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: var(--spacing-sm);
            margin-bottom: var(--spacing-lg);
        }

        .login-logo-icon {
            width: 50px;
            height: 50px;
            background: var(--gradient-primary);
            border-radius: var(--radius-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
        }

        .login-logo-text {
            font-family: var(--font-display);
            font-size: 1.5rem;
            font-weight: 700;
        }

        .login-logo-text span {
            color: var(--accent-primary);
        }

        .login-title {
            font-size: 1.5rem;
            margin-bottom: var(--spacing-sm);
        }

        .login-subtitle {
            color: var(--text-secondary);
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-header">
            <div class="login-logo">
                <img src="<?= asset('img/logo.png') ?>" alt="Delícias da Fran" style="height: 80px;">
            </div>
            <h1 class="login-title">Acesso Restrito</h1>
            <p class="login-subtitle">Área da cozinha e administração</p>
        </div>

        <div class="card">
            <div class="card-body">
                <form method="POST" action="<?= url('login') ?>">
                    <?= csrf_field() ?>

                    <div class="form-group">
                        <label class="form-label" for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?= old('email') ?>" class="form-input"
                            placeholder="seu@email.com" required autofocus>
                        <?php if ($err = error('email')): ?>
                            <p class="form-error"><?= e($err) ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password">Senha</label>
                        <input type="password" id="password" name="password" class="form-input" placeholder="••••••••"
                            required>
                        <?php if ($err = error('password')): ?>
                            <p class="form-error"><?= e($err) ?></p>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block btn-lg">
                        Entrar
                    </button>
                </form>
            </div>
        </div>

        <p class="text-center text-muted mt-xl" style="font-size: 0.875rem;">
            <a href="<?= url('/') ?>">← Voltar ao site</a>
        </p>
    </div>
</body>

</html>
<?php
// Clear session errors after displaying
unset($_SESSION['errors'], $_SESSION['old_input']);
?>