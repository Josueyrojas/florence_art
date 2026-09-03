<?php

namespace App\Models;

use App\Core\Model;

class Settings extends Model
{
    public function current(): array
    {
        $row = $this->db->query('SELECT * FROM settings WHERE id = 1')->fetch();
        return $row ?: $this->defaults();
    }

    public function update(array $data): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE settings SET business_name = ?, tagline = ?, phone = ?, whatsapp = ?, email = ?, address = ?, website = ?
             WHERE id = 1'
        );
        return $stmt->execute([
            $data['business_name'] ?: 'Florence Art',
            $data['tagline'] ?? null,
            $data['phone'] ?? null,
            $data['whatsapp'] ?? null,
            $data['email'] ?? null,
            $data['address'] ?? null,
            $data['website'] ?? null,
        ]);
    }

    public function updateLogo(?string $path): bool
    {
        $stmt = $this->db->prepare('UPDATE settings SET logo_path = ? WHERE id = 1');
        return $stmt->execute([$path]);
    }

    private function defaults(): array
    {
        return [
            'business_name' => 'Florence Art',
            'tagline'       => null,
            'phone'         => null,
            'whatsapp'      => null,
            'email'         => null,
            'address'       => null,
            'website'       => null,
            'logo_path'     => null,
        ];
    }
}
