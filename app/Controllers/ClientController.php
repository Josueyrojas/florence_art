<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Client;
use App\Models\Project;

class ClientController extends Controller
{
    private const PER_PAGE = 15;

    public function index(): void
    {
        $model = new Client();
        $search = trim((string) $this->input('search', ''));
        $page = max(1, (int) $this->input('page', 1));

        $this->view('clients/index', [
            'clients'    => $model->paginated($search, $page, self::PER_PAGE),
            'totalCount' => $model->count($search),
            'perPage'    => self::PER_PAGE,
            'page'       => $page,
            'search'     => $search,
        ]);
    }

    public function show(string $id): void
    {
        $id = (int) $id;
        $client = (new Client())->find($id);

        if (!$client) {
            http_response_code(404);
            echo 'Cliente no encontrado';
            return;
        }

        $projects = (new Project())->forClient($id);
        $totals = [
            'total_cost'   => array_sum(array_column($projects, 'total_project_cost')),
            'total_paid'   => array_sum(array_column($projects, 'total_payments')),
            'balance_due'  => array_sum(array_column($projects, 'balance_due')),
        ];

        $this->view('clients/show', [
            'client'   => $client,
            'projects' => $projects,
            'totals'   => $totals,
        ]);
    }

    public function create(): void
    {
        $this->view('clients/create');
    }

    public function store(): void
    {
        $this->verifyCsrf();

        $name = trim((string) $this->input('name', ''));
        if ($name === '') {
            flash('error', 'El nombre del cliente es obligatorio.');
            $this->redirect('clients/create');
        }

        $model = new Client();
        $model->create([
            'name'    => $name,
            'phone'   => $this->input('phone'),
            'email'   => $this->input('email'),
            'address' => $this->input('address'),
            'notes'   => $this->input('notes'),
        ]);

        flash('success', 'Cliente registrado correctamente.');
        $this->redirect('clients');
    }

    public function edit(string $id): void
    {
        $id = (int) $id;
        $model = new Client();
        $client = $model->find($id);

        if (!$client) {
            http_response_code(404);
            echo 'Cliente no encontrado';
            return;
        }

        $this->view('clients/edit', ['client' => $client]);
    }

    public function update(string $id): void
    {
        $this->verifyCsrf();
        $id = (int) $id;

        $name = trim((string) $this->input('name', ''));
        if ($name === '') {
            flash('error', 'El nombre del cliente es obligatorio.');
            $this->redirect('clients/' . $id . '/edit');
        }

        (new Client())->update($id, [
            'name'    => $name,
            'phone'   => $this->input('phone'),
            'email'   => $this->input('email'),
            'address' => $this->input('address'),
            'notes'   => $this->input('notes'),
        ]);

        flash('success', 'Cliente actualizado correctamente.');
        $this->redirect('clients');
    }

    public function destroy(string $id): void
    {
        $this->requireAdmin();
        $this->verifyCsrf();

        try {
            (new Client())->delete((int) $id);
            flash('success', 'Cliente eliminado.');
        } catch (\PDOException $e) {
            flash('error', 'No se puede eliminar: el cliente tiene proyectos asociados.');
        }

        $this->redirect('clients');
    }
}
