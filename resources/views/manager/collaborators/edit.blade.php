@extends('layouts.app')
@section('title', 'Modifier — ' . $collaborator->full_name)
@section('page-title', 'Modifier l\'utilisateur')
@section('breadcrumb')
    <a href="{{ route('manager.collaborators.index') }}">Utilisateurs</a> / {{ $collaborator->full_name }}
@endsection

@section('content')
<form method="POST" action="{{ route('manager.collaborators.update', $collaborator) }}">
    @csrf @method('PUT')

    <div class="doc-form-grid">
        <div class="card">
            <div class="card-header"><h3>Informations personnelles</h3></div>
            <div class="card-body">
                <div class="form-row">
                    <div class="form-group">
                        <label>Prénom <span class="required">*</span></label>
                        <input type="text" name="firstname" value="{{ old('firstname', $collaborator->firstname) }}"
                               class="{{ $errors->has('firstname') ? 'is-invalid' : '' }}" required>
                        @error('firstname')<span class="error-msg">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label>Nom <span class="required">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $collaborator->name) }}"
                               class="{{ $errors->has('name') ? 'is-invalid' : '' }}" required>
                        @error('name')<span class="error-msg">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="form-group">
                    <label>Adresse email <span class="required">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $collaborator->email) }}"
                           class="{{ $errors->has('email') ? 'is-invalid' : '' }}" required>
                    @error('email')<span class="error-msg">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label>Nouveau mot de passe <span class="text-muted text-sm">(laisser vide pour ne pas changer)</span></label>
                    <input type="password" name="password"
                           class="{{ $errors->has('password') ? 'is-invalid' : '' }}"
                           placeholder="Minimum 8 caractères">
                    @error('password')<span class="error-msg">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>Confirmer le nouveau mot de passe</label>
                    <input type="password" name="password_confirmation" placeholder="Répéter le mot de passe">
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h3>Accès aux sociétés</h3></div>
            <div class="card-body">
                @if($companies->isEmpty())
                    <div class="alert alert-info">Aucune société disponible.</div>
                @else
                    <p class="text-muted text-sm" style="margin-bottom:12px">
                        Gérez les sociétés auxquelles cet utilisateur a accès.
                    </p>
                    @foreach($companies as $company)
                    <div class="form-check" style="margin-bottom:10px">
                        <input type="checkbox" id="co_{{ $company->id }}" name="company_ids[]"
                               value="{{ $company->id }}"
                               {{ in_array($company->id, old('company_ids', $selectedCompanyIds)) ? 'checked' : '' }}>
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
        <button type="submit" class="btn-primary">Enregistrer les modifications</button>
    </div>
</form>
@endsection
