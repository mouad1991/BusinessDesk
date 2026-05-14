<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateInvoicePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $company = app()->bound('currentCompany') ? app('currentCompany') : null;
        $invoice = $this->route('invoice');
        $payment = $this->route('payment');

        return $company
            && $company->user_id === Auth::id()
            && $invoice->company_id === $company->id
            && $payment->invoice_id === $invoice->id;
    }

    public function rules(): array
    {
        $invoice = $this->route('invoice');
        $payment = $this->route('payment');

        $remaining = max(0, (float) $invoice->total_ttc - (float) $invoice->paid_amount + (float) $payment->amount);

        return [
            'amount'         => ['required', 'numeric', 'min:0.01', 'max:' . $remaining],
            'payment_method' => ['required', 'in:especes,virement,cheque,cb'],
            'payment_date'   => ['required', 'date'],
            'reference'      => ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'amount.required'         => 'Le montant est obligatoire.',
            'amount.numeric'          => 'Le montant doit être un nombre.',
            'amount.min'              => 'Le montant doit être positif.',
            'amount.max'              => 'Le montant dépasse le reste à payer.',
            'payment_method.required' => 'Le mode de règlement est obligatoire.',
            'payment_method.in'       => 'Mode de règlement invalide.',
            'payment_date.required'   => 'La date est obligatoire.',
            'payment_date.date'       => 'Date invalide.',
        ];
    }
}
