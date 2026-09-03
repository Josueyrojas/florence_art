<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\ProjectExtra;

class ProjectExtraController extends Controller
{
    public function store(): void
    {
        $this->verifyCsrf();
        $projectId = (int) $this->input('project_id');

        (new ProjectExtra())->create([
            'project_id'  => $projectId,
            'description' => $this->input('description'),
            'amount'      => (float) $this->input('amount'),
            'extra_date'  => $this->input('extra_date'),
        ]);

        flash('success', 'Modificación registrada.');
        $this->redirect('projects/' . $projectId);
    }

    public function destroy(string $id): void
    {
        $this->requireAdmin();
        $this->verifyCsrf();
        $projectId = (int) $this->input('project_id');
        (new ProjectExtra())->delete((int) $id);
        flash('success', 'Modificación eliminada.');
        $this->redirect('projects/' . $projectId);
    }

    public function edit(string $id): void
    {
        $id = (int) $id;
        $extra = (new ProjectExtra())->find($id);

        if (!$extra) {
            http_response_code(404);
            echo 'Modificación no encontrada';
            return;
        }

        $this->view('project_extras/edit', ['extra' => $extra]);
    }

    public function update(string $id): void
    {
        $this->verifyCsrf();
        $id = (int) $id;
        $projectId = (int) $this->input('project_id');

        (new ProjectExtra())->update($id, [
            'description' => $this->input('description'),
            'amount'      => (float) $this->input('amount'),
            'extra_date'  => $this->input('extra_date'),
        ]);

        flash('success', 'Modificación actualizada.');
        $this->redirect('projects/' . $projectId);
    }
}
