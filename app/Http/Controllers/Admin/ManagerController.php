<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ManagerController extends Controller
{
    public function index()
    {
        $managers = User::where('role', 'manager')
            ->withCount('companies')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.managers.index', compact('managers'));
    }

    public function create()
    {
        return view('admin.managers.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:100',
            'firstname' => 'required|string|max:100',
            'email'     => 'required|email|unique:users,email',
            'password'  => ['required', 'confirmed', Password::min(8)],
            'address'   => 'nullable|string|max:255',
            'mobile'    => 'nullable|string|max:20',
        ], [
            'name.required'      => 'Le nom est obligatoire.',
            'firstname.required' => 'Le prénom est obligatoire.',
            'email.required'     => 'L\'email est obligatoire.',
            'email.unique'       => 'Cet email est déjà utilisé.',
            'password.required'  => 'Le mot de passe est obligatoire.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
        ]);

        $data['role'] = 'manager';
        User::create($data);

        return redirect()->route('admin.managers.index')
            ->with('success', 'Gérant créé avec succès.');
    }

    public function edit(User $manager)
    {
        abort_if($manager->isAdmin(), 403);
        return view('admin.managers.edit', compact('manager'));
    }

    public function update(Request $request, User $manager)
    {
        abort_if($manager->isAdmin(), 403);

        $data = $request->validate([
            'name'      => 'required|string|max:100',
            'firstname' => 'required|string|max:100',
            'email'     => 'required|email|unique:users,email,' . $manager->id,
            'address'   => 'nullable|string|max:255',
            'mobile'    => 'nullable|string|max:20',
            'password'  => ['nullable', 'confirmed', Password::min(8)],
        ], [
            'name.required'      => 'Le nom est obligatoire.',
            'firstname.required' => 'Le prénom est obligatoire.',
            'email.required'     => 'L\'email est obligatoire.',
            'email.unique'       => 'Cet email est déjà utilisé.',
        ]);

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $manager->update($data);

        return redirect()->route('admin.managers.index')
            ->with('success', 'Gérant modifié avec succès.');
    }

    public function destroy(User $manager)
    {
        abort_if($manager->isAdmin(), 403);
        $manager->delete();

        return redirect()->route('admin.managers.index')
            ->with('success', 'Gérant supprimé avec succès.');
    }

    public function impersonate(User $manager)
    {
        abort_if($manager->isAdmin(), 403);
        abort_if(!Auth::user()->isAdmin(), 403);

        session(['impersonate' => Auth::id()]);
        Auth::login($manager);

        return redirect()->route('manager.dashboard')
            ->with('info', 'Vous naviguez en tant que ' . $manager->full_name);
    }
}
