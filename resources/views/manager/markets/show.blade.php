@extends('layouts.app')
@section('title', Str::limit($market->title, 50))
@section('page-title', Str::limit($market->title, 60))
@section('breadcrumb')
    <a href="{{ route('manager.markets.index') }}">Marchés</a> / {{ Str::limit($market->title, 40) }}
@endsection

@section('page-actions')
    <a href="{{ route('manager.markets.edit', $market) }}" class="btn-secondary">Modifier</a>
    <form method="POST" action="{{ route('manager.markets.destroy', $market) }}"
        style="display:inline" onsubmit="return confirm('Supprimer définitivement ce marché ?')">
        @csrf @method('DELETE')
        <button type="submit" class="btn-danger">Supprimer</button>
    </form>
@endsection

@section('content')
<div class="doc-view-grid">
    <div class="doc-view-main">

        {{-- Informations principales --}}
        <div class="card">
            <div class="card-body">
                <div class="doc-info-2col">
                    <div>
                        <h4 class="section-label">Informations principales</h4>
                        <table class="info-table">
                            <tr><td>Type</td><td>
                                <span class="badge {{ $market->market_type === 'public' ? 'badge-blue' : 'badge-purple' }}">{{ $market->market_type_label }}</span>
                            </td></tr>
                            <tr><td>Catégorie</td><td>{{ $market->category_label }}</td></tr>
                            <tr><td>Client</td><td>{{ $market->client?->company_name ?? '—' }}</td></tr>
                            @if($market->start_date || $market->end_date)
                            <tr><td>Période</td><td>
                                {{ $market->start_date?->format('d/m/Y') ?? '…' }}
                                @if($market->start_date && $market->end_date) → @endif
                                {{ $market->end_date?->format('d/m/Y') ?? '…' }}
                            </td></tr>
                            @endif
                        </table>
                    </div>
                    <div>
                        <h4 class="section-label">Suivi financier</h4>
                        <table class="info-table">
                            <tr>
                                <td>Montant total</td>
                                <td class="font-bold">{{ number_format($market->total_amount, 2, ',', ' ') }} DH</td>
                            </tr>
                            <tr>
                                <td>Facturé</td>
                                <td>{{ number_format($market->invoiced_amount, 2, ',', ' ') }} DH</td>
                            </tr>
                            <tr>
                                <td>Réglé</td>
                                <td class="text-success font-bold">{{ number_format($market->paid_amount, 2, ',', ' ') }} DH</td>
                            </tr>
                            <tr>
                                <td>Reste à encaisser</td>
                                <td class="font-bold {{ $market->remaining_amount > 0 ? 'text-danger' : 'text-success' }}">
                                    {{ number_format($market->remaining_amount, 2, ',', ' ') }} DH
                                </td>
                            </tr>
                        </table>
                        {{-- Barre de progression --}}
                        @if($market->total_amount > 0)
                        @php $pct = min(100, round(($market->paid_amount / $market->total_amount) * 100)); @endphp
                        <div class="market-progress-wrap mt-2">
                            <div class="market-progress-bar" style="width:{{ $pct }}%"></div>
                        </div>
                        <div class="text-muted text-sm text-right">{{ $pct }}% encaissé</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Caution + Notes --}}
        <div class="doc-info-2col mt-4" style="gap:16px;">
            @if($market->caution_amount || $market->caution_status)
            <div class="card">
                <div class="card-header"><h3>Caution</h3></div>
                <div class="card-body">
                    <table class="info-table">
                        @if($market->caution_amount)
                        <tr><td>Montant</td><td class="font-bold">{{ number_format($market->caution_amount, 2, ',', ' ') }} DH</td></tr>
                        @endif
                        @if($market->caution_status)
                        @php $cautionBadge = match($market->caution_status) { 'deposited' => 'badge-orange', 'recovered' => 'badge-green', default => 'badge-gray' }; @endphp
                        <tr><td>Statut</td><td><span class="badge {{ $cautionBadge }}">{{ $market->caution_status_label }}</span></td></tr>
                        @endif
                    </table>
                </div>
            </div>
            @endif

            @if($market->notes)
            <div class="card">
                <div class="card-header"><h3>Notes / Observations</h3></div>
                <div class="card-body">
                    <p style="white-space:pre-line; font-size:13px; color:#334155;">{{ $market->notes }}</p>
                </div>
            </div>
            @endif
        </div>

        {{-- Pièces jointes --}}
        @if($market->attachments->isNotEmpty())
        <div class="card mt-4">
            <div class="card-header"><h3>Pièces jointes ({{ $market->attachments->count() }})</h3></div>
            <div class="card-body">
                <div class="attachments-grid">
                    @foreach($market->attachments as $att)
                    <div class="attachment-card">
                        <div class="attachment-ext">{{ strtoupper($att->extension) }}</div>
                        <div class="attachment-info">
                            <span class="attachment-name-text">{{ $att->original_name }}</span>
                            <span class="attachment-date text-muted text-sm">{{ $att->created_at->format('d/m/Y') }}</span>
                        </div>
                        <div class="attachment-actions">
                            <a href="{{ route('manager.market-attachments.download', [$market, $att]) }}"
                               class="btn-sm btn-secondary" target="_blank">Télécharger</a>
                            <form method="POST"
                                  action="{{ route('manager.market-attachments.destroy', [$market, $att]) }}"
                                  style="display:inline" onsubmit="return confirm('Supprimer ce fichier ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-sm btn-danger">✕</button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

    </div>

    {{-- Sidebar --}}
    <div class="doc-view-sidebar">
        <div class="card">
            <div class="card-body">
                <h4 style="margin-bottom:10px;">Statut du marché</h4>
                <span class="badge badge-lg {{ $market->status_badge_class }}">{{ $market->status_label }}</span>

                <form method="POST" action="{{ route('manager.markets.update-status', $market) }}" class="status-form mt-3">
                    @csrf @method('PATCH')
                    <select name="status">
                        @foreach(\App\Models\Market::statusOptions() as $val => $label)
                        <option value="{{ $val }}" {{ $market->status === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn-status">Changer</button>
                </form>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-body">
                <h4 style="margin-bottom:10px;">Résumé financier</h4>
                <div class="sidebar-stat"><span class="sidebar-stat-label">Total</span><span class="sidebar-stat-value">{{ number_format($market->total_amount, 0, ',', ' ') }} DH</span></div>
                <div class="sidebar-stat mt-1"><span class="sidebar-stat-label">Réglé</span><span class="sidebar-stat-value text-success">{{ number_format($market->paid_amount, 0, ',', ' ') }} DH</span></div>
                <div class="sidebar-stat mt-1"><span class="sidebar-stat-label">Reste</span><span class="sidebar-stat-value {{ $market->remaining_amount > 0 ? 'text-danger' : 'text-success' }}">{{ number_format($market->remaining_amount, 0, ',', ' ') }} DH</span></div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-body">
                <h4>Société</h4>
                <p class="font-bold">{{ $company->name }}</p>
                <p class="text-muted text-sm">{{ $company->address }}</p>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-body">
                <p class="text-muted text-sm">Créé le {{ $market->created_at->format('d/m/Y à H:i') }}</p>
                @if($market->created_at != $market->updated_at)
                <p class="text-muted text-sm">Modifié le {{ $market->updated_at->format('d/m/Y à H:i') }}</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
