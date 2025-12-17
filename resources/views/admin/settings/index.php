<?php
// Admin Settings Page
ob_start();
?>

<div>
    <div style="margin-bottom: var(--spacing-xl);">
        <h1>⚙️ Configurações</h1>
        <p class="text-secondary">Personalize seu sistema</p>
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

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--spacing-xl);">
        <!-- Logo Settings -->
        <div class="card">
            <div class="card-body">
                <h3 class="mb-lg">📷 Logo do Sistema</h3>

                <!-- Current Logo Preview -->
                <div
                    style="background: var(--bg-secondary); padding: var(--spacing-lg); border-radius: var(--radius-lg); margin-bottom: var(--spacing-lg); text-align: center;">
                    <p class="text-muted mb-md">Logo Atual:</p>
                    <img src="<?= asset('img/logo.png') ?>?v=<?= time() ?>" alt="Logo atual"
                        style="max-height: 120px; max-width: 100%;">
                </div>

                <!-- Upload Form -->
                <form method="POST" action="<?= url('admin/configuracoes/logo') ?>" enctype="multipart/form-data">
                    <?= csrf_field() ?>

                    <div class="form-group">
                        <label class="form-label">Nova Logo</label>
                        <input type="file" name="logo" accept="image/jpeg,image/png,image/webp,image/gif"
                            class="form-input" required onchange="previewLogo(this)">
                        <small class="text-muted">JPG, PNG, WebP ou GIF. Máximo 5MB. Recomendado: fundo
                            transparente.</small>
                    </div>

                    <!-- New Logo Preview -->
                    <div id="logoPreview"
                        style="display: none; background: var(--bg-secondary); padding: var(--spacing-lg); border-radius: var(--radius-lg); margin-bottom: var(--spacing-lg); text-align: center;">
                        <p class="text-muted mb-md">Nova logo:</p>
                        <img id="previewImg" src="" style="max-height: 120px; max-width: 100%;">
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">
                        💾 Salvar Logo
                    </button>
                </form>
            </div>
        </div>

        <!-- Business Info -->
        <div class="card">
            <div class="card-body">
                <h3 class="mb-lg">🏪 Informações do Negócio</h3>

                <form method="POST" action="<?= url('admin/configuracoes/salvar') ?>">
                    <?= csrf_field() ?>

                    <div class="form-group">
                        <label class="form-label">Nome do Negócio</label>
                        <input type="text" name="business_name" class="form-input"
                            value="<?= e(setting('business_name', 'Delícias da Fran')) ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">WhatsApp (só números)</label>
                        <input type="text" name="whatsapp" class="form-input"
                            value="<?= e(setting('whatsapp', '5561991930671')) ?>" placeholder="5561999999999" required>
                        <small class="text-muted">Inclua código do país e DDD, sem espaços</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Telefone (exibição)</label>
                        <input type="text" name="phone" class="form-input"
                            value="<?= e(setting('phone', '(61) 99193-0671')) ?>" placeholder="(61) 99999-9999">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Endereço</label>
                        <input type="text" name="address" class="form-input"
                            value="<?= e(setting('address', 'Seu endereço aqui')) ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Horário de Funcionamento</label>
                        <input type="text" name="hours" class="form-input"
                            value="<?= e(setting('hours', '11:00 - 22:00')) ?>" placeholder="11:00 - 22:00">
                    </div>

                    <div class="form-group">
                        <label class="form-label">📍 Localização Google Maps</label>
                        <textarea name="map_embed" class="form-textarea" rows="4"
                            placeholder="Cole aqui o código embed do Google Maps..."><?= e(setting('map_embed', '')) ?></textarea>
                        <small class="text-muted">
                            Para obter: vá ao Google Maps → Pesquise seu endereço → Clique em "Compartilhar" →
                            "Incorporar um mapa" → Copie o código &lt;iframe&gt;
                        </small>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">
                        💾 Salvar Configurações
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function previewLogo(input) {
        const file = input.files[0];
        if (!file) return;

        if (file.size > 5 * 1024 * 1024) {
            alert('Arquivo muito grande! Máximo 5MB.');
            input.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = (e) => {
            document.getElementById('previewImg').src = e.target.result;
            document.getElementById('logoPreview').style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
</script>

<?php
$content = ob_get_clean();
$title = 'Configurações';
require VIEWS_PATH . '/layouts/admin.php';
?>