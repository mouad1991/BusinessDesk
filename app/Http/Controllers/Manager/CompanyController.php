<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CompanyController extends Controller
{
    public function index()
    {
        $companies = Company::where('user_id', Auth::id())
            ->orderBy('name')
            ->get();

        return view('manager.companies.index', compact('companies'));
    }

    public function create()
    {
        // Only an admin impersonating a manager can create a company
        abort_if(!session()->has('impersonate'), 403, 'Seul l\'administrateur peut créer une société.');
        return view('manager.companies.create');
    }

    public function store(Request $request)
    {
        abort_if(!session()->has('impersonate'), 403, 'Seul l\'administrateur peut créer une société.');

        $data = $this->validateCompany($request);
        $data['user_id'] = Auth::id();
        $data['logo'] = $this->handleLogo($request);

        Company::create($data);

        return redirect()->route('manager.companies.index')
            ->with('success', 'Société créée avec succès.');
    }

    public function edit(Company $company)
    {
        abort_if($company->user_id !== Auth::id(), 403);
        return view('manager.companies.edit', compact('company'));
    }

    public function update(Request $request, Company $company)
    {
        abort_if($company->user_id !== Auth::id(), 403);

        $data = $this->validateCompany($request, $company->id);

        if ($request->hasFile('logo')) {
            if ($company->logo) {
                Storage::disk('public')->delete($company->logo);
            }
            $data['logo'] = $this->handleLogo($request);
        } else {
            unset($data['logo']);
        }

        $company->update($data);

        return redirect()->route('manager.companies.index')
            ->with('success', 'Société modifiée avec succès.');
    }

    public function destroy(Company $company)
    {
        abort_if($company->user_id !== Auth::id(), 403);

        if ($company->logo) {
            Storage::disk('public')->delete($company->logo);
        }

        $company->delete();

        return redirect()->route('manager.companies.index')
            ->with('success', 'Société supprimée avec succès.');
    }

    private function validateCompany(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'name'              => 'required|string|max:255',
            'address'           => 'required|string|max:500',
            'phone'             => 'required|string|max:30',
            'email'             => 'required|email|max:255',
            'rc'                => 'nullable|string|max:50',
            'ice'               => 'nullable|string|max:50',
            'if_number'         => 'nullable|string|max:50',
            'tp'                => 'nullable|string|max:50',
            'capital'           => 'nullable|numeric|min:0',
            'doc_prefix'        => 'required|string|max:10',
            'logo'              => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'bank_account_name' => 'nullable|string|max:255',
            'bank'              => 'nullable|string|max:100',
            'rib'               => 'nullable|string|max:50',
            'conditions_devis'  => 'nullable|string',
            'conditions_bc'     => 'nullable|string',
            'pdf_template'      => 'nullable|string|in:modern,amber,rounded,colorful',
        ], [
            'name.required'       => 'La raison sociale est obligatoire.',
            'address.required'    => 'L\'adresse est obligatoire.',
            'phone.required'      => 'Le téléphone est obligatoire.',
            'email.required'      => 'L\'email est obligatoire.',
            'doc_prefix.required' => 'Le préfixe de document est obligatoire.',
            'logo.image'          => 'Le logo doit être une image.',
            'logo.max'            => 'Le logo ne doit pas dépasser 2 Mo.',
        ]);
    }

    private function handleLogo(Request $request): ?string
    {
        if ($request->hasFile('logo')) {
            return $request->file('logo')->store('logos', 'public');
        }
        return null;
    }
}
