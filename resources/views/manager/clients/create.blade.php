@extends('layouts.app')
@section('title', 'Nouveau client')
@section('page-title', 'Nouveau client')
@section('breadcrumb')
    <a href="{{ route('manager.clients.index') }}">Clients</a> / Nouveau
@endsection

@section('content')
<form method="POST" action="{{ route('manager.clients.store') }}">
    @csrf

    <div class="card" style="max-width:640px">
        <div class="card-header"><h3>Informations client</h3></div>
        <div class="card-body">
            <div class="form-group">
                <label>Société <span class="required">*</span></label>
                <input type="text" name="company_name" value="{{ old('company_name') }}" class="{{ $errors->has('company_name') ? 'is-invalid' : '' }}" required placeholder="Nom de la société">
                @error('company_name')<span class="error-msg">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label>Adresse</label>
                <textarea name="address" rows="3" placeholder="Adresse complète">{{ old('address') }}</textarea>
                @error('address')<span class="error-msg">{{ $message }}</span>@enderror
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Téléphone</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" placeholder="06 XX XX XX XX">
                    @error('phone')<span class="error-msg">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>ICE</label>
                    <input type="text" name="ice" value="{{ old('ice') }}" placeholder="Identifiant commun de l'entreprise">
                    @error('ice')<span class="error-msg">{{ $message }}</span>@enderror
                </div>
            </div>
        </div>
    </div>

    <div class="form-actions mt-4">
        <a href="{{ route('manager.clients.index') }}" class="btn-secondary">Annuler</a>
        <button type="submit" class="btn-primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v14a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
            Créer le client
        </button>
    </div>
</form>
@endsection
