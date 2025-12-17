<?php
/**
 * Helper Functions
 */

/**
 * Get configuration value
 */
function config(string $key, $default = null)
{
    static $config = null;

    if ($config === null) {
        $config = require CONFIG_PATH . '/app.php';
    }

    $keys = explode('.', $key);
    $value = $config;

    foreach ($keys as $k) {
        if (!isset($value[$k])) {
            return $default;
        }
        $value = $value[$k];
    }

    return $value;
}

/**
 * Generate asset URL
 */
function asset(string $path): string
{
    return config('url') . '/assets/' . ltrim($path, '/');
}

/**
 * Generate URL
 */
function url(string $path = ''): string
{
    return config('url') . '/' . ltrim($path, '/');
}

/**
 * Render a view
 */
function view(string $name, array $data = []): void
{
    extract($data);

    $viewFile = VIEWS_PATH . '/' . str_replace('.', '/', $name) . '.php';

    if (!file_exists($viewFile)) {
        die("View not found: $name");
    }

    require $viewFile;
}

/**
 * Include a partial view
 */
function partial(string $name, array $data = []): void
{
    extract($data);

    $viewFile = VIEWS_PATH . '/partials/' . str_replace('.', '/', $name) . '.php';

    if (file_exists($viewFile)) {
        require $viewFile;
    }
}

/**
 * Get CSRF token
 */
function csrf_token(): string
{
    return $_SESSION['csrf_token'] ?? '';
}

/**
 * Generate CSRF input field
 */
function csrf_field(): string
{
    return '<input type="hidden" name="_token" value="' . csrf_token() . '">';
}

/**
 * Check if user is authenticated
 */
function auth(): ?array
{
    return $_SESSION['user'] ?? null;
}

/**
 * Check if user has role
 */
function hasRole(string $role): bool
{
    $user = auth();
    return $user && $user['role'] === $role;
}

/**
 * Redirect to URL
 */
function redirect(string $url): void
{
    header("Location: " . url($url));
    exit;
}

/**
 * Return JSON response
 */
function json(array $data, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

/**
 * Get old input value (flash data)
 */
function old(string $key, $default = '')
{
    return $_SESSION['old_input'][$key] ?? $default;
}

/**
 * Get error message
 */
function error(string $key): ?string
{
    return $_SESSION['errors'][$key] ?? null;
}

/**
 * Set flash message
 */
function flash(string $key, $value): void
{
    $_SESSION['flash'][$key] = $value;
}

/**
 * Get flash message
 */
function getFlash(string $key, $default = null)
{
    $value = $_SESSION['flash'][$key] ?? $default;
    unset($_SESSION['flash'][$key]);
    return $value;
}

/**
 * Format currency
 */
function money(float $value): string
{
    return 'R$ ' . number_format($value, 2, ',', '.');
}

/**
 * Sanitize string
 */
function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

/**
 * Generate WhatsApp link
 */
function whatsappLink(string $message): string
{
    $number = config('whatsapp.number');
    $encoded = urlencode($message);
    return "https://wa.me/{$number}?text={$encoded}";
}

/**
 * Time ago helper
 */
function timeAgo(string $datetime): string
{
    $time = strtotime($datetime);
    $diff = time() - $time;

    if ($diff < 60) {
        return 'agora';
    } elseif ($diff < 3600) {
        $mins = floor($diff / 60);
        return $mins . ' min';
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . 'h';
    } else {
        return date('d/m H:i', $time);
    }
}

/**
 * Order status badge color
 */
function statusColor(string $status): string
{
    return match ($status) {
        'RECEBIDO' => 'bg-blue-500',
        'PREPARANDO' => 'bg-yellow-500',
        'PRONTO' => 'bg-green-500',
        'FINALIZADO' => 'bg-gray-500',
        'CANCELADO' => 'bg-red-500',
        default => 'bg-gray-400',
    };
}

/**
 * Order status text in Portuguese
 */
function statusText(string $status): string
{
    return match ($status) {
        'RECEBIDO' => 'Recebido',
        'PREPARANDO' => 'Em Preparo',
        'PRONTO' => 'Pronto!',
        'FINALIZADO' => 'Finalizado',
        'CANCELADO' => 'Cancelado',
        default => $status,
    };
}

/**
 * Get setting from database
 */
function setting(string $key, $default = null)
{
    static $settings = null;

    if ($settings === null) {
        $settings = [];
        try {
            $rows = Database::fetchAll("SELECT `key`, `value` FROM settings");
            foreach ($rows as $row) {
                $settings[$row['key']] = $row['value'];
            }
        } catch (Exception $e) {
            // Table might not exist yet
        }
    }

    return $settings[$key] ?? $default;
}

/**
 * Set/update setting in database
 */
function setSetting(string $key, string $value): void
{
    $exists = Database::fetch("SELECT 1 FROM settings WHERE `key` = ?", [$key]);

    if ($exists) {
        Database::getInstance()->prepare("UPDATE settings SET `value` = ?, updated_at = CURRENT_TIMESTAMP WHERE `key` = ?")->execute([$value, $key]);
    } else {
        Database::getInstance()->prepare("INSERT INTO settings (`key`, `value`) VALUES (?, ?)")->execute([$key, $value]);
    }
}
