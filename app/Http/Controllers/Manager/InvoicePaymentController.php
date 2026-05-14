<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInvoicePaymentRequest;
use App\Http\Requests\UpdateInvoicePaymentRequest;
use App\Models\Invoice;
use App\Models\InvoicePayment;

class InvoicePaymentController extends Controller
{
    public function store(StoreInvoicePaymentRequest $request, Invoice $invoice)
    {
        $invoice->payments()->create($request->validated());
        $invoice->syncPaymentStatus();

        return back()->with('success', 'Règlement enregistré avec succès.');
    }

    public function update(UpdateInvoicePaymentRequest $request, Invoice $invoice, InvoicePayment $payment)
    {
        abort_if($payment->invoice_id !== $invoice->id, 403);

        $payment->update($request->validated());
        $invoice->syncPaymentStatus();

        return back()->with('success', 'Règlement modifié avec succès.');
    }

    public function destroy(Invoice $invoice, InvoicePayment $payment)
    {
        $company = app('currentCompany');
        abort_if($invoice->company_id !== $company->id, 403);
        abort_if($payment->invoice_id !== $invoice->id, 403);

        $payment->delete();
        $invoice->syncPaymentStatus();

        return back()->with('success', 'Règlement supprimé.');
    }
}
