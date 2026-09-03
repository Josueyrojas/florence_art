<?php

namespace App\Models;

use App\Core\Model;

class Expense extends Model
{
    public function forProject(int $projectId): array
    {
        $sql = "SELECT e.*, s.name AS supplier_name
                FROM expenses e
                LEFT JOIN suppliers s ON s.id = e.supplier_id
                WHERE e.project_id = ?
                ORDER BY e.expense_date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$projectId]);
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM expenses WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function update(int $id, array $data): bool
    {
        $isCredit = !empty($data['is_credit']) ? 1 : 0;

        $stmt = $this->db->prepare(
            'UPDATE expenses SET supplier_id = ?, category = ?, description = ?, amount = ?, expense_date = ?, is_credit = ?, payment_status = ?
             WHERE id = ?'
        );
        return $stmt->execute([
            $data['supplier_id'] ?: null,
            $data['category'] ?? 'material',
            $data['description'],
            $data['amount'],
            $data['expense_date'],
            $isCredit,
            $isCredit ? 'pendiente' : 'pagado',
            $id,
        ]);
    }

    public function create(array $data): int
    {
        $isCredit = !empty($data['is_credit']) ? 1 : 0;

        $stmt = $this->db->prepare(
            'INSERT INTO expenses (project_id, supplier_id, category, description, amount, expense_date, is_credit, payment_status)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['project_id'],
            $data['supplier_id'] ?: null,
            $data['category'] ?? 'material',
            $data['description'],
            $data['amount'],
            $data['expense_date'],
            $isCredit,
            $isCredit ? 'pendiente' : 'pagado',
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM expenses WHERE id = ?');
        return $stmt->execute([$id]);
    }

    public function monthlyTotal(?string $yearMonth = null): float
    {
        $yearMonth = $yearMonth ?? date('Y-m');
        $stmt = $this->db->prepare(
            "SELECT COALESCE(SUM(amount), 0) FROM expenses WHERE DATE_FORMAT(expense_date, '%Y-%m') = ?"
        );
        $stmt->execute([$yearMonth]);
        return (float) $stmt->fetchColumn();
    }

    public function totalPendingToSuppliers(): float
    {
        $credit = (float) $this->db->query(
            'SELECT COALESCE(SUM(amount), 0) FROM expenses WHERE is_credit = 1'
        )->fetchColumn();

        $paid = (float) $this->db->query(
            'SELECT COALESCE(SUM(amount), 0) FROM supplier_payments'
        )->fetchColumn();

        return $credit - $paid;
    }
}
