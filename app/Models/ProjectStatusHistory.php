<?php

namespace App\Models;

use App\Core\Model;

class ProjectStatusHistory extends Model
{
    public function forProject(int $projectId): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM project_status_history WHERE project_id = ? ORDER BY changed_at DESC'
        );
        $stmt->execute([$projectId]);
        return $stmt->fetchAll();
    }

    public function log(int $projectId, ?string $oldStatus, string $newStatus): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO project_status_history (project_id, old_status, new_status) VALUES (?, ?, ?)'
        );
        $stmt->execute([$projectId, $oldStatus, $newStatus]);
    }

    /**
     * Proyectos con fecha de entrega en los próximos $days días (sin contar ya entregados).
     */
    public function upcomingDeliveries(int $days = 7): array
    {
        $stmt = $this->db->prepare("
            SELECT p.id, p.name, p.delivery_date, c.name AS client_name
            FROM projects p INNER JOIN clients c ON c.id = p.client_id
            WHERE p.delivery_date IS NOT NULL
              AND p.delivery_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL ? DAY)
              AND p.status NOT IN ('entregado', 'finalizado', 'cancelado')
            ORDER BY p.delivery_date ASC
        ");
        $stmt->execute([$days]);
        return $stmt->fetchAll();
    }

    /**
     * Proyectos con saldo pendiente cuya fecha de entrega ya pasó.
     */
    public function overdueBalances(): array
    {
        return $this->db->query("
            SELECT p.id, p.name, p.delivery_date, c.name AS client_name,
                   (p.agreed_cost + COALESCE(extras.total, 0) - COALESCE(pay.total, 0)) AS balance_due
            FROM projects p
            INNER JOIN clients c ON c.id = p.client_id
            LEFT JOIN (SELECT project_id, SUM(amount) AS total FROM project_extras GROUP BY project_id) extras
                ON extras.project_id = p.id
            LEFT JOIN (SELECT project_id, SUM(amount) AS total FROM payments GROUP BY project_id) pay
                ON pay.project_id = p.id
            WHERE p.delivery_date IS NOT NULL
              AND p.delivery_date < CURDATE()
              AND p.status NOT IN ('finalizado', 'cancelado')
            HAVING balance_due > 0
            ORDER BY p.delivery_date ASC
        ")->fetchAll();
    }
}
