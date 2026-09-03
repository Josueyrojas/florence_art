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
}
