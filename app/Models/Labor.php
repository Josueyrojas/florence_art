<?php

namespace App\Models;

use App\Core\Model;

class Labor extends Model
{
    public function forProject(int $projectId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM labor_costs WHERE project_id = ? ORDER BY labor_date DESC');
        $stmt->execute([$projectId]);
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM labor_costs WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE labor_costs SET worker_name = ?, labor_type = ?, description = ?, hours = ?, amount = ?, labor_date = ? WHERE id = ?'
        );
        return $stmt->execute([
            $data['worker_name'] ?? null,
            $data['labor_type'] ?? 'interno',
            $data['description'] ?? null,
            $data['hours'] ?: null,
            $data['amount'],
            $data['labor_date'],
            $id,
        ]);
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO labor_costs (project_id, worker_name, labor_type, description, hours, amount, labor_date)
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['project_id'],
            $data['worker_name'] ?? null,
            $data['labor_type'] ?? 'interno',
            $data['description'] ?? null,
            $data['hours'] ?: null,
            $data['amount'],
            $data['labor_date'],
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM labor_costs WHERE id = ?');
        return $stmt->execute([$id]);
    }

    public function monthlyTotal(?string $yearMonth = null): float
    {
        $yearMonth = $yearMonth ?? date('Y-m');
        $stmt = $this->db->prepare(
            "SELECT COALESCE(SUM(amount), 0) FROM labor_costs WHERE DATE_FORMAT(labor_date, '%Y-%m') = ?"
        );
        $stmt->execute([$yearMonth]);
        return (float) $stmt->fetchColumn();
    }
}
