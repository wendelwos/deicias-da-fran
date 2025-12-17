<?php
// Admin Products List
ob_start();
?>

<div>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--spacing-xl);">
        <div>
            <h1>📦 Produtos</h1>
            <p class="text-secondary">Gerencie os produtos do cardápio</p>
        </div>
        <div style="display: flex; gap: var(--spacing-md);">
            <a href="<?= url('admin/categorias') ?>" class="btn btn-secondary">
                📁 Categorias
            </a>
            <a href="<?= url('admin/produtos/novo') ?>" class="btn btn-primary">
                + Novo Produto
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

    <!-- Filter by Category -->
    <div class="card mb-xl">
        <div class="card-body">
            <form method="GET" action="<?= url('admin/produtos') ?>"
                style="display: flex; gap: var(--spacing-md); align-items: flex-end;">
                <div class="form-group" style="margin-bottom: 0; flex: 1;">
                    <label class="form-label">Filtrar por Categoria</label>
                    <select name="category" class="form-select">
                        <option value="">Todas as Categorias</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= $currentCategory == $cat['id'] ? 'selected' : '' ?>>
                                <?= e($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Filtrar</button>
            </form>
        </div>
    </div>

    <!-- Products Table -->
    <div class="card">
        <div class="card-body">
            <?php if (empty($products)): ?>
                <div class="empty-state">
                    <div class="empty-state-icon">📦</div>
                    <p>Nenhum produto encontrado</p>
                    <a href="<?= url('admin/produtos/novo') ?>" class="btn btn-primary mt-lg">Criar Produto</a>
                </div>
            <?php else: ?>
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid var(--border-color);">
                            <th style="text-align: left; padding: var(--spacing-md);">Produto</th>
                            <th style="text-align: left; padding: var(--spacing-md);">Categoria</th>
                            <th style="text-align: left; padding: var(--spacing-md);">Preço</th>
                            <th style="text-align: left; padding: var(--spacing-md);">Combo</th>
                            <th style="text-align: center; padding: var(--spacing-md);">Status</th>
                            <th style="text-align: center; padding: var(--spacing-md);">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                            <tr style="border-bottom: 1px solid var(--border-color);">
                                <td style="padding: var(--spacing-md);">
                                    <div style="font-weight: 600;"><?= e($product['name']) ?></div>
                                    <?php if ($product['is_buildable']): ?>
                                        <span class="badge"
                                            style="background: rgba(255, 159, 28, 0.2); color: var(--accent-secondary); font-size: 0.65rem;">MONTÁVEL</span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: var(--spacing-md); color: var(--text-secondary);">
                                    <?= e($product['category_name']) ?>
                                </td>
                                <td style="padding: var(--spacing-md);">
                                    <span style="font-weight: 600;"><?= money($product['price']) ?></span>
                                    <?php if ($product['price_combo']): ?>
                                        <br><span class="text-muted" style="font-size: 0.75rem;">Combo:
                                            <?= money($product['price_combo']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: var(--spacing-md);">
                                    <?= $product['is_combo'] ? '✅' : '-' ?>
                                </td>
                                <td style="padding: var(--spacing-md); text-align: center;">
                                    <button onclick="toggleProductStatus(<?= $product['id'] ?>, this)"
                                        class="btn btn-sm <?= $product['active'] ? 'badge-pronto' : 'badge-cancelado' ?>"
                                        style="cursor: pointer; min-width: 80px;"
                                        data-active="<?= $product['active'] ? '1' : '0' ?>">
                                        <?= $product['active'] ? 'ATIVO' : 'INATIVO' ?>
                                    </button>
                                </td>
                                <td style="padding: var(--spacing-md); text-align: center;">
                                    <div style="display: flex; gap: var(--spacing-sm); justify-content: center;">
                                        <a href="<?= url('admin/produtos/' . $product['id'] . '/editar') ?>"
                                            class="btn btn-sm btn-secondary">
                                            ✏️ Editar
                                        </a>
                                        <form method="POST" action="<?= url('admin/produtos/' . $product['id'] . '/excluir') ?>"
                                            onsubmit="return confirm('Tem certeza que deseja excluir este produto?');">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-danger">🗑️</button>
                                        </form>
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

<script>
    async function toggleProductStatus(productId, btn) {
        const currentActive = btn.dataset.active === '1';
        const newActive = !currentActive;

        btn.disabled = true;
        btn.textContent = 'Aguarde...';

        try {
            const formData = new FormData();
            formData.append('_token', '<?= csrf_token() ?>');

            const res = await fetch('<?= url('admin/produtos/') ?>' + productId + '/toggle', {
                method: 'POST',
                body: formData
            });

            const data = await res.json();

            if (data.success) {
                btn.dataset.active = data.active ? '1' : '0';
                btn.textContent = data.active ? 'ATIVO' : 'INATIVO';
                btn.className = 'btn btn-sm ' + (data.active ? 'badge-pronto' : 'badge-cancelado');
                btn.style.cursor = 'pointer';
                btn.style.minWidth = '80px';
            } else {
                alert('Erro: ' + (data.message || 'Erro ao atualizar'));
                btn.textContent = currentActive ? 'ATIVO' : 'INATIVO';
            }
        } catch (err) {
            console.error(err);
            alert('Erro de conexão');
            btn.textContent = currentActive ? 'ATIVO' : 'INATIVO';
        }

        btn.disabled = false;
    }
</script>

<?php
$content = ob_get_clean();
$title = 'Produtos';
require VIEWS_PATH . '/layouts/admin.php';
?>