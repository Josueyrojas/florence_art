<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Expense;

class ExpenseController extends Controller
{
    public function store(): void
    {
        $this->verifyCsrf();
        $projectId = (int) $this->input('project_id');

        (new Expense())->create([
            'project_id'   => $projectId,
            'supplier_id'  => $this->input('supplier_id') ?: null,
            'category'     => $this->input('category', 'material'),
            'description'  => $this->input('description'),
            'amount'       => (float) $this->input('amount'),
            'expense_date' => $this->input('expense_date'),
            'is_credit'    => (bool) $this->input('is_credit'),
        ]);

        flash('success', 'Gasto registrado.');
        $this->redirect('projects/' . $projectId);
    }

    public function destroy(string $id): void
    {
        $this->requireAdmin();
        $this->verifyCsrf();
        $projectId = (int) $this->input('project_id');
        (new Expense())->delete((int) $id);
        flash('success', 'Gasto eliminado.');
        $this->redirect('projects/' . $projectId);
    }

    public function edit(string $id): void
    {
        $id = (int) $id;
        $expense = (new Expense())->find($id);

        if (!$expense) {
            http_response_code(404);
            echo 'Gasto no encontrado';
            return;
        }

        $this->view('expenses/edit', [
            'expense'   => $expense,
            'suppliers' => (new \App\Models\Supplier())->all(),
        ]);
    }

    public function update(string $id): void
    {
        $this->verifyCsrf();
        $id = (int) $id;
        $projectId = (int) $this->input('project_id');

        (new Expense())->update($id, [
            'supplier_id'  => $this->input('supplier_id') ?: null,
            'category'     => $this->input('category', 'material'),
            'description'  => $this->input('description'),
            'amount'       => (float) $this->input('amount'),
            'expense_date' => $this->input('expense_date'),
            'is_credit'    => (bool) $this->input('is_credit'),
        ]);

        flash('success', 'Gasto actualizado.');
        $this->redirect('projects/' . $projectId);
    }
}
