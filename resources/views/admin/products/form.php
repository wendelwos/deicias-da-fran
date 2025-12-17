<?php
// Admin Product Form (Create/Edit)
$isEdit = isset($product) && $product;
$title = $isEdit ? 'Editar Produto' : 'Novo Produto';
ob_start();
?>

<div>
    <div style="margin-bottom: var(--spacing-xl);">
        <a href="<?= url('admin/produtos') ?>" class="btn btn-secondary btn-sm mb-md">
            ← Voltar aos Produtos
        </a>
        <h1><?= $isEdit ? '✏️ Editar Produto' : '📦 Novo Produto' ?></h1>
    </div>
    
    <form method="POST" action="<?= $isEdit ? url('admin/produtos/' . $product['id']) : url('admin/produtos') ?>" enctype="multipart/form-data">
        <?= csrf_field() ?>
        
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: var(--spacing-xl);">
            <!-- Main Form -->
            <div>
                <div class="card mb-xl">
                    <div class="card-body">
                        <h3 class="mb-lg">Informações Básicas</h3>
                        
                        <div class="form-group">
                            <label class="form-label" for="name">Nome do Produto *</label>
                            <input type="text" id="name" name="name" 
                                   value="<?= e($product['name'] ?? old('name')) ?>"
                                   class="form-input" required>
                            <?php if ($err = error('name')): ?>
                            <p class="form-error"><?= e($err) ?></p>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="category_id">Categoria *</label>
                            <select id="category_id" name="category_id" class="form-select" required>
                                <option value="">Selecione...</option>
                                <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" 
                                        <?= ($product['category_id'] ?? old('category_id')) == $cat['id'] ? 'selected' : '' ?>>
                                    <?= e($cat['name']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="description">Descrição</label>
                            <textarea id="description" name="description" class="form-textarea" rows="3"><?= e($product['description'] ?? old('description')) ?></textarea>
                        </div>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--spacing-md);">
                            <div class="form-group">
                                <label class="form-label" for="price">Preço (R$) *</label>
                                <input type="number" id="price" name="price" step="0.01" min="0"
                                       value="<?= $product['price'] ?? old('price', '0') ?>"
                                       class="form-input" required>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label" for="price_combo">Preço Combo (R$)</label>
                                <input type="number" id="price_combo" name="price_combo" step="0.01" min="0"
                                       value="<?= $product['price_combo'] ?? old('price_combo', '') ?>"
                                       class="form-input">
                                <small class="text-muted">Deixe vazio se não tiver combo</small>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="display_order">Ordem de Exibição</label>
                            <input type="number" id="display_order" name="display_order" min="0"
                                   value="<?= $product['display_order'] ?? old('display_order', '0') ?>"
                                   class="form-input" style="width: 100px;">
                        </div>
                    </div>
                </div>
                
                <!-- Product Options (for buildable products) -->
                <div class="card" x-data="{ isBuildable: <?= ($product['is_buildable'] ?? false) ? 'true' : 'false' ?> }">
                    <div class="card-body">
                        <h3 class="mb-lg">Opções do Produto</h3>
                        
                        <div class="form-group">
                            <label style="display: flex; align-items: center; gap: var(--spacing-sm); cursor: pointer;">
                                <input type="checkbox" name="is_buildable" value="1"
                                       <?= ($product['is_buildable'] ?? old('is_buildable')) ? 'checked' : '' ?>
                                       @change="isBuildable = $event.target.checked"
                                       style="width: 20px; height: 20px;">
                                <span>Produto Montável (cliente escolhe opções)</span>
                            </label>
                            <small class="text-muted">Ex: Macarrão (escolhe massa, ingredientes), Suco (escolhe sabor)</small>
                        </div>
                        
                        <div x-show="isBuildable" x-transition style="margin-top: var(--spacing-lg);">
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--spacing-md); margin-bottom: var(--spacing-lg);">
                                <div class="form-group" style="margin-bottom: 0;">
                                    <label class="form-label">Máx. Ingredientes</label>
                                    <input type="number" name="max_ingredients" min="0"
                                           value="<?= $product['max_ingredients'] ?? 0 ?>"
                                           class="form-input">
                                    <small class="text-muted">0 = sem limite</small>
                                </div>
                                <div class="form-group" style="margin-bottom: 0;">
                                    <label class="form-label">Máx. Temperos</label>
                                    <input type="number" name="max_seasonings" min="0"
                                           value="<?= $product['max_seasonings'] ?? 0 ?>"
                                           class="form-input">
                                </div>
                            </div>
                            
                            <!-- Dynamic Options -->
                            <div x-data="productOptions(<?= json_encode($options ?? []) ?>)">
                                <!-- Massas -->
                                <div class="mb-lg" style="padding: var(--spacing-md); background: var(--bg-card); border-radius: var(--radius-lg);">
                                    <label class="form-label">🍝 Massas (opcional)</label>
                                    <template x-for="(item, index) in massas" :key="'massa-' + index">
                                        <div style="display: flex; gap: var(--spacing-sm); margin-bottom: var(--spacing-sm);">
                                            <input type="text" :name="'options[massa][]'" x-model="massas[index]"
                                                   class="form-input" placeholder="Ex: Espaguete">
                                            <button type="button" @click="massas.splice(index, 1)" class="btn btn-sm btn-danger">✕</button>
                                        </div>
                                    </template>
                                    <button type="button" @click="massas.push('')" class="btn btn-sm btn-secondary">+ Adicionar Massa</button>
                                </div>
                                
                                <!-- Sabores (for juices) -->
                                <div class="mb-lg" style="padding: var(--spacing-md); background: var(--bg-card); border-radius: var(--radius-lg);">
                                    <label class="form-label">🍹 Sabores (opcional)</label>
                                    <template x-for="(item, index) in sabores" :key="'sabor-' + index">
                                        <div style="display: flex; gap: var(--spacing-sm); margin-bottom: var(--spacing-sm);">
                                            <input type="text" :name="'options[sabor][]'" x-model="sabores[index]"
                                                   class="form-input" placeholder="Ex: Maracujá">
                                            <button type="button" @click="sabores.splice(index, 1)" class="btn btn-sm btn-danger">✕</button>
                                        </div>
                                    </template>
                                    <button type="button" @click="sabores.push('')" class="btn btn-sm btn-secondary">+ Adicionar Sabor</button>
                                </div>
                                
                                <!-- Ingredientes -->
                                <div class="mb-lg" style="padding: var(--spacing-md); background: var(--bg-card); border-radius: var(--radius-lg);">
                                    <label class="form-label">🥓 Ingredientes (opcional)</label>
                                    <template x-for="(item, index) in ingredientes" :key="'ing-' + index">
                                        <div style="display: flex; gap: var(--spacing-sm); margin-bottom: var(--spacing-sm);">
                                            <input type="text" :name="'options[ingrediente][]'" x-model="ingredientes[index]"
                                                   class="form-input" placeholder="Ex: Bacon">
                                            <button type="button" @click="ingredientes.splice(index, 1)" class="btn btn-sm btn-danger">✕</button>
                                        </div>
                                    </template>
                                    <button type="button" @click="ingredientes.push('')" class="btn btn-sm btn-secondary">+ Adicionar Ingrediente</button>
                                </div>
                                
                                <!-- Temperos -->
                                <div style="padding: var(--spacing-md); background: var(--bg-card); border-radius: var(--radius-lg);">
                                    <label class="form-label">🌿 Temperos (opcional)</label>
                                    <template x-for="(item, index) in temperos" :key="'temp-' + index">
                                        <div style="display: flex; gap: var(--spacing-sm); margin-bottom: var(--spacing-sm);">
                                            <input type="text" :name="'options[tempero][]'" x-model="temperos[index]"
                                                   class="form-input" placeholder="Ex: Orégano">
                                            <button type="button" @click="temperos.splice(index, 1)" class="btn btn-sm btn-danger">✕</button>
                                        </div>
                                    </template>
                                    <button type="button" @click="temperos.push('')" class="btn btn-sm btn-secondary">+ Adicionar Tempero</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div>
                <!-- Image Upload Section -->
                <div class="card mb-xl">
                    <div class="card-body">
                        <h4 class="mb-lg">📷 Imagem do Produto</h4>
                        
                        <!-- Current Image Preview -->
                        <div class="image-preview-container" style="margin-bottom: var(--spacing-md);">
                            <?php if (!empty($product['image'])): ?>
                            <div id="currentImage">
                                <img src="<?= asset('img/' . $product['image']) ?>" 
                                     alt="Imagem atual" 
                                     style="width: 100%; border-radius: var(--radius-lg); margin-bottom: var(--spacing-sm);">
                                <p class="text-muted text-sm">Imagem atual: <?= e($product['image']) ?></p>
                            </div>
                            <?php endif; ?>
                            
                            <!-- New Image Preview -->
                            <div id="imagePreview" style="display: none;">
                                <img id="previewImg" src="" 
                                     style="width: 100%; border-radius: var(--radius-lg); margin-bottom: var(--spacing-sm);">
                                <p class="text-muted text-sm">Nova imagem selecionada</p>
                            </div>
                        </div>
                        
                        <!-- Upload Input -->
                        <div class="form-group">
                            <label class="form-label">Selecionar Imagem</label>
                            <input type="file" name="product_image" id="productImage" 
                                   accept="image/jpeg,image/png,image/webp"
                                   class="form-input"
                                   onchange="previewImage(this)">
                            <small class="text-muted">JPG, PNG ou WebP. Máx 5MB.</small>
                        </div>
                    </div>
                </div>
                
                <div class="card mb-xl">
                    <div class="card-body">
                        <h4 class="mb-lg">Publicação</h4>
                        
                        <div class="form-group">
                            <label style="display: flex; align-items: center; gap: var(--spacing-sm); cursor: pointer;">
                                <input type="checkbox" name="active" value="1"
                                       <?= ($product['active'] ?? true) ? 'checked' : '' ?>
                                       style="width: 20px; height: 20px;">
                                <span>Produto Ativo</span>
                            </label>
                        </div>
                        
                        <div class="form-group">
                            <label style="display: flex; align-items: center; gap: var(--spacing-sm); cursor: pointer;">
                                <input type="checkbox" name="is_combo" value="1"
                                       <?= ($product['is_combo'] ?? old('is_combo')) ? 'checked' : '' ?>
                                       style="width: 20px; height: 20px;">
                                <span>É um Combo</span>
                            </label>
                            <small class="text-muted">Exibe destaque no cardápio</small>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-block btn-lg">
                            <?= $isEdit ? 'Salvar Alterações' : 'Criar Produto' ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script>
function productOptions(existingOptions) {
    // Group existing options by type
    const grouped = {};
    (existingOptions || []).forEach(opt => {
        if (!grouped[opt.option_type]) grouped[opt.option_type] = [];
        grouped[opt.option_type].push(opt.name);
    });
    
    return {
        massas: grouped['massa'] || [],
        sabores: grouped['sabor'] || [],
        ingredientes: grouped['ingrediente'] || [],
        temperos: grouped['tempero'] || [],
    };
}

// Simple image preview
function previewImage(input) {
    const file = input.files[0];
    if (!file) return;
    
    // Validate file size (5MB)
    if (file.size > 5 * 1024 * 1024) {
        alert('Arquivo muito grande! Máximo 5MB.');
        input.value = '';
        return;
    }
    
    const reader = new FileReader();
    reader.onload = (e) => {
        document.getElementById('previewImg').src = e.target.result;
        document.getElementById('imagePreview').style.display = 'block';
        
        // Hide current image if exists
        const currentImg = document.getElementById('currentImage');
        if (currentImg) currentImg.style.display = 'none';
    };
    reader.readAsDataURL(file);
}
</script>

<?php
// Clear session
unset($_SESSION['errors'], $_SESSION['old_input']);

$content = ob_get_clean();
require VIEWS_PATH . '/layouts/admin.php';
?>
