@extends('layouts.app')
@section('title', 'Nouveau gérant')
@section('page-title', 'Nouveau gérant')

@section('content')
<div class="card" style="max-width:600px">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.managers.store') }}">
            @csrf
            <div class="form-row">
                <div class="form-group">
                    <label>Prénom <span class="required">*</span></label>
                    <input type="text" name="firstname" value="{{ old('firstname') }}" class="{{ $errors->has('firstname') ? 'is-invalid' : '' }}" required>
                    @error('firstname')<span class="error-msg">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>Nom <span class="required">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" class="{{ $errors->has('name') ? 'is-invalid' : '' }}" required>
                    @error('name')<span class="error-msg">{{ $message }}</span>@enderror
                </div>
            </div>
            <div class="form-group">
                <label>Email <span class="required">*</span></label>
                <input type="email" name="email" value="{{ old('email') }}" class="{{ $errors->has('email') ? 'is-invalid' : '' }}" required>
                @error('email')<span class="error-msg">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label>Mobile</label>
                <input type="text" name="mobile" value="{{ old('mobile') }}">
            </div>
            <div class="form-group">
                <label>Adresse</label>
                <input type="text" name="address" value="{{ old('address') }}">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Mot de passe <span class="required">*</span></label>
                    <input type="password" name="password" class="{{ $errors->has('password') ? 'is-invalid' : '' }}" required>
                    @error('password')<span class="error-msg">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>Confirmer le mot de passe <span class="required">*</span></label>
                    <input type="password" name="password_confirmation" required>
                </div>
            </div>
            <div class="form-actions">
                <a href="{{ route('admin.managers.index') }}" class="btn-secondary">Annuler</a>
                <button type="submit" class="btn-primary">Créer le gérant</button>
            </div>
        </form>
    </div>
</div>
@endsection
