<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Labor;

class LaborController extends Controller
{
    public function store(): void
    {
        $this->verifyCsrf();
        $projectId = (int) $this->input('project_id');

        (new Labor())->create([
            'project_id'  => $projectId,
            'worker_name' => $this->input('worker_name'),
            'labor_type'  => $this->input('labor_type', 'interno'),
            'description' => $this->input('description'),
            'hours'       => $this->input('hours'),
            'amount'      => (float) $this->input('amount'),
            'labor_date'  => $this->input('labor_date'),
        ]);

        flash('success', 'Mano de obra registrada.');
        $this->redirect('projects/' . $projectId);
    }

    public function destroy(string $id): void
    {
        $this->requireAdmin();
        $this->verifyCsrf();
        $projectId = (int) $this->input('project_id');
        (new Labor())->delete((int) $id);
        flash('success', 'Registro eliminado.');
        $this->redirect('projects/' . $projectId);
    }

    public function edit(string $id): void
    {
        $id = (int) $id;
        $labor = (new Labor())->find($id);

        if (!$labor) {
            http_response_code(404);
            echo 'Registro no encontrado';
            return;
        }

        $this->view('labor/edit', ['labor' => $labor]);
    }

    public function update(string $id): void
    {
        $this->verifyCsrf();
        $id = (int) $id;
        $projectId = (int) $this->input('project_id');

        (new Labor())->update($id, [
            'worker_name' => $this->input('worker_name'),
            'labor_type'  => $this->input('labor_type', 'interno'),
            'description' => $this->input('description'),
            'hours'       => $this->input('hours'),
            'amount'      => (float) $this->input('amount'),
            'labor_date'  => $this->input('labor_date'),
        ]);

        flash('success', 'Registro actualizado.');
        $this->redirect('projects/' . $projectId);
    }
}
