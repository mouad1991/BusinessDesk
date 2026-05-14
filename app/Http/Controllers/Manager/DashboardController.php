<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Market;
use App\Models\Quote;
use App\Models\Invoice;

class DashboardController extends Controller
{
    public function index()
    {
        $company = app('currentCompany');

        $company->loadCount(['quotes', 'invoices', 'deliveryNotes', 'purchaseOrders']);

        $data = [
            'company'     => $company,
            'lastQuote'   => Quote::where('company_id', $company->id)->latest()->first(),
            'lastInvoice' => Invoice::where('company_id', $company->id)->latest()->first(),
            'markets'     => [
                'in_progress' => Market::where('company_id', $company->id)->where('status', 'in_progress')->count(),
                'total'       => Market::where('company_id', $company->id)->count(),
                'total_amount'=> Market::where('company_id', $company->id)->sum('total_amount'),
                'paid_amount' => Market::where('company_id', $company->id)->sum('paid_amount'),
                'remaining'   => Market::where('company_id', $company->id)->sum('remaining_amount'),
                'cautions'    => Market::where('company_id', $company->id)->where('caution_status', 'deposited')->count(),
            ],
        ];

        return view('manager.dashboard', compact('data', 'company'));
    }
}
