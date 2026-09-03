<?php

namespace App\Models;

use App\Core\Model;

class Client extends Model
{
    /**
     * Lista completa sin paginar (para selects/dropdowns).
     */
    public function all(): array
    {
        return $this->db->query('SELECT * FROM clients ORDER BY name ASC')->fetchAll();
    }

    /**
     * Listado paginado con búsqueda, para la pantalla de índice.
     */
    public function paginated(string $search = '', int $page = 1, int $perPage = 15): array
    {
        [$where, $params] = $this->buildFilters($search);

        $sql = "SELECT * FROM clients {$where} ORDER BY name ASC LIMIT {$perPage} OFFSET " . (max(1, $page) - 1) * $perPage;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function count(string $search = ''): int
    {
        [$where, $params] = $this->buildFilters($search);

        $stmt = $this->db->prepare("SELECT COUNT(*) FROM clients {$where}");
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    private function buildFilters(string $search): array
    {
        if ($search === '') {
            return ['', []];
        }

        $like = '%' . $search . '%';
        return [' WHERE name LIKE ? OR phone LIKE ? OR email LIKE ? ', [$like, $like, $like]];
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM clients WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO clients (name, phone, email, address, notes) VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['name'],
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
            'UPDATE clients SET name = ?, phone = ?, email = ?, address = ?, notes = ? WHERE id = ?'
        );
        return $stmt->execute([
            $data['name'],
            $data['phone'] ?? null,
            $data['email'] ?? null,
            $data['address'] ?? null,
            $data['notes'] ?? null,
            $id,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM clients WHERE id = ?');
        return $stmt->execute([$id]);
    }
}
