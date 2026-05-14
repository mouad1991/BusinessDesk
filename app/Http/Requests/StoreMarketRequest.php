<?php

namespace App\Http\Requests;

use App\Models\Market;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreMarketRequest extends FormRequest
{
    public function authorize(): bool
    {
        $company = app()->bound('currentCompany') ? app('currentCompany') : null;
        return $company && $company->user_id === Auth::id();
    }

    public function rules(): array
    {
        return [
            'title'            => ['required', 'string', 'max:500'],
            'market_type'      => ['required', 'in:public,private'],
            'category'         => ['required', 'in:supply,service,works,maintenance,other'],
            'client_id'        => ['nullable', 'exists:clients,id'],
            'status'           => ['required', 'in:' . implode(',', array_keys(Market::statusOptions()))],
            'start_date'       => ['nullable', 'date'],
            'end_date'         => ['nullable', 'date', 'after_or_equal:start_date'],
            'total_amount'     => ['required', 'numeric', 'min:0'],
            'invoiced_amount'  => ['nullable', 'numeric', 'min:0'],
            'paid_amount'      => ['nullable', 'numeric', 'min:0'],
            'caution_amount'   => ['nullable', 'numeric', 'min:0'],
            'caution_status'   => ['nullable', 'in:not_deposited,deposited,recovered'],
            'notes'            => ['nullable', 'string'],
            'attachments'      => ['nullable', 'array'],
            'attachments.*'    => ['file', 'max:10240', 'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'          => "L'objet du marché est obligatoire.",
            'market_type.required'    => 'Le type de marché est obligatoire.',
            'category.required'       => 'La catégorie est obligatoire.',
            'status.required'         => 'Le statut est obligatoire.',
            'total_amount.required'   => 'Le montant total est obligatoire.',
            'total_amount.numeric'    => 'Le montant doit être un nombre.',
            'total_amount.min'        => 'Le montant doit être positif.',
            'end_date.after_or_equal' => 'La date de fin doit être après la date de début.',
            'attachments.*.max'       => 'Chaque fichier ne doit pas dépasser 10 Mo.',
            'attachments.*.mimes'     => 'Formats acceptés : PDF, Word, Excel, JPEG, PNG.',
        ];
    }
}
