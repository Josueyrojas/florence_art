<?php

namespace App\Models;

use App\Core\Model;

class Supplier extends Model
{
    public function all(): array
    {
        return $this->db->query('SELECT * FROM suppliers ORDER BY name ASC')->fetchAll();
    }

    /**
     * Lista de proveedores con su saldo pendiente (fiado):
     * total comprado a crédito - total abonado.
     */
    public function allWithBalance(string $search = '', int $page = 1, int $perPage = 15): array
    {
        [$where, $params] = $this->buildFilters($search);

        $sql = "
            SELECT
                s.*,
                COALESCE(credit.total_credit, 0) AS total_credit,
                COALESCE(paid.total_paid, 0) AS total_paid,
                (COALESCE(credit.total_credit, 0) - COALESCE(paid.total_paid, 0)) AS balance
            FROM suppliers s
            LEFT JOIN (
                SELECT supplier_id, SUM(amount) AS total_credit
                FROM expenses
                WHERE is_credit = 1
                GROUP BY supplier_id
            ) credit ON credit.supplier_id = s.id
            LEFT JOIN (
                SELECT supplier_id, SUM(amount) AS total_paid
                FROM supplier_payments
                GROUP BY supplier_id
            ) paid ON paid.supplier_id = s.id
            {$where}
            ORDER BY s.name ASC
            LIMIT {$perPage} OFFSET " . (max(1, $page) - 1) * $perPage . "
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function count(string $search = ''): int
    {
        [$where, $params] = $this->buildFilters($search);

        $stmt = $this->db->prepare("SELECT COUNT(*) FROM suppliers s {$where}");
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    private function buildFilters(string $search): array
    {
        if ($search === '') {
            return ['', []];
        }

        $like = '%' . $search . '%';
        return [' WHERE s.name LIKE ? OR s.contact_name LIKE ? ', [$like, $like]];
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM suppliers WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO suppliers (name, contact_name, phone, email, address, notes) VALUES (?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['name'],
            $data['contact_name'] ?? null,
            $data['phone'] ?? null,
            $data['email'] ?? null,
            $data['address'] ?? null,
            $data['notes'] ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE suppliers SET name = ?, contact_name = ?, phone = ?, email = ?, address = ?, notes = ? WHERE id = ?'
        );
        return $stmt->execute([
            $data['name'],
            $data['contact_name'] ?? null,
            $data['phone'] ?? null,
            $data['email'] ?? null,
            $data['address'] ?? null,
            $data['notes'] ?? null,
            $id,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM suppliers WHERE id = ?');
        return $stmt->execute([$id]);
    }
}
