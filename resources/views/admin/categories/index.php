<?php
// Admin Categories List
ob_start();
?>

<div>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--spacing-xl);">
        <div>
            <h1>📁 Categorias</h1>
            <p class="text-secondary">Gerencie as categorias do cardápio</p>
        </div>
        <div style="display: flex; gap: var(--spacing-md);">
            <a href="<?= url('admin/produtos') ?>" class="btn btn-secondary">
                ← Voltar aos Produtos
            </a>
            <a href="<?= url('admin/categorias/nova') ?>" class="btn btn-primary">
                + Nova Categoria
            </a>
        </div>
    </div>

    <?php if ($msg = getFlash('success')): ?>
        <div
            style="background: rgba(34, 197, 94, 0.2); border: 1px solid var(--accent-green); padding: var(--spacing-md); border-radius: var(--radius-lg); margin-bottom: var(--spacing-lg);">
            ✅ <?= e($msg) ?>
        </div>
    <?php endif; ?>

    <?php if ($msg = getFlash('error')): ?>
        <div
            style="background: rgba(239, 68, 68, 0.2); border: 1px solid var(--accent-red); padding: var(--spacing-md); border-radius: var(--radius-lg); margin-bottom: var(--spacing-lg);">
            ❌ <?= e($msg) ?>
        </div>
    <?php endif; ?>

    <!-- Categories Table -->
    <div class="card">
        <div class="card-body">
            <?php if (empty($categories)): ?>
                <div class="empty-state">
                    <div class="empty-state-icon">📁</div>
                    <p>Nenhuma categoria encontrada</p>
                    <a href="<?= url('admin/categorias/nova') ?>" class="btn btn-primary mt-lg">Criar Categoria</a>
                </div>
            <?php else: ?>
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid var(--border-color);">
                            <th style="text-align: left; padding: var(--spacing-md);">Ordem</th>
                            <th style="text-align: left; padding: var(--spacing-md);">Categoria</th>
                            <th style="text-align: left; padding: var(--spacing-md);">Slug</th>
                            <th style="text-align: center; padding: var(--spacing-md);">Produtos</th>
                            <th style="text-align: center; padding: var(--spacing-md);">Status</th>
                            <th style="text-align: center; padding: var(--spacing-md);">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $cat): ?>
                            <tr style="border-bottom: 1px solid var(--border-color);">
                                <td style="padding: var(--spacing-md); color: var(--text-muted); width: 60px;">
                                    <?= $cat['display_order'] ?>
                                </td>
                                <td style="padding: var(--spacing-md);">
                                    <div style="font-weight: 600;"><?= e($cat['name']) ?></div>
                                    <?php if ($cat['description']): ?>
                                        <div class="text-muted" style="font-size: 0.75rem;"><?= e($cat['description']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td
                                    style="padding: var(--spacing-md); color: var(--text-muted); font-family: monospace; font-size: 0.875rem;">
                                    <?= e($cat['slug']) ?>
                                </td>
                                <td style="padding: var(--spacing-md); text-align: center;">
                                    <span class="badge" style="background: var(--bg-elevated);">
                                        <?= $cat['product_count'] ?> produtos
                                    </span>
                                </td>
                                <td style="padding: var(--spacing-md); text-align: center;">
                                    <?php if ($cat['active']): ?>
                                        <span class="badge badge-pronto">Ativa</span>
                                    <?php else: ?>
                                        <span class="badge badge-cancelado">Inativa</span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: var(--spacing-md); text-align: center;">
                                    <div style="display: flex; gap: var(--spacing-sm); justify-content: center;">
                                        <a href="<?= url('admin/categorias/' . $cat['id'] . '/editar') ?>"
                                            class="btn btn-sm btn-secondary">
                                            ✏️ Editar
                                        </a>
                                        <?php if ($cat['product_count'] == 0): ?>
                                            <form method="POST" action="<?= url('admin/categorias/' . $cat['id'] . '/excluir') ?>"
                                                onsubmit="return confirm('Tem certeza que deseja excluir esta categoria?');">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-sm btn-danger">🗑️</button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
$title = 'Categorias';
require VIEWS_PATH . '/layouts/admin.php';
?>