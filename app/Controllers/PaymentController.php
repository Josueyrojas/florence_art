<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Payment;

class PaymentController extends Controller
{
    public function store(): void
    {
        $this->verifyCsrf();
        $projectId = (int) $this->input('project_id');

        (new Payment())->create([
            'project_id'     => $projectId,
            'amount'         => (float) $this->input('amount'),
            'payment_date'   => $this->input('payment_date'),
            'payment_method' => $this->input('payment_method', 'efectivo'),
            'notes'          => $this->input('notes'),
        ]);

        flash('success', 'Abono registrado.');
        $this->redirect('projects/' . $projectId);
    }

    public function destroy(string $id): void
    {
        $this->requireAdmin();
        $this->verifyCsrf();
        $projectId = (int) $this->input('project_id');
        (new Payment())->delete((int) $id);
        flash('success', 'Abono eliminado.');
        $this->redirect('projects/' . $projectId);
    }

    public function edit(string $id): void
    {
        $id = (int) $id;
        $payment = (new Payment())->find($id);

        if (!$payment) {
            http_response_code(404);
            echo 'Abono no encontrado';
            return;
        }

        $this->view('payments/edit', ['payment' => $payment]);
    }

    public function update(string $id): void
    {
        $this->verifyCsrf();
        $id = (int) $id;
        $projectId = (int) $this->input('project_id');

        (new Payment())->update($id, [
            'amount'         => (float) $this->input('amount'),
            'payment_date'   => $this->input('payment_date'),
            'payment_method' => $this->input('payment_method', 'efectivo'),
            'notes'          => $this->input('notes'),
        ]);

        flash('success', 'Abono actualizado.');
        $this->redirect('projects/' . $projectId);
    }
}
