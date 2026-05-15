@extends('layouts.app')
@section('title', 'Nouvel utilisateur associé')
@section('page-title', 'Nouvel utilisateur associé')
@section('breadcrumb')
    <a href="{{ route('manager.collaborators.index') }}">Utilisateurs</a> / Nouveau
@endsection

@section('content')
<form method="POST" action="{{ route('manager.collaborators.store') }}">
    @csrf

    <div class="doc-form-grid">
        <div class="card">
            <div class="card-header"><h3>Informations personnelles</h3></div>
            <div class="card-body">
                <div class="form-row">
                    <div class="form-group">
                        <label>Prénom <span class="required">*</span></label>
                        <input type="text" name="firstname" value="{{ old('firstname') }}"
                               class="{{ $errors->has('firstname') ? 'is-invalid' : '' }}" required autofocus>
                        @error('firstname')<span class="error-msg">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label>Nom <span class="required">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}"
                               class="{{ $errors->has('name') ? 'is-invalid' : '' }}" required>
                        @error('name')<span class="error-msg">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="form-group">
                    <label>Adresse email <span class="required">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="{{ $errors->has('email') ? 'is-invalid' : '' }}" required>
                    @error('email')<span class="error-msg">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>Mot de passe <span class="required">*</span></label>
                    <input type="password" name="password"
                           class="{{ $errors->has('password') ? 'is-invalid' : '' }}" required
                           placeholder="Minimum 8 caractères">
                    @error('password')<span class="error-msg">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>Confirmer le mot de passe <span class="required">*</span></label>
                    <input type="password" name="password_confirmation" required placeholder="Répéter le mot de passe">
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h3>Accès aux sociétés</h3></div>
            <div class="card-body">
                @if($companies->isEmpty())
                    <div class="alert alert-info">Vous n'avez pas encore de société. Créez-en une d'abord.</div>
                @else
                    <p class="text-muted text-sm" style="margin-bottom:12px">
                        Sélectionnez les sociétés auxquelles cet utilisateur aura accès.
                    </p>
                    @foreach($companies as $company)
                    <div class="form-check" style="margin-bottom:10px">
                        <input type="checkbox" id="co_{{ $company->id }}" name="company_ids[]"
                               value="{{ $company->id }}"
                               {{ in_array($company->id, old('company_ids', [])) ? 'checked' : '' }}>
                        <label for="co_{{ $company->id }}" style="font-weight:500">
                            {{ $company->name }}
                            <span class="text-muted text-sm">— {{ $company->doc_prefix }}</span>
                        </label>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    <div class="form-actions mt-4">
        <a href="{{ route('manager.collaborators.index') }}" class="btn-secondary">Annuler</a>
        <button type="submit" class="btn-primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            Créer l'utilisateur
        </button>
    </div>
</form>
@endsection
