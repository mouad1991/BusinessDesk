<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('profile.edit', [
            'user' => Auth::user(),
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'firstname' => 'required|string|max:100',
            'name'      => 'required|string|max:100',
            'email'     => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'mobile'    => 'nullable|string|max:30',
            'address'   => 'nullable|string|max:500',
        ], [
            'firstname.required' => 'Le prénom est obligatoire.',
            'name.required'      => 'Le nom est obligatoire.',
            'email.required'     => 'L\'email est obligatoire.',
            'email.email'        => 'Format d\'email invalide.',
            'email.unique'       => 'Cet email est déjà utilisé par un autre compte.',
        ]);

        $user->update($data);

        return back()->with('success', 'Profil mis à jour avec succès.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'confirmed', Password::min(8)],
        ], [
            'current_password.required'         => 'Le mot de passe actuel est obligatoire.',
            'current_password.current_password' => 'Le mot de passe actuel est incorrect.',
            'password.required'                 => 'Le nouveau mot de passe est obligatoire.',
            'password.confirmed'                => 'La confirmation du mot de passe ne correspond pas.',
            'password.min'                      => 'Le mot de passe doit contenir au moins 8 caractères.',
        ]);

        $user = Auth::user();
        $user->password = $request->input('password');
        $user->save();

        return back()->with('success', 'Mot de passe modifié avec succès.');
    }
}
