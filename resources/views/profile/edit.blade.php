@extends('layouts.app')
@section('title', 'Mon profil')
@section('page-title', 'Mon profil')
@section('breadcrumb')
    Mon profil
@endsection

@section('content')
<div class="profile-grid">

    {{-- Carte : Informations personnelles --}}
    <div class="card">
        <div class="card-header">
            <h3>Informations personnelles</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('profile.update') }}">
                @csrf
                @method('PATCH')

                <div class="form-row">
                    <div class="form-group">
                        <label>Prénom <span class="required">*</span></label>
                        <input type="text" name="firstname"
                               value="{{ old('firstname', $user->firstname) }}"
                               class="{{ $errors->updateProfile ?? false ? ($errors->updateProfile->has('firstname') ? 'is-invalid' : '') : ($errors->has('firstname') ? 'is-invalid' : '') }}"
                               required>
                        @error('firstname')<span class="error-msg">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label>Nom <span class="required">*</span></label>
                        <input type="text" name="name"
                               value="{{ old('name', $user->name) }}"
                               class="{{ $errors->has('name') ? 'is-invalid' : '' }}"
                               required>
                        @error('name')<span class="error-msg">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="form-group">
                    <label>Adresse email <span class="required">*</span></label>
                    <input type="email" name="email"
                           value="{{ old('email', $user->email) }}"
                           class="{{ $errors->has('email') ? 'is-invalid' : '' }}"
                           required>
                    @error('email')<span class="error-msg">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label>Téléphone mobile</label>
                    <input type="text" name="mobile"
                           value="{{ old('mobile', $user->mobile) }}"
                           placeholder="06 12 34 56 78">
                    @error('mobile')<span class="error-msg">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label>Adresse</label>
                    <textarea name="address" rows="2" placeholder="Adresse postale">{{ old('address', $user->address) }}</textarea>
                    @error('address')<span class="error-msg">{{ $message }}</span>@enderror
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v14a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                        Enregistrer les modifications
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Carte : Sécurité (mot de passe) --}}
    <div class="card mt-4">
        <div class="card-header">
            <h3>Sécurité — Changer mon mot de passe</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('profile.password') }}">
                @csrf
                @method('PATCH')

                <div class="form-group">
                    <label>Mot de passe actuel <span class="required">*</span></label>
                    <div class="input-password">
                        <input type="password" id="current_password" name="current_password"
                               class="{{ $errors->has('current_password') ? 'is-invalid' : '' }}"
                               placeholder="••••••••" required>
                        <button type="button" class="toggle-password" onclick="togglePwd('current_password')">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                    @error('current_password')<span class="error-msg">{{ $message }}</span>@enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Nouveau mot de passe <span class="required">*</span></label>
                        <div class="input-password">
                            <input type="password" id="password" name="password"
                                   class="{{ $errors->has('password') ? 'is-invalid' : '' }}"
                                   placeholder="Au moins 8 caractères" required>
                            <button type="button" class="toggle-password" onclick="togglePwd('password')">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                        </div>
                        @error('password')<span class="error-msg">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label>Confirmation <span class="required">*</span></label>
                        <div class="input-password">
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                   placeholder="Retaper le mot de passe" required>
                            <button type="button" class="toggle-password" onclick="togglePwd('password_confirmation')">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="alert alert-info" style="margin:8px 0">
                    Astuce : utilisez au moins 8 caractères, en mélangeant lettres, chiffres et symboles.
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        Mettre à jour le mot de passe
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Carte : Compte (lecture seule) --}}
    <div class="card mt-4">
        <div class="card-header">
            <h3>Informations du compte</h3>
        </div>
        <div class="card-body">
            <div class="profile-meta">
                <div class="profile-meta-item">
                    <span class="profile-meta-label">Rôle</span>
                    <span class="profile-meta-value">{{ $user->isAdmin() ? 'Administrateur' : 'Gérant' }}</span>
                </div>
                <div class="profile-meta-item">
                    <span class="profile-meta-label">Identifiant</span>
                    <span class="profile-meta-value">#{{ $user->id }}</span>
                </div>
                <div class="profile-meta-item">
                    <span class="profile-meta-label">Inscrit le</span>
                    <span class="profile-meta-value">{{ $user->created_at?->format('d/m/Y à H:i') }}</span>
                </div>
                @if($user->isManager())
                <div class="profile-meta-item">
                    <span class="profile-meta-label">Sociétés associées</span>
                    <span class="profile-meta-value">{{ $user->companies()->count() }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>

</div>

@push('styles')
<style>
    .profile-grid { max-width: 820px; }
    .profile-meta { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; }
    .profile-meta-item { display: flex; flex-direction: column; gap: 4px; padding: 12px 14px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; }
    .profile-meta-label { font-size: 0.75rem; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600; }
    .profile-meta-value { font-size: 0.95rem; color: #0f172a; font-weight: 600; }
    .input-password { position: relative; }
    .input-password input { padding-right: 40px; width: 100%; }
    .toggle-password { position: absolute; right: 8px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #64748b; padding: 4px; display: flex; }
    .toggle-password svg { width: 18px; height: 18px; }
    .toggle-password:hover { color: #0f172a; }
</style>
@endpush

@push('scripts')
<script>
function togglePwd(id) {
    const i = document.getElementById(id);
    if (!i) return;
    i.type = i.type === 'password' ? 'text' : 'password';
}
</script>
@endpush
@endsection
