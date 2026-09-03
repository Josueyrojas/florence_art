<?php

namespace App\Models;

use App\Core\Model;

class Payment extends Model
{
    public function forProject(int $projectId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM payments WHERE project_id = ? ORDER BY payment_date DESC');
        $stmt->execute([$projectId]);
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM payments WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE payments SET amount = ?, payment_date = ?, payment_method = ?, notes = ? WHERE id = ?'
        );
        return $stmt->execute([
            $data['amount'],
            $data['payment_date'],
            $data['payment_method'] ?? 'efectivo',
            $data['notes'] ?? null,
            $id,
        ]);
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO payments (project_id, amount, payment_date, payment_method, notes)
             VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['project_id'],
            $data['amount'],
            $data['payment_date'],
            $data['payment_method'] ?? 'efectivo',
            $data['notes'] ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM payments WHERE id = ?');
        return $stmt->execute([$id]);
    }

    public function monthlyTotal(?string $yearMonth = null): float
    {
        $yearMonth = $yearMonth ?? date('Y-m');
        $stmt = $this->db->prepare(
            "SELECT COALESCE(SUM(amount), 0) FROM payments WHERE DATE_FORMAT(payment_date, '%Y-%m') = ?"
        );
        $stmt->execute([$yearMonth]);
        return (float) $stmt->fetchColumn();
    }
}
