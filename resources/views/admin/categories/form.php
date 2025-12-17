<?php
// Admin Category Form (Create/Edit)
$isEdit = isset($category) && $category;
$title = $isEdit ? 'Editar Categoria' : 'Nova Categoria';
ob_start();
?>

<div>
    <div style="margin-bottom: var(--spacing-xl);">
        <a href="<?= url('admin/categorias') ?>" class="btn btn-secondary btn-sm mb-md">
            ← Voltar às Categorias
        </a>
        <h1><?= $isEdit ? '✏️ Editar Categoria' : '📁 Nova Categoria' ?></h1>
    </div>

    <div class="card" style="max-width: 600px;">
        <div class="card-body">
            <form method="POST"
                action="<?= $isEdit ? url('admin/categorias/' . $category['id']) : url('admin/categorias') ?>">
                <?= csrf_field() ?>

                <div class="form-group">
                    <label class="form-label" for="name">Nome da Categoria *</label>
                    <input type="text" id="name" name="name" value="<?= e($category['name'] ?? old('name')) ?>"
                        class="form-input" required>
                    <?php if ($err = error('name')): ?>
                        <p class="form-error"><?= e($err) ?></p>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label class="form-label" for="description">Descrição</label>
                    <textarea id="description" name="description" class="form-textarea"
                        rows="3"><?= e($category['description'] ?? old('description')) ?></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label" for="display_order">Ordem de Exibição</label>
                    <input type="number" id="display_order" name="display_order" min="0"
                        value="<?= $category['display_order'] ?? old('display_order', '0') ?>" class="form-input"
                        style="width: 100px;">
                    <small class="text-muted">Categorias com menor número aparecem primeiro</small>
                </div>

                <button type="submit" class="btn btn-primary btn-lg">
                    <?= $isEdit ? 'Salvar Alterações' : 'Criar Categoria' ?>
                </button>
            </form>
        </div>
    </div>
</div>

<?php
// Clear session
unset($_SESSION['errors'], $_SESSION['old_input']);

$content = ob_get_clean();
require VIEWS_PATH . '/layouts/admin.php';
?>