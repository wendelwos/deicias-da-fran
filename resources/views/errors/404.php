<?php
// 404 Error Page
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página não encontrada | Delícias da Fran</title>
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
</head>

<body
    style="min-height: 100vh; display: flex; align-items: center; justify-content: center; background: var(--bg-base);">
    <div class="text-center" style="padding: 2rem;">
        <div style="font-size: 6rem; margin-bottom: 1rem;">🍝</div>
        <h1 style="font-size: 4rem; color: var(--accent-primary); margin-bottom: 0.5rem;">404</h1>
        <p style="font-size: 1.25rem; color: var(--text-secondary); margin-bottom: 2rem;">
            Ops! Esta página não foi encontrada.
        </p>
        <a href="<?= url('/') ?>" class="btn btn-primary btn-lg">
            ← Voltar ao Início
        </a>
    </div>
</body>

</html>