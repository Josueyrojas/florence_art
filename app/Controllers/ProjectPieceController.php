<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\ProjectPiece;

class ProjectPieceController extends Controller
{
    public function store(): void
    {
        $this->verifyCsrf();
        $projectId = (int) $this->input('project_id');

        $description = trim((string) $this->input('description', ''));
        if ($description === '') {
            flash('error', 'La descripción de la pieza es obligatoria.');
            $this->redirect('projects/' . $projectId);
        }

        (new ProjectPiece())->create([
            'project_id'  => $projectId,
            'description' => $description,
            'quantity'    => (int) $this->input('quantity', 1),
            'status'      => $this->input('status', 'pendiente'),
            'notes'       => $this->input('notes'),
        ]);

        flash('success', 'Pieza agregada.');
        $this->redirect('projects/' . $projectId);
    }

    /**
     * Cambio rápido de estado (select en la tabla de piezas).
     */
    public function updateStatus(string $id): void
    {
        $this->verifyCsrf();
        $projectId = (int) $this->input('project_id');

        (new ProjectPiece())->updateStatus((int) $id, $this->input('status', 'pendiente'));

        flash('success', 'Estado de la pieza actualizado.');
        $this->redirect('projects/' . $projectId);
    }

    public function destroy(string $id): void
    {
        $this->requireAdmin();
        $this->verifyCsrf();
        $projectId = (int) $this->input('project_id');

        (new ProjectPiece())->delete((int) $id);

        flash('success', 'Pieza eliminada.');
        $this->redirect('projects/' . $projectId);
    }
}
