<?php

namespace App\Http\Requests;

use App\Models\Market;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateMarketRequest extends FormRequest
{
    public function authorize(): bool
    {
        $company = app()->bound('currentCompany') ? app('currentCompany') : null;
        $market  = $this->route('market');
        return $company
            && $company->user_id === Auth::id()
            && $market->company_id === $company->id;
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
        return (new StoreMarketRequest())->messages();
    }
}
