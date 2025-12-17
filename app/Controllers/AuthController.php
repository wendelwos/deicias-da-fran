<?php
/**
 * Authentication Controller
 */

class AuthController
{

    public function showLogin(): void
    {
        // Redirect if already logged in
        if (auth()) {
            redirect('cozinha');
            return;
        }

        view('auth/login');
    }

    public function login(): void
    {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $errors = [];

        if (empty($email)) {
            $errors['email'] = 'Email é obrigatório';
        }

        if (empty($password)) {
            $errors['password'] = 'Senha é obrigatória';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_input'] = ['email' => $email];
            redirect('login');
            return;
        }

        // Find user
        $user = Database::fetch("
            SELECT * FROM users WHERE email = ? AND active = 1
        ", [$email]);

        if (!$user || !password_verify($password, $user['password'])) {
            $_SESSION['errors'] = ['email' => 'Email ou senha inválidos'];
            $_SESSION['old_input'] = ['email' => $email];
            redirect('login');
            return;
        }

        // Set session
        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'],
        ];

        // Log the login
        Database::insert('audit_logs', [
            'user_id' => $user['id'],
            'action' => 'LOGIN',
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
        ]);

        // Clear errors
        unset($_SESSION['errors'], $_SESSION['old_input']);

        // Redirect based on role
        if ($user['role'] === 'admin') {
            redirect('admin/pedidos');
        } else {
            redirect('cozinha');
        }
    }

    public function logout(): void
    {
        if ($user = auth()) {
            Database::insert('audit_logs', [
                'user_id' => $user['id'],
                'action' => 'LOGOUT',
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            ]);
        }

        session_destroy();
        redirect('login');
    }
}
