<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CollaboratorController extends Controller
{
    private const MAX_COLLABORATORS = 2;

    public function index()
    {
        $this->authorizeManager();
        $manager = Auth::user();
        $collaborators = $manager->collaborators()->with('accessibleCompanies')->get();
        $companies = $manager->companies()->orderBy('name')->get();

        return view('manager.collaborators.index', compact('collaborators', 'companies'));
    }

    public function create()
    {
        $this->authorizeManager();
        $manager = Auth::user();

        if ($manager->collaborators()->count() >= self::MAX_COLLABORATORS) {
            return redirect()->route('manager.collaborators.index')
                ->with('error', 'Limite de ' . self::MAX_COLLABORATORS . ' utilisateurs associés atteinte.');
        }

        $companies = $manager->companies()->orderBy('name')->get();
        return view('manager.collaborators.create', compact('companies'));
    }

    public function store(Request $request)
    {
        $this->authorizeManager();
        $manager = Auth::user();

        if ($manager->collaborators()->count() >= self::MAX_COLLABORATORS) {
            return back()->with('error', 'Limite de ' . self::MAX_COLLABORATORS . ' utilisateurs associés atteinte.');
        }

        $data = $request->validate([
            'firstname'    => 'required|string|max:100',
            'name'         => 'required|string|max:100',
            'email'        => 'required|email|unique:users,email',
            'password'     => 'required|string|min:8|confirmed',
            'company_ids'  => 'nullable|array',
            'company_ids.*'=> 'integer',
        ], [
            'firstname.required'  => 'Le prénom est obligatoire.',
            'name.required'       => 'Le nom est obligatoire.',
            'email.required'      => 'L\'email est obligatoire.',
            'email.unique'        => 'Cet email est déjà utilisé.',
            'password.required'   => 'Le mot de passe est obligatoire.',
            'password.min'        => 'Le mot de passe doit comporter au moins 8 caractères.',
            'password.confirmed'  => 'La confirmation du mot de passe ne correspond pas.',
        ]);

        $collaborator = User::create([
            'firstname'      => $data['firstname'],
            'name'           => $data['name'],
            'email'          => $data['email'],
            'password'       => $data['password'],
            'role'           => 'collaborator',
            'parent_user_id' => $manager->id,
        ]);

        // Assign companies (only companies owned by this manager)
        if (!empty($data['company_ids'])) {
            $validIds = $manager->companies()->pluck('id')->toArray();
            $ids = array_intersect((array) $data['company_ids'], $validIds);
            $collaborator->accessibleCompanies()->attach($ids);
        }

        return redirect()->route('manager.collaborators.index')
            ->with('success', "Utilisateur {$collaborator->full_name} créé avec succès.");
    }

    public function edit(User $collaborator)
    {
        $this->authorizeManager();
        $this->authorizeCollaboratorOwner($collaborator);

        $companies = Auth::user()->companies()->orderBy('name')->get();
        $selectedCompanyIds = $collaborator->accessibleCompanies()->pluck('companies.id')->toArray();

        return view('manager.collaborators.edit', compact('collaborator', 'companies', 'selectedCompanyIds'));
    }

    public function update(Request $request, User $collaborator)
    {
        $this->authorizeManager();
        $this->authorizeCollaboratorOwner($collaborator);
        $manager = Auth::user();

        $data = $request->validate([
            'firstname'    => 'required|string|max:100',
            'name'         => 'required|string|max:100',
            'email'        => 'required|email|unique:users,email,' . $collaborator->id,
            'password'     => 'nullable|string|min:8|confirmed',
            'company_ids'  => 'nullable|array',
            'company_ids.*'=> 'integer',
        ], [
            'firstname.required' => 'Le prénom est obligatoire.',
            'name.required'      => 'Le nom est obligatoire.',
            'email.required'     => 'L\'email est obligatoire.',
            'email.unique'       => 'Cet email est déjà utilisé.',
            'password.min'       => 'Le mot de passe doit comporter au moins 8 caractères.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
        ]);

        $updateData = [
            'firstname' => $data['firstname'],
            'name'      => $data['name'],
            'email'     => $data['email'],
        ];

        if (!empty($data['password'])) {
            $updateData['password'] = $data['password'];
        }

        $collaborator->update($updateData);

        // Sync company access
        $validIds = $manager->companies()->pluck('id')->toArray();
        $ids = !empty($data['company_ids'])
            ? array_intersect((array) $data['company_ids'], $validIds)
            : [];
        $collaborator->accessibleCompanies()->sync($ids);

        return redirect()->route('manager.collaborators.index')
            ->with('success', "Utilisateur {$collaborator->full_name} mis à jour.");
    }

    public function destroy(User $collaborator)
    {
        $this->authorizeManager();
        $this->authorizeCollaboratorOwner($collaborator);

        $collaborator->accessibleCompanies()->detach();
        $collaborator->delete();

        return redirect()->route('manager.collaborators.index')
            ->with('success', 'Utilisateur supprimé.');
    }

    /** Only real managers (not collaborators) can manage collaborators */
    private function authorizeManager(): void
    {
        if (!Auth::user()->isManager() && !session()->has('impersonate')) {
            abort(403, 'Accès refusé.');
        }
    }

    /** Ensure the collaborator belongs to the authenticated manager */
    private function authorizeCollaboratorOwner(User $collaborator): void
    {
        abort_if($collaborator->parent_user_id !== Auth::id(), 403);
    }
}
