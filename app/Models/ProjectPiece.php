<?php

namespace App\Models;

use App\Core\Model;

class ProjectPiece extends Model
{
    public function forProject(int $projectId): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM project_pieces WHERE project_id = ? ORDER BY created_at ASC'
        );
        $stmt->execute([$projectId]);
        return $stmt->fetchAll();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO project_pieces (project_id, description, quantity, status, notes) VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['project_id'],
            $data['description'],
            $data['quantity'] ?: 1,
            $data['status'] ?? 'pendiente',
            $data['notes'] ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function updateStatus(int $id, string $status): bool
    {
        $stmt = $this->db->prepare('UPDATE project_pieces SET status = ? WHERE id = ?');
        return $stmt->execute([$status, $id]);
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE project_pieces SET description = ?, quantity = ?, status = ?, notes = ? WHERE id = ?'
        );
        return $stmt->execute([
            $data['description'],
            $data['quantity'] ?: 1,
            $data['status'],
            $data['notes'] ?? null,
            $id,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM project_pieces WHERE id = ?');
        return $stmt->execute([$id]);
    }

    /**
     * Piezas pendientes/en proceso de todos los proyectos activos, para el
     * panel de "Pendientes" del dashboard. Ordenadas por fecha de entrega
     * del proyecto (las más urgentes primero); los proyectos sin fecha de
     * entrega quedan al final.
     */
    public function pendingAcrossProjects(): array
    {
        return $this->db->query("
            SELECT pp.id, pp.description, pp.quantity, pp.status,
                   p.id AS project_id, p.name AS project_name, p.delivery_date,
                   c.name AS client_name
            FROM project_pieces pp
            INNER JOIN projects p ON p.id = pp.project_id
            INNER JOIN clients c ON c.id = p.client_id
            WHERE pp.status IN ('pendiente', 'en_proceso')
              AND p.status NOT IN ('entregado', 'finalizado', 'cancelado')
            ORDER BY (p.delivery_date IS NULL) ASC, p.delivery_date ASC, pp.created_at ASC
        ")->fetchAll();
    }
}
