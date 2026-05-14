<?php

namespace App\Http\Middleware;

use App\Models\Company;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SetCurrentCompany
{
    public function handle(Request $request, Closure $next)
    {
        $companyId = $request->session()->get('current_company_id');

        if (!$companyId) {
            return redirect()->route('manager.company-select')
                ->with('info', 'Veuillez sélectionner une société.');
        }

        $company = Company::where('user_id', Auth::id())->find($companyId);

        if (!$company) {
            $request->session()->forget('current_company_id');
            return redirect()->route('manager.company-select')
                ->with('error', 'Société invalide ou introuvable.');
        }

        // Bind for DI and share with all views
        app()->instance('currentCompany', $company);
        view()->share('currentCompany', $company);

        return $next($request);
    }
}
