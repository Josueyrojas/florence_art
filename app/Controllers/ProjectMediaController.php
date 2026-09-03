<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\ProjectMedia;

class ProjectMediaController extends Controller
{
    private const ALLOWED_MIME_TYPES = ['image/jpeg', 'image/png', 'image/webp'];
    private const MAX_SIZE_BYTES = 5 * 1024 * 1024; // 5 MB

    public function store(): void
    {
        $this->verifyCsrf();
        $projectId = (int) $this->input('project_id');

        if (empty($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            flash('error', 'No se pudo subir el archivo.');
            $this->redirect('projects/' . $projectId);
        }

        $file = $_FILES['file'];

        if ($file['size'] > self::MAX_SIZE_BYTES) {
            flash('error', 'El archivo supera el tamaño máximo de 5MB.');
            $this->redirect('projects/' . $projectId);
        }

        $mime = mime_content_type($file['tmp_name']);
        if (!in_array($mime, self::ALLOWED_MIME_TYPES, true)) {
            flash('error', 'Formato no permitido. Usa JPG, PNG o WEBP.');
            $this->redirect('projects/' . $projectId);
        }

        $extension = match ($mime) {
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
        };

        $filename = 'project_' . $projectId . '_' . bin2hex(random_bytes(8)) . '.' . $extension;
        $uploadDir = __DIR__ . '/../../public/uploads/projects/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        move_uploaded_file($file['tmp_name'], $uploadDir . $filename);

        (new ProjectMedia())->create([
            'project_id' => $projectId,
            'file_path'  => 'uploads/projects/' . $filename,
            'media_type' => $this->input('media_type', 'foto'),
            'caption'    => $this->input('caption'),
        ]);

        flash('success', 'Archivo subido correctamente.');
        $this->redirect('projects/' . $projectId);
    }

    public function destroy(string $id): void
    {
        $this->requireAdmin();
        $this->verifyCsrf();
        $projectId = (int) $this->input('project_id');
        (new ProjectMedia())->delete((int) $id);
        flash('success', 'Archivo eliminado.');
        $this->redirect('projects/' . $projectId);
    }
}
