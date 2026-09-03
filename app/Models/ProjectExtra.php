<?php

namespace App\Models;

use App\Core\Model;

class ProjectExtra extends Model
{
    public function forProject(int $projectId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM project_extras WHERE project_id = ? ORDER BY extra_date DESC');
        $stmt->execute([$projectId]);
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM project_extras WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE project_extras SET description = ?, amount = ?, extra_date = ? WHERE id = ?'
        );
        return $stmt->execute([
            $data['description'],
            $data['amount'],
            $data['extra_date'],
            $id,
        ]);
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO project_extras (project_id, description, amount, extra_date) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['project_id'],
            $data['description'],
            $data['amount'],
            $data['extra_date'],
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM project_extras WHERE id = ?');
        return $stmt->execute([$id]);
    }
}
