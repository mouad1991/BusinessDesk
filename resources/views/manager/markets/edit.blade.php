@extends('layouts.app')
@section('title', 'Modifier — ' . Str::limit($market->title, 40))
@section('page-title', 'Modifier le marché')
@section('breadcrumb')
    <a href="{{ route('manager.markets.index') }}">Marchés</a> /
    <a href="{{ route('manager.markets.show', $market) }}">{{ Str::limit($market->title, 30) }}</a> / Modifier
@endsection

@section('content')
<form method="POST" action="{{ route('manager.markets.update', $market) }}" enctype="multipart/form-data">
    @csrf @method('PUT')
    @include('manager.markets._form')
    <div class="form-actions mt-4">
        <a href="{{ route('manager.markets.show', $market) }}" class="btn-secondary">Annuler</a>
        <button type="submit" class="btn-primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v14a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
            Enregistrer les modifications
        </button>
    </div>
</form>
@endsection
