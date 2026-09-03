<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\SupplierPayment;

class SupplierPaymentController extends Controller
{
    public function store(): void
    {
        $this->verifyCsrf();
        $supplierId = (int) $this->input('supplier_id');

        (new SupplierPayment())->create([
            'supplier_id'    => $supplierId,
            'amount'         => (float) $this->input('amount'),
            'payment_date'   => $this->input('payment_date'),
            'payment_method' => $this->input('payment_method', 'efectivo'),
            'notes'          => $this->input('notes'),
        ]);

        flash('success', 'Abono a proveedor registrado.');
        $this->redirect('suppliers/' . $supplierId);
    }
}
