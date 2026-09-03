<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Supplier;
use App\Models\SupplierPayment;

class SupplierController extends Controller
{
    private const PER_PAGE = 15;

    public function index(): void
    {
        $model = new Supplier();
        $search = trim((string) $this->input('search', ''));
        $page = max(1, (int) $this->input('page', 1));

        $this->view('suppliers/index', [
            'suppliers'  => $model->allWithBalance($search, $page, self::PER_PAGE),
            'totalCount' => $model->count($search),
            'perPage'    => self::PER_PAGE,
            'page'       => $page,
            'search'     => $search,
        ]);
    }

    public function create(): void
    {
        $this->view('suppliers/create');
    }

    public function store(): void
    {
        $this->verifyCsrf();

        $name = trim((string) $this->input('name', ''));
        if ($name === '') {
            flash('error', 'El nombre del proveedor es obligatorio.');
            $this->redirect('suppliers/create');
        }

        (new Supplier())->create([
            'name'         => $name,
            'contact_name' => $this->input('contact_name'),
            'phone'        => $this->input('phone'),
            'email'        => $this->input('email'),
            'address'      => $this->input('address'),
            'notes'        => $this->input('notes'),
        ]);

        flash('success', 'Proveedor registrado correctamente.');
        $this->redirect('suppliers');
    }

    public function show(string $id): void
    {
        $id = (int) $id;
        $supplier = (new Supplier())->find($id);

        if (!$supplier) {
            http_response_code(404);
            echo 'Proveedor no encontrado';
            return;
        }

        $this->view('suppliers/show', [
            'supplier' => $supplier,
            'payments' => (new SupplierPayment())->forSupplier($id),
        ]);
    }

    public function edit(string $id): void
    {
        $id = (int) $id;
        $supplier = (new Supplier())->find($id);

        if (!$supplier) {
            http_response_code(404);
            echo 'Proveedor no encontrado';
            return;
        }

        $this->view('suppliers/edit', ['supplier' => $supplier]);
    }

    public function update(string $id): void
    {
        $this->verifyCsrf();
        $id = (int) $id;

        $name = trim((string) $this->input('name', ''));
        if ($name === '') {
            flash('error', 'El nombre del proveedor es obligatorio.');
            $this->redirect('suppliers/' . $id . '/edit');
        }

        (new Supplier())->update($id, [
            'name'         => $name,
            'contact_name' => $this->input('contact_name'),
            'phone'        => $this->input('phone'),
            'email'        => $this->input('email'),
            'address'      => $this->input('address'),
            'notes'        => $this->input('notes'),
        ]);

        flash('success', 'Proveedor actualizado correctamente.');
        $this->redirect('suppliers');
    }

    public function destroy(string $id): void
    {
        $this->requireAdmin();
        $this->verifyCsrf();
        (new Supplier())->delete((int) $id);
        flash('success', 'Proveedor eliminado.');
        $this->redirect('suppliers');
    }
}
