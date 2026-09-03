<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\OperatingExpense;

class OperatingExpenseController extends Controller
{
    public function index(): void
    {
        $model = new OperatingExpense();
        $this->view('operating_expenses/index', ['expenses' => $model->all()]);
    }

    public function store(): void
    {
        $this->verifyCsrf();

        (new OperatingExpense())->create([
            'category'     => $this->input('category', 'otro'),
            'description'  => $this->input('description'),
            'amount'       => (float) $this->input('amount'),
            'expense_date' => $this->input('expense_date'),
            'is_recurring' => (bool) $this->input('is_recurring'),
        ]);

        flash('success', 'Gasto operativo registrado.');
        $this->redirect('operating-expenses');
    }

    public function destroy(string $id): void
    {
        $this->requireAdmin();
        $this->verifyCsrf();
        (new OperatingExpense())->delete((int) $id);
        flash('success', 'Gasto eliminado.');
        $this->redirect('operating-expenses');
    }
}
