<?php

namespace App\Models;

use App\Core\Model;

class OperatingExpense extends Model
{
    public function all(): array
    {
        return $this->db->query('SELECT * FROM operating_expenses ORDER BY expense_date DESC')->fetchAll();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO operating_expenses (category, description, amount, expense_date, is_recurring)
             VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['category'] ?? 'otro',
            $data['description'],
            $data['amount'],
            $data['expense_date'],
            !empty($data['is_recurring']) ? 1 : 0,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM operating_expenses WHERE id = ?');
        return $stmt->execute([$id]);
    }

    public function monthlyTotal(?string $yearMonth = null): float
    {
        $yearMonth = $yearMonth ?? date('Y-m');
        $stmt = $this->db->prepare(
            "SELECT COALESCE(SUM(amount), 0) FROM operating_expenses WHERE DATE_FORMAT(expense_date, '%Y-%m') = ?"
        );
        $stmt->execute([$yearMonth]);
        return (float) $stmt->fetchColumn();
    }
}
