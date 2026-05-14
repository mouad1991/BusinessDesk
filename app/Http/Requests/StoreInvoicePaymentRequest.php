<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreInvoicePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $company = app()->bound('currentCompany') ? app('currentCompany') : null;
        $invoice = $this->route('invoice');

        return $company
            && $company->user_id === Auth::id()
            && $invoice->company_id === $company->id
            && $invoice->payment_status !== 'paid';
    }

    public function rules(): array
    {
        $invoice   = $this->route('invoice');
        $remaining = max(0, (float) $invoice->total_ttc - (float) $invoice->paid_amount);

        return [
            'amount'         => ['required', 'numeric', 'min:0.01', 'max:' . $remaining],
            'payment_method' => ['required', 'in:especes,virement,cheque,cb'],
            'payment_date'   => ['required', 'date'],
            'reference'      => ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        $invoice   = $this->route('invoice');
        $remaining = number_format(
            max(0, (float) $invoice->total_ttc - (float) $invoice->paid_amount),
            2, ',', ' '
        );

        return [
            'amount.required'    => 'Le montant est obligatoire.',
            'amount.numeric'     => 'Le montant doit être un nombre.',
            'amount.min'         => 'Le montant doit être positif.',
            'amount.max'         => "Le montant ne peut pas dépasser le reste à payer ({$remaining} DH).",
            'payment_method.required' => 'Le mode de règlement est obligatoire.',
            'payment_method.in'  => 'Mode de règlement invalide.',
            'payment_date.required'   => 'La date est obligatoire.',
            'payment_date.date'  => 'Date invalide.',
        ];
    }
}
