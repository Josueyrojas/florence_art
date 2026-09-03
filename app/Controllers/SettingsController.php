<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Settings;

class SettingsController extends Controller
{
    private const ALLOWED_MIME_TYPES = ['image/jpeg', 'image/png', 'image/webp'];
    private const MAX_SIZE_BYTES = 5 * 1024 * 1024; // 5 MB

    public function index(): void
    {
        $this->requireAdmin();
        $this->view('settings/index', ['settings' => (new Settings())->current()]);
    }

    public function update(): void
    {
        $this->requireAdmin();
        $this->verifyCsrf();

        (new Settings())->update([
            'business_name' => trim((string) $this->input('business_name', 'Florence Art')),
            'tagline'       => $this->input('tagline'),
            'phone'         => $this->input('phone'),
            'whatsapp'      => $this->input('whatsapp'),
            'email'         => $this->input('email'),
            'address'       => $this->input('address'),
            'website'       => $this->input('website'),
        ]);

        flash('success', 'Datos del negocio actualizados.');
        $this->redirect('settings');
    }

    public function uploadLogo(): void
    {
        $this->requireAdmin();
        $this->verifyCsrf();

        if (empty($_FILES['logo']) || $_FILES['logo']['error'] !== UPLOAD_ERR_OK) {
            flash('error', 'No se pudo subir el logo.');
            $this->redirect('settings');
        }

        $file = $_FILES['logo'];

        if ($file['size'] > self::MAX_SIZE_BYTES) {
            flash('error', 'El archivo supera el tamaño máximo de 5MB.');
            $this->redirect('settings');
        }

        $mime = mime_content_type($file['tmp_name']);
        if (!in_array($mime, self::ALLOWED_MIME_TYPES, true)) {
            flash('error', 'Formato no permitido. Usa JPG, PNG o WEBP.');
            $this->redirect('settings');
        }

        $extension = match ($mime) {
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
        };

        $model = new Settings();
        $current = $model->current();

        $filename = 'logo_' . bin2hex(random_bytes(6)) . '.' . $extension;
        $uploadDir = __DIR__ . '/../../public/assets/img/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        move_uploaded_file($file['tmp_name'], $uploadDir . $filename);

        // Borra el logo anterior para no dejar archivos huérfanos.
        if (!empty($current['logo_path'])) {
            $oldFile = __DIR__ . '/../../public/' . $current['logo_path'];
            if (is_file($oldFile)) {
                unlink($oldFile);
            }
        }

        $model->updateLogo('assets/img/' . $filename);

        flash('success', 'Logo actualizado.');
        $this->redirect('settings');
    }

    public function removeLogo(): void
    {
        $this->requireAdmin();
        $this->verifyCsrf();

        $model = new Settings();
        $current = $model->current();

        if (!empty($current['logo_path'])) {
            $oldFile = __DIR__ . '/../../public/' . $current['logo_path'];
            if (is_file($oldFile)) {
                unlink($oldFile);
            }
        }

        $model->updateLogo(null);

        flash('success', 'Logo eliminado. Se muestra el emblema por defecto.');
        $this->redirect('settings');
    }
}
