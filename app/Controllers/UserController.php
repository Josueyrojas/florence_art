<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class UserController extends Controller
{
    public function index(): void
    {
        $this->requireAdmin();
        $this->view('users/index', [
            'users'         => (new User())->all(),
            'currentUserId' => (int) ($_SESSION['user_id'] ?? 0),
        ]);
    }

    public function create(): void
    {
        $this->requireAdmin();
        $this->view('users/create');
    }

    public function store(): void
    {
        $this->requireAdmin();
        $this->verifyCsrf();

        $errors = $this->validateInput(requirePassword: true);
        if ($errors) {
            flash('error', implode(' ', $errors));
            $this->redirect('users/create');
        }

        try {
            (new User())->create([
                'name'      => trim((string) $this->input('name')),
                'email'     => trim((string) $this->input('email')),
                'password'  => (string) $this->input('password'),
                'role'      => $this->input('role', 'operador'),
                'is_active' => (bool) $this->input('is_active', true),
            ]);
        } catch (\PDOException $e) {
            flash('error', 'Ya existe un usuario registrado con ese correo.');
            $this->redirect('users/create');
        }

        flash('success', 'Usuario creado correctamente.');
        $this->redirect('users');
    }

    public function edit(string $id): void
    {
        $this->requireAdmin();
        $id = (int) $id;
        $user = (new User())->find($id);

        if (!$user) {
            http_response_code(404);
            echo 'Usuario no encontrado';
            return;
        }

        $this->view('users/edit', ['user' => $user]);
    }

    public function update(string $id): void
    {
        $this->requireAdmin();
        $this->verifyCsrf();
        $id = (int) $id;

        $model = new User();
        $existing = $model->find($id);
        if (!$existing) {
            http_response_code(404);
            echo 'Usuario no encontrado';
            return;
        }

        $errors = $this->validateInput(requirePassword: false);
        if ($errors) {
            flash('error', implode(' ', $errors));
            $this->redirect('users/' . $id . '/edit');
        }

        $newRole = $this->input('role', $existing['role']);
        $newActive = (bool) $this->input('is_active', false);
        $isSelf = $id === (int) ($_SESSION['user_id'] ?? 0);

        // No dejar que un admin se quite a sí mismo el rol o se desactive si es el último activo.
        $losesAdmin = $existing['role'] === 'admin' && ($newRole !== 'admin' || !$newActive);
        if ($losesAdmin && $model->countActiveAdmins() <= 1) {
            flash('error', 'No puedes quitar el rol de administrador ni desactivar al único administrador activo del sistema.');
            $this->redirect('users/' . $id . '/edit');
        }
        if ($isSelf && !$newActive) {
            flash('error', 'No puedes desactivar tu propia cuenta.');
            $this->redirect('users/' . $id . '/edit');
        }

        try {
            $model->update($id, [
                'name'      => trim((string) $this->input('name')),
                'email'     => trim((string) $this->input('email')),
                'role'      => $newRole,
                'is_active' => $newActive,
            ]);
        } catch (\PDOException $e) {
            flash('error', 'Ya existe un usuario registrado con ese correo.');
            $this->redirect('users/' . $id . '/edit');
        }

        $newPassword = (string) $this->input('password', '');
        if ($newPassword !== '') {
            if (strlen($newPassword) < 8) {
                flash('error', 'Usuario actualizado, pero la contraseña no se cambió: debe tener al menos 8 caracteres.');
                $this->redirect('users/' . $id);
            }
            $model->updatePassword($id, $newPassword);
        }

        flash('success', 'Usuario actualizado correctamente.');
        $this->redirect('users');
    }

    public function destroy(string $id): void
    {
        $this->requireAdmin();
        $this->verifyCsrf();
        $id = (int) $id;

        if ($id === (int) ($_SESSION['user_id'] ?? 0)) {
            flash('error', 'No puedes eliminar tu propia cuenta.');
            $this->redirect('users');
        }

        $model = new User();
        $target = $model->find($id);

        if ($target && $target['role'] === 'admin' && $target['is_active'] && $model->countActiveAdmins() <= 1) {
            flash('error', 'No puedes eliminar al único administrador activo del sistema.');
            $this->redirect('users');
        }

        $model->delete($id);
        flash('success', 'Usuario eliminado.');
        $this->redirect('users');
    }

    private function validateInput(bool $requirePassword): array
    {
        $errors = [];

        if (trim((string) $this->input('name', '')) === '') {
            $errors[] = 'El nombre es obligatorio.';
        }

        $email = trim((string) $this->input('email', ''));
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Ingresa un correo válido.';
        }

        if (!in_array($this->input('role'), ['admin', 'operador'], true)) {
            $errors[] = 'Selecciona un rol válido.';
        }

        if ($requirePassword) {
            $password = (string) $this->input('password', '');
            if (strlen($password) < 8) {
                $errors[] = 'La contraseña debe tener al menos 8 caracteres.';
            }
        }

        return $errors;
    }
}
