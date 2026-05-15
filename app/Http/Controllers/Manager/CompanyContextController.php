<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyContextController extends Controller
{
    public function select(Request $request)
    {
        $user = Auth::user();
        $companies = $user->getWorkableCompanies();

        // Auto-select if only one company
        if ($companies->count() === 1 && !$request->session()->has('current_company_id')) {
            $request->session()->put('current_company_id', $companies->first()->id);
            return redirect()->route('manager.dashboard');
        }

        return view('manager.company-select', compact('companies'));
    }

    public function switch(Request $request, Company $company)
    {
        $user = Auth::user();

        if ($user->isCollaborator()) {
            $hasAccess = $user->accessibleCompanies()->where('companies.id', $company->id)->exists();
            abort_if(!$hasAccess, 403, 'Accès refusé à cette société.');
        } else {
            abort_if($company->user_id !== $user->id, 403);
        }

        $request->session()->put('current_company_id', $company->id);

        return redirect()->route('manager.dashboard')
            ->with('success', 'Société active : ' . $company->name);
    }
}
