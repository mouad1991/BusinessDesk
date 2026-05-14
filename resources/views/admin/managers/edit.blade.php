@extends('layouts.app')
@section('title', 'Modifier le gérant')
@section('page-title', 'Modifier le gérant')

@section('content')
<div class="card" style="max-width:600px">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.managers.update', $manager) }}">
            @csrf @method('PUT')
            <div class="form-row">
                <div class="form-group">
                    <label>Prénom <span class="required">*</span></label>
                    <input type="text" name="firstname" value="{{ old('firstname', $manager->firstname) }}" class="{{ $errors->has('firstname') ? 'is-invalid' : '' }}" required>
                    @error('firstname')<span class="error-msg">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>Nom <span class="required">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $manager->name) }}" class="{{ $errors->has('name') ? 'is-invalid' : '' }}" required>
                    @error('name')<span class="error-msg">{{ $message }}</span>@enderror
                </div>
            </div>
            <div class="form-group">
                <label>Email <span class="required">*</span></label>
                <input type="email" name="email" value="{{ old('email', $manager->email) }}" class="{{ $errors->has('email') ? 'is-invalid' : '' }}" required>
                @error('email')<span class="error-msg">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label>Mobile</label>
                <input type="text" name="mobile" value="{{ old('mobile', $manager->mobile) }}">
            </div>
            <div class="form-group">
                <label>Adresse</label>
                <input type="text" name="address" value="{{ old('address', $manager->address) }}">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Nouveau mot de passe <small class="text-muted">(laisser vide pour ne pas changer)</small></label>
                    <input type="password" name="password" class="{{ $errors->has('password') ? 'is-invalid' : '' }}">
                    @error('password')<span class="error-msg">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>Confirmer le nouveau mot de passe</label>
                    <input type="password" name="password_confirmation">
                </div>
            </div>
            <div class="form-actions">
                <a href="{{ route('admin.managers.index') }}" class="btn-secondary">Annuler</a>
                <button type="submit" class="btn-primary">Enregistrer</button>
            </div>
        </form>
    </div>
</div>
@endsection
