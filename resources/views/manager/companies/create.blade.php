@extends('layouts.app')
@section('title', 'Nouvelle société')
@section('page-title', 'Nouvelle société')
@section('breadcrumb')
    <a href="{{ route('manager.companies.index') }}">Sociétés</a> / Nouvelle
@endsection

@section('content')
<form method="POST" action="{{ route('manager.companies.store') }}" enctype="multipart/form-data">
    @csrf

    <div class="doc-form-grid">
        <!-- Informations générales -->
        <div class="card">
            <div class="card-header"><h3>Informations générales</h3></div>
            <div class="card-body">
                <div class="form-group">
                    <label>Raison sociale <span class="required">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" class="{{ $errors->has('name') ? 'is-invalid' : '' }}" required placeholder="Nom de la société">
                    @error('name')<span class="error-msg">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>Adresse <span class="required">*</span></label>
                    <textarea name="address" rows="3" class="{{ $errors->has('address') ? 'is-invalid' : '' }}" required placeholder="Adresse complète">{{ old('address') }}</textarea>
                    @error('address')<span class="error-msg">{{ $message }}</span>@enderror
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Téléphone <span class="required">*</span></label>
                        <input type="text" name="phone" value="{{ old('phone') }}" class="{{ $errors->has('phone') ? 'is-invalid' : '' }}" required placeholder="06 XX XX XX XX">
                        @error('phone')<span class="error-msg">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label>Email <span class="required">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}" class="{{ $errors->has('email') ? 'is-invalid' : '' }}" required placeholder="contact@entreprise.ma">
                        @error('email')<span class="error-msg">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="form-group">
                    <label>Préfixe documents <span class="required">*</span></label>
                    <input type="text" name="doc_prefix" value="{{ old('doc_prefix') }}" class="{{ $errors->has('doc_prefix') ? 'is-invalid' : '' }}" required placeholder="Ex : ENT">
                    @error('doc_prefix')<span class="error-msg">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>Logo</label>
                    <input type="file" name="logo" accept="image/*">
                    @error('logo')<span class="error-msg">{{ $message }}</span>@enderror
                </div>
            </div>
        </div>

        <!-- Informations légales & bancaires -->
        <div class="card">
            <div class="card-header"><h3>Informations légales & bancaires</h3></div>
            <div class="card-body">
                <div class="form-row">
                    <div class="form-group">
                        <label>RC</label>
                        <input type="text" name="rc" value="{{ old('rc') }}" placeholder="Registre de commerce">
                    </div>
                    <div class="form-group">
                        <label>ICE</label>
                        <input type="text" name="ice" value="{{ old('ice') }}" placeholder="Identifiant commun de l'entreprise">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>IF</label>
                        <input type="text" name="if_number" value="{{ old('if_number') }}" placeholder="Identifiant fiscal">
                    </div>
                    <div class="form-group">
                        <label>TP</label>
                        <input type="text" name="tp" value="{{ old('tp') }}" placeholder="Taxe professionnelle">
                    </div>
                </div>
                <div class="form-group">
                    <label>Capital</label>
                    <input type="text" name="capital" value="{{ old('capital') }}" placeholder="Capital social">
                </div>
                <hr>
                <div class="form-group">
                    <label>Intitulé du compte bancaire</label>
                    <input type="text" name="bank_account_name" value="{{ old('bank_account_name') }}" placeholder="Nom du titulaire du compte">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Banque</label>
                        <input type="text" name="bank" value="{{ old('bank') }}" placeholder="Nom de la banque">
                    </div>
                    <div class="form-group">
                        <label>RIB</label>
                        <input type="text" name="rib" value="{{ old('rib') }}" placeholder="Relevé d'identité bancaire">
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('components.pdf-template-picker', ['selectedTemplate' => old('pdf_template', 'modern')])

    <div class="form-actions mt-4">
        <a href="{{ route('manager.companies.index') }}" class="btn-secondary">Annuler</a>
        <button type="submit" class="btn-primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v14a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
            Créer la société
        </button>
    </div>
</form>
@endsection
