@extends('layouts.app')
@section('title', 'Marchés — ' . $company->name)
@section('page-title', 'Marchés')
@section('breadcrumb')
    {{ $company->name }} / Marchés
@endsection

@section('page-actions')
    <a href="{{ route('manager.markets.export', request()->query()) }}"
       class="btn-secondary">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:15px;height:15px"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        Exporter XLS
    </a>
    <a href="{{ route('manager.markets.create') }}" class="btn-primary">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:15px;height:15px"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Nouveau marché
    </a>
@endsection

@section('content')

{{-- Filtres --}}
<form method="GET" action="{{ route('manager.markets.index') }}" class="market-filters card">
    <div class="card-body">
        <div class="market-filters-grid">
            <div class="form-group mb-0">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Rechercher par objet…" class="filter-input">
            </div>
            <div class="form-group mb-0">
                <select name="status" class="filter-input">
                    <option value="">Tous les statuts</option>
                    @foreach(\App\Models\Market::statusOptions() as $val => $label)
                    <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group mb-0">
                <select name="market_type" class="filter-input">
                    <option value="">Public / Privé</option>
                    @foreach(\App\Models\Market::typeOptions() as $val => $label)
                    <option value="{{ $val }}" {{ request('market_type') === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group mb-0">
                <select name="client_id" class="filter-input">
                    <option value="">Tous les clients</option>
                    @foreach($clients as $client)
                    <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>{{ $client->company_name }}</option>
                    @endforeach
                </select>
            </div>
            <div style="display:flex; gap:8px;">
                <button type="submit" class="btn-primary btn-sm">Filtrer</button>
                @if(request()->hasAny(['search','status','market_type','client_id']))
                <a href="{{ route('manager.markets.index') }}" class="btn-secondary btn-sm">Réinitialiser</a>
                @endif
            </div>
        </div>
    </div>
</form>

{{-- Stats rapides --}}
@php
$allMarkets = $company->markets();
$statsInProgress = $allMarkets->clone()->where('status','in_progress')->count();
$statsTotal = $allMarkets->clone()->count();
$statsTotalAmount = $allMarkets->clone()->sum('total_amount');
$statsPaid = $allMarkets->clone()->sum('paid_amount');
$statsRemaining = $allMarkets->clone()->sum('remaining_amount');
$statsCautions = $allMarkets->clone()->where('caution_status','deposited')->count();
@endphp
<div class="market-stats-bar">
    <div class="mstat"><span class="mstat-val">{{ $statsInProgress }}</span><span class="mstat-label">En cours</span></div>
    <div class="mstat"><span class="mstat-val">{{ $statsTotal }}</span><span class="mstat-label">Total marchés</span></div>
    <div class="mstat"><span class="mstat-val">{{ number_format($statsTotalAmount, 0, ',', ' ') }} DH</span><span class="mstat-label">Valeur totale</span></div>
    <div class="mstat"><span class="mstat-val text-success">{{ number_format($statsPaid, 0, ',', ' ') }} DH</span><span class="mstat-label">Encaissé</span></div>
    <div class="mstat"><span class="mstat-val {{ $statsRemaining > 0 ? 'text-danger' : '' }}">{{ number_format($statsRemaining, 0, ',', ' ') }} DH</span><span class="mstat-label">Restant</span></div>
    <div class="mstat"><span class="mstat-val">{{ $statsCautions }}</span><span class="mstat-label">Cautions déposées</span></div>
</div>

{{-- Tableau --}}
<div class="card mt-3">
    <div class="card-body p-0">
        @if($markets->isEmpty())
        <div class="empty-card" style="padding:40px">
            <div class="empty-icon">📋</div>
            <h3>Aucun marché trouvé</h3>
            <p>Créez votre premier marché pour commencer le suivi.</p>
            <a href="{{ route('manager.markets.create') }}" class="btn-primary">Créer un marché</a>
        </div>
        @else
        <div class="table-responsive">
        <table class="table markets-table">
            <thead>
                <tr>
                    <th>Objet</th>
                    <th>Client</th>
                    <th>Type</th>
                    <th>Statut</th>
                    <th>Période</th>
                    <th class="text-right">Montant total</th>
                    <th class="text-right">Réglé</th>
                    <th class="text-right">Reste</th>
                    <th>Caution</th>
                    <th style="width:90px"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($markets as $market)
                <tr>
                    <td>
                        <a href="{{ route('manager.markets.show', $market) }}" class="font-bold market-title-link">
                            {{ Str::limit($market->title, 50) }}
                        </a>
                        <div class="text-muted text-sm">{{ $market->category_label }}</div>
                    </td>
                    <td>{{ $market->client?->company_name ?? '—' }}</td>
                    <td><span class="badge {{ $market->market_type === 'public' ? 'badge-blue' : 'badge-purple' }}">{{ $market->market_type_label }}</span></td>
                    <td><span class="badge {{ $market->status_badge_class }}">{{ $market->status_label }}</span></td>
                    <td class="text-sm">
                        @if($market->start_date){{ $market->start_date->format('d/m/Y') }}@endif
                        @if($market->start_date && $market->end_date) → @endif
                        @if($market->end_date){{ $market->end_date->format('d/m/Y') }}@endif
                        @if(!$market->start_date && !$market->end_date)—@endif
                    </td>
                    <td class="text-right font-bold">{{ number_format($market->total_amount, 2, ',', ' ') }} DH</td>
                    <td class="text-right text-success">{{ number_format($market->paid_amount, 2, ',', ' ') }} DH</td>
                    <td class="text-right {{ $market->remaining_amount > 0 ? 'text-danger' : 'text-success' }}">
                        {{ number_format($market->remaining_amount, 2, ',', ' ') }} DH
                    </td>
                    <td>
                        @if($market->caution_status)
                        @php $cautionBadge = match($market->caution_status) { 'deposited' => 'badge-orange', 'recovered' => 'badge-green', default => 'badge-gray' }; @endphp
                        <span class="badge {{ $cautionBadge }}">{{ $market->caution_status_label }}</span>
                        @else
                        <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td class="text-right">
                        <a href="{{ route('manager.markets.show', $market) }}" class="btn-sm btn-secondary">Voir</a>
                        <a href="{{ route('manager.markets.edit', $market) }}" class="btn-sm btn-secondary">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:12px;height:12px"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        </a>
                        <form method="POST" action="{{ route('manager.markets.destroy', $market) }}" style="display:inline" onsubmit="return confirm('Supprimer ce marché ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-sm btn-danger">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:12px;height:12px"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>
        <div class="p-3">{{ $markets->links() }}</div>
        @endif
    </div>
</div>
@endsection
