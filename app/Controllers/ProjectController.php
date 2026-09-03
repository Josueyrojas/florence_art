<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Project;
use App\Models\Client;
use App\Models\ProjectExtra;
use App\Models\ProjectMedia;
use App\Models\Payment;
use App\Models\Expense;
use App\Models\Labor;
use App\Models\Supplier;
use App\Models\ProjectStatusHistory;

class ProjectController extends Controller
{
    private const PER_PAGE = 15;

    public function index(): void
    {
        $model = new Project();
        $filters = [
            'status'    => $this->input('status'),
            'client_id' => $this->input('client_id'),
            'search'    => $this->input('search'),
        ];
        $page = max(1, (int) $this->input('page', 1));

        $this->view('projects/index', [
            'projects'   => $model->all(array_filter($filters), $page, self::PER_PAGE),
            'totalCount' => $model->count(array_filter($filters)),
            'perPage'    => self::PER_PAGE,
            'page'       => $page,
            'clients'    => (new Client())->all(),
            'filters'    => $filters,
        ]);
    }

    public function create(): void
    {
        $this->view('projects/create', ['clients' => (new Client())->all()]);
    }

    public function store(): void
    {
        $this->verifyCsrf();

        $errors = $this->validateProjectInput();
        if ($errors) {
            flash('error', implode(' ', $errors));
            $this->redirect('projects/create');
        }

        $status = $this->input('status', 'cotizado');
        $model = new Project();
        $id = $model->create([
            'client_id'     => (int) $this->input('client_id'),
            'name'          => trim((string) $this->input('name')),
            'description'   => $this->input('description'),
            'agreed_cost'   => (float) $this->input('agreed_cost'),
            'start_date'    => $this->input('start_date'),
            'delivery_date' => $this->input('delivery_date'),
            'status'        => $status,
        ]);

        (new ProjectStatusHistory())->log($id, null, $status);

        flash('success', 'Proyecto creado correctamente.');
        $this->redirect('projects/' . $id);
    }

    public function show(string $id): void
    {
        $id = (int) $id;
        $model = new Project();
        $project = $model->find($id);

        if (!$project) {
            http_response_code(404);
            echo 'Proyecto no encontrado';
            return;
        }

        $this->view('projects/show', [
            'project'       => $project,
            'summary'       => $model->financialSummary($id),
            'extras'        => (new ProjectExtra())->forProject($id),
            'media'         => (new ProjectMedia())->forProject($id),
            'payments'      => (new Payment())->forProject($id),
            'expenses'      => (new Expense())->forProject($id),
            'labor'         => (new Labor())->forProject($id),
            'suppliers'     => (new Supplier())->all(),
            'statusHistory' => (new ProjectStatusHistory())->forProject($id),
        ]);
    }

    public function edit(string $id): void
    {
        $id = (int) $id;
        $model = new Project();
        $project = $model->find($id);

        if (!$project) {
            http_response_code(404);
            echo 'Proyecto no encontrado';
            return;
        }

        $this->view('projects/edit', [
            'project' => $project,
            'clients' => (new Client())->all(),
        ]);
    }

    public function update(string $id): void
    {
        $this->verifyCsrf();
        $id = (int) $id;

        $errors = $this->validateProjectInput();
        if ($errors) {
            flash('error', implode(' ', $errors));
            $this->redirect('projects/' . $id . '/edit');
        }

        $model = new Project();
        $existing = $model->find($id);
        $newStatus = $this->input('status');

        $model->update($id, [
            'client_id'     => (int) $this->input('client_id'),
            'name'          => trim((string) $this->input('name')),
            'description'   => $this->input('description'),
            'agreed_cost'   => (float) $this->input('agreed_cost'),
            'start_date'    => $this->input('start_date'),
            'delivery_date' => $this->input('delivery_date'),
            'status'        => $newStatus,
        ]);

        if ($existing && $existing['status'] !== $newStatus) {
            (new ProjectStatusHistory())->log($id, $existing['status'], $newStatus);
        }

        flash('success', 'Proyecto actualizado correctamente.');
        $this->redirect('projects/' . $id);
    }

    public function destroy(string $id): void
    {
        $this->requireAdmin();
        $this->verifyCsrf();
        (new Project())->delete((int) $id);
        flash('success', 'Proyecto eliminado.');
        $this->redirect('projects');
    }

    /**
     * Reporte imprimible (el usuario lo convierte a PDF con "Imprimir" del navegador).
     */
    public function report(string $id): void
    {
        $id = (int) $id;
        $model = new Project();
        $project = $model->find($id);

        if (!$project) {
            http_response_code(404);
            echo 'Proyecto no encontrado';
            return;
        }

        $this->view('projects/report', [
            'project'  => $project,
            'summary'  => $model->financialSummary($id),
            'extras'   => (new ProjectExtra())->forProject($id),
            'payments' => (new Payment())->forProject($id),
            'expenses' => (new Expense())->forProject($id),
            'labor'    => (new Labor())->forProject($id),
        ], 'layouts/print');
    }

    /**
     * Exporta el listado completo de proyectos (con los mismos filtros del índice) a CSV.
     */
    public function exportAllCsv(): void
    {
        $model = new Project();
        $filters = array_filter([
            'status'    => $this->input('status'),
            'client_id' => $this->input('client_id'),
            'search'    => $this->input('search'),
        ]);
        $projects = $model->all($filters, 1, 100000);

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="proyectos.csv"');

        $out = fopen('php://output', 'w');
        fprintf($out, "\xEF\xBB\xBF");
        fputcsv($out, ['Proyecto', 'Cliente', 'Estado', 'Costo total', 'Abonado', 'Saldo pendiente', 'Fecha inicio', 'Fecha entrega']);
        foreach ($projects as $p) {
            fputcsv($out, [
                $p['name'],
                $p['client_name'],
                $p['status'],
                $p['total_project_cost'],
                $p['total_payments'],
                $p['balance_due'],
                $p['start_date'],
                $p['delivery_date'],
            ]);
        }
        fclose($out);
        exit;
    }

    /**
     * Exporta el detalle financiero del proyecto (abonos, gastos, mano de obra) a CSV.
     */
    public function exportCsv(string $id): void
    {
        $id = (int) $id;
        $model = new Project();
        $project = $model->find($id);

        if (!$project) {
            http_response_code(404);
            echo 'Proyecto no encontrado';
            return;
        }

        $rows = [];
        foreach ((new Payment())->forProject($id) as $p) {
            $rows[] = ['Abono', $p['payment_date'], $p['amount'], $p['payment_method'], $p['notes']];
        }
        foreach ((new Expense())->forProject($id) as $e) {
            $rows[] = ['Gasto', $e['expense_date'], -$e['amount'], $e['category'], $e['description']];
        }
        foreach ((new Labor())->forProject($id) as $l) {
            $rows[] = ['Mano de obra', $l['labor_date'], -$l['amount'], $l['labor_type'], $l['description']];
        }
        foreach ((new ProjectExtra())->forProject($id) as $ex) {
            $rows[] = ['Modificación', $ex['extra_date'], $ex['amount'], '', $ex['description']];
        }
        usort($rows, fn ($a, $b) => strcmp((string) $a[1], (string) $b[1]));

        $filename = 'proyecto_' . $id . '_' . preg_replace('/[^a-z0-9]+/i', '_', $project['name']) . '.csv';

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $out = fopen('php://output', 'w');
        fprintf($out, "\xEF\xBB\xBF"); // BOM para que Excel detecte UTF-8
        fputcsv($out, ['Tipo', 'Fecha', 'Monto', 'Categoría / Método', 'Descripción / Notas']);
        foreach ($rows as $row) {
            fputcsv($out, $row);
        }
        fclose($out);
        exit;
    }

    private function validateProjectInput(): array
    {
        $errors = [];

        if (trim((string) $this->input('name', '')) === '') {
            $errors[] = 'El nombre del proyecto es obligatorio.';
        }
        if (!$this->input('client_id')) {
            $errors[] = 'Debes seleccionar un cliente.';
        }
        if (!is_numeric($this->input('agreed_cost', ''))) {
            $errors[] = 'El costo acordado debe ser un número.';
        }

        return $errors;
    }
}
