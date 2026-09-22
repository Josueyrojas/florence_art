<?php

namespace App\Models;

use App\Core\Model;

class User extends Model
{
    private const MAX_ATTEMPTS = 5;
    private const LOCKOUT_MINUTES = 15;

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        return $stmt->fetch() ?: null;
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function isLocked(array $user): bool
    {
        return !empty($user['locked_until']) && strtotime($user['locked_until']) > time();
    }

    /**
     * Suma un intento fallido; bloquea la cuenta temporalmente al llegar al límite.
     */
    public function registerFailedAttempt(int $id): void
    {
        $this->db->prepare('UPDATE users SET failed_attempts = failed_attempts + 1 WHERE id = ?')->execute([$id]);

        $stmt = $this->db->prepare('SELECT failed_attempts FROM users WHERE id = ?');
        $stmt->execute([$id]);
        $attempts = (int) $stmt->fetchColumn();

        if ($attempts >= self::MAX_ATTEMPTS) {
            $lockUntil = date('Y-m-d H:i:s', time() + self::LOCKOUT_MINUTES * 60);
            $this->db->prepare('UPDATE users SET locked_until = ? WHERE id = ?')->execute([$lockUntil, $id]);
        }
    }

    public function resetFailedAttempts(int $id): void
    {
        $this->db->prepare('UPDATE users SET failed_attempts = 0, locked_until = NULL WHERE id = ?')->execute([$id]);
    }

    public function all(): array
    {
        return $this->db->query('SELECT * FROM users ORDER BY name ASC')->fetchAll();
    }

    public function countActiveAdmins(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM users WHERE role = 'admin' AND is_active = 1");
        return (int) $stmt->fetchColumn();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO users (name, email, password_hash, role, is_active) VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['name'],
            $data['email'],
            password_hash($data['password'], PASSWORD_DEFAULT),
            $data['role'] ?? 'operador',
            !empty($data['is_active']) ? 1 : 0,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE users SET name = ?, email = ?, role = ?, is_active = ? WHERE id = ?'
        );
        return $stmt->execute([
            $data['name'],
            $data['email'],
            $data['role'],
            !empty($data['is_active']) ? 1 : 0,
            $id,
        ]);
    }

    public function updatePassword(int $id, string $password): bool
    {
        $stmt = $this->db->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
        return $stmt->execute([password_hash($password, PASSWORD_DEFAULT), $id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM users WHERE id = ?');
        return $stmt->execute([$id]);
    }

    /**
     * Genera y guarda un nuevo token de sesión, invalidando cualquier otra
     * sesión activa de este usuario (solo se permite una a la vez).
     */
    public function issueSessionToken(int $id): string
    {
        $token = bin2hex(random_bytes(32));
        $this->db->prepare('UPDATE users SET current_session_token = ? WHERE id = ?')->execute([$token, $id]);
        return $token;
    }

    public function clearSessionToken(int $id): void
    {
        $this->db->prepare('UPDATE users SET current_session_token = NULL WHERE id = ?')->execute([$id]);
    }

    public function hasValidSessionToken(int $id, string $token): bool
    {
        $stmt = $this->db->prepare('SELECT current_session_token FROM users WHERE id = ?');
        $stmt->execute([$id]);
        $stored = $stmt->fetchColumn();
        return $stored !== false && $stored !== null && hash_equals((string) $stored, $token);
    }
}
