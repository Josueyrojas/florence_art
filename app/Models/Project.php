<?php

namespace App\Models;

use App\Core\Model;

class Project extends Model
{
    public function all(array $filters = [], int $page = 1, int $perPage = 15): array
    {
        [$where, $params] = $this->buildFilters($filters);

        $sql = "
            SELECT
                p.*,
                c.name AS client_name,
                COALESCE(extras.total_extras, 0) AS total_extras,
                COALESCE(pay.total_payments, 0) AS total_payments,
                (p.agreed_cost + COALESCE(extras.total_extras, 0)) AS total_project_cost,
                (p.agreed_cost + COALESCE(extras.total_extras, 0) - COALESCE(pay.total_payments, 0)) AS balance_due
            FROM projects p
            INNER JOIN clients c ON c.id = p.client_id
            LEFT JOIN (
                SELECT project_id, SUM(amount) AS total_extras
                FROM project_extras GROUP BY project_id
            ) extras ON extras.project_id = p.id
            LEFT JOIN (
                SELECT project_id, SUM(amount) AS total_payments
                FROM payments GROUP BY project_id
            ) pay ON pay.project_id = p.id
            {$where}
            ORDER BY p.created_at DESC
            LIMIT {$perPage} OFFSET " . (max(1, $page) - 1) * $perPage . "
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Todos los proyectos de un cliente, sin paginar (para su ficha de detalle).
     */
    public function forClient(int $clientId): array
    {
        return $this->all(['client_id' => $clientId], 1, 1000);
    }

    public function count(array $filters = []): int
    {
        [$where, $params] = $this->buildFilters($filters);

        $sql = "SELECT COUNT(*) FROM projects p INNER JOIN clients c ON c.id = p.client_id {$where}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    private function buildFilters(array $filters): array
    {
        $conditions = [];
        $params = [];

        if (!empty($filters['status'])) {
            $conditions[] = 'p.status = ?';
            $params[] = $filters['status'];
        }
        if (!empty($filters['client_id'])) {
            $conditions[] = 'p.client_id = ?';
            $params[] = $filters['client_id'];
        }
        if (!empty($filters['search'])) {
            $conditions[] = '(p.name LIKE ? OR c.name LIKE ?)';
            $like = '%' . $filters['search'] . '%';
            $params[] = $like;
            $params[] = $like;
        }

        $where = $conditions ? ' WHERE ' . implode(' AND ', $conditions) : '';
        return [$where, $params];
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT p.*, c.name AS client_name, c.phone AS client_phone, c.email AS client_email
             FROM projects p INNER JOIN clients c ON c.id = p.client_id
             WHERE p.id = ?'
        );
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO projects (client_id, name, description, agreed_cost, start_date, delivery_date, status)
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['client_id'],
            $data['name'],
            $data['description'] ?? null,
            $data['agreed_cost'],
            $data['start_date'] ?: null,
            $data['delivery_date'] ?: null,
            $data['status'] ?? 'cotizado',
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE projects SET client_id = ?, name = ?, description = ?, agreed_cost = ?,
             start_date = ?, delivery_date = ?, status = ? WHERE id = ?'
        );
        return $stmt->execute([
            $data['client_id'],
            $data['name'],
            $data['description'] ?? null,
            $data['agreed_cost'],
            $data['start_date'] ?: null,
            $data['delivery_date'] ?: null,
            $data['status'],
            $id,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM projects WHERE id = ?');
        return $stmt->execute([$id]);
    }

    /**
     * Utilidad = Abonos recibidos - (Gastos directos + Mano de obra).
     * Los gastos "al fiado" se contabilizan como costo desde que se generan
     * (no hasta que se paga al proveedor), porque ya son un costo real del
     * proyecto aunque el dinero se deba todavía.
     */
    public function financialSummary(int $id): array
    {
        $extras = $this->sumWhere('project_extras', 'project_id', $id);
        $payments = $this->sumWhere('payments', 'project_id', $id);
        $directExpenses = $this->sumWhere('expenses', 'project_id', $id);
        $labor = $this->sumWhere('labor_costs', 'project_id', $id);

        $project = $this->find($id);
        $totalCost = (float) $project['agreed_cost'] + $extras;
        $balanceDue = $totalCost - $payments;
        $utility = $payments - ($directExpenses + $labor);

        return [
            'agreed_cost'     => (float) $project['agreed_cost'],
            'total_extras'    => $extras,
            'total_cost'      => $totalCost,
            'total_payments'  => $payments,
            'balance_due'     => $balanceDue,
            'direct_expenses' => $directExpenses,
            'labor_costs'     => $labor,
            'utility'         => $utility,
        ];
    }

    private function sumWhere(string $table, string $column, int $id): float
    {
        // $table/$column son fijos en el código (no provienen del usuario), es seguro interpolarlos.
        $stmt = $this->db->prepare("SELECT COALESCE(SUM(amount), 0) AS total FROM {$table} WHERE {$column} = ?");
        $stmt->execute([$id]);
        return (float) $stmt->fetchColumn();
    }
}
