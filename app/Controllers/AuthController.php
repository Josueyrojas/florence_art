<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin(): void
    {
        $this->view('auth/login', [], 'layouts/guest');
    }

    public function login(): void
    {
        $this->verifyCsrf();

        $email = trim((string) $this->input('email', ''));
        $password = (string) $this->input('password', '');

        $user = (new User())->findByEmail($email);

        if (!$user || !$user['is_active'] || !password_verify($password, $user['password_hash'])) {
            flash('error', 'Correo o contraseña incorrectos.');
            $this->redirect('login');
        }

        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role'];

        $this->redirect('');
    }

    public function logout(): void
    {
        $this->verifyCsrf();
        $_SESSION = [];
        session_destroy();
        header('Location: ' . url('login'));
        exit;
    }
}
