@extends('layouts.app')
@section('title', 'Modifier — ' . $client->company_name)
@section('page-title', 'Modifier le client')
@section('breadcrumb')
    <a href="{{ route('manager.clients.index') }}">Clients</a> / {{ $client->company_name }} / Modifier
@endsection

@section('content')
<form method="POST" action="{{ route('manager.clients.update', $client) }}">
    @csrf @method('PUT')

    <div class="card" style="max-width:640px">
        <div class="card-header"><h3>Informations client</h3></div>
        <div class="card-body">
            <div class="form-group">
                <label>Société <span class="required">*</span></label>
                <input type="text" name="company_name" value="{{ old('company_name', $client->company_name) }}" class="{{ $errors->has('company_name') ? 'is-invalid' : '' }}" required>
                @error('company_name')<span class="error-msg">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label>Adresse</label>
                <textarea name="address" rows="3">{{ old('address', $client->address) }}</textarea>
                @error('address')<span class="error-msg">{{ $message }}</span>@enderror
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Téléphone</label>
                    <input type="text" name="phone" value="{{ old('phone', $client->phone) }}">
                    @error('phone')<span class="error-msg">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>ICE</label>
                    <input type="text" name="ice" value="{{ old('ice', $client->ice) }}">
                    @error('ice')<span class="error-msg">{{ $message }}</span>@enderror
                </div>
            </div>
        </div>
    </div>

    <div class="form-actions mt-4">
        <a href="{{ route('manager.clients.index') }}" class="btn-secondary">Annuler</a>
        <button type="submit" class="btn-primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v14a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
            Enregistrer les modifications
        </button>
    </div>
</form>
@endsection
