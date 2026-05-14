<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ], [
            'email.required'    => 'L\'email est obligatoire.',
            'email.email'       => 'Format d\'email invalide.',
            'password.required' => 'Le mot de passe est obligatoire.',
        ]);

        if (!Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'Email ou mot de passe incorrect.',
            ]);
        }

        $request->session()->regenerate();

        return $this->redirectByRole(Auth::user());
    }

    public function logout(Request $request)
    {
        // If admin is impersonating, stop impersonation
        if ($request->session()->has('impersonate')) {
            $adminId = $request->session()->get('impersonate');
            $request->session()->forget('impersonate');
            Auth::loginUsingId($adminId);
            return redirect()->route('admin.managers.index')
                ->with('success', 'Impersonation terminée.');
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function redirectByRole(User $user): \Illuminate\Http\RedirectResponse
    {
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        // Manager: ensure a current company is selected for the session
        if (!session()->has('current_company_id')) {
            return redirect()->route('manager.company-select');
        }

        return redirect()->route('manager.dashboard');
    }
}
