<?php

namespace App\Models;

use App\Core\Model;

class SupplierPayment extends Model
{
    public function forSupplier(int $supplierId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM supplier_payments WHERE supplier_id = ? ORDER BY payment_date DESC');
        $stmt->execute([$supplierId]);
        return $stmt->fetchAll();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO supplier_payments (supplier_id, amount, payment_date, payment_method, notes)
             VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['supplier_id'],
            $data['amount'],
            $data['payment_date'],
            $data['payment_method'] ?? 'efectivo',
            $data['notes'] ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }
}
