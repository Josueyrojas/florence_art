<?php

namespace App\Models;

use App\Core\Model;

class ProjectMedia extends Model
{
    public function forProject(int $projectId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM project_media WHERE project_id = ? ORDER BY uploaded_at DESC');
        $stmt->execute([$projectId]);
        return $stmt->fetchAll();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO project_media (project_id, file_path, media_type, caption) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['project_id'],
            $data['file_path'],
            $data['media_type'] ?? 'foto',
            $data['caption'] ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM project_media WHERE id = ?');
        return $stmt->execute([$id]);
    }
}
