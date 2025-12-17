<?php
/**
 * Settings Controller - System Configuration
 */

class SettingsController
{
    public function __construct()
    {
        if (!auth()) {
            redirect('login');
            return;
        }

        // Only admin can access
        if (!hasRole('admin')) {
            redirect('cozinha');
            return;
        }
    }

    /**
     * Show settings page
     */
    public function index(): void
    {
        view('admin/settings/index');
    }

    /**
     * Update logo
     */
    public function updateLogo(): void
    {
        if (empty($_FILES['logo']['tmp_name'])) {
            flash('error', 'Selecione uma imagem para o logo.');
            redirect('admin/configuracoes');
            return;
        }

        $file = $_FILES['logo'];

        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        if (!in_array($file['type'], $allowedTypes)) {
            flash('error', 'Formato inválido. Use JPG, PNG, WebP ou GIF.');
            redirect('admin/configuracoes');
            return;
        }

        // Check file size (5MB max)
        if ($file['size'] > 5 * 1024 * 1024) {
            flash('error', 'Arquivo muito grande. Máximo 5MB.');
            redirect('admin/configuracoes');
            return;
        }

        // Backup old logo
        $logoPath = ROOT_PATH . '/assets/img/logo.png';
        if (file_exists($logoPath)) {
            $backupPath = ROOT_PATH . '/assets/img/logo_backup_' . time() . '.png';
            copy($logoPath, $backupPath);
        }

        // Save new logo
        if (move_uploaded_file($file['tmp_name'], $logoPath)) {
            flash('success', 'Logo atualizada com sucesso!');
        } else {
            flash('error', 'Erro ao salvar a imagem. Verifique permissões.');
        }

        redirect('admin/configuracoes');
    }

    /**
     * Update business settings
     */
    public function updateSettings(): void
    {
        $fields = ['business_name', 'whatsapp', 'address', 'hours', 'phone', 'map_embed'];

        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                setSetting($field, trim($_POST[$field]));
            }
        }

        flash('success', 'Configurações atualizadas com sucesso!');
        redirect('admin/configuracoes');
    }
}
