@extends('layouts.app')
@section('title', 'Tableau de bord')
@section('page-title', 'Tableau de bord')

@section('page-actions')
    <a href="{{ route('manager.companies.create') }}" class="btn-primary">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Nouvelle société
    </a>
@endsection

@section('content')
<div class="company-block">
    <div class="stats-grid stats-grid-4">
        <div class="stat-card stat-card-sm">
            <div class="stat-value">{{ $company->quotes_count }}</div>
            <div class="stat-label">Devis</div>
            <a href="{{ route('manager.quotes.index') }}" class="stat-link">Voir tout</a>
        </div>
        <div class="stat-card stat-card-sm">
            <div class="stat-value">{{ $company->invoices_count }}</div>
            <div class="stat-label">Factures</div>
            <a href="{{ route('manager.invoices.index') }}" class="stat-link">Voir tout</a>
        </div>
        <div class="stat-card stat-card-sm">
            <div class="stat-value">{{ $company->delivery_notes_count }}</div>
            <div class="stat-label">Bons de livraison</div>
            <a href="{{ route('manager.delivery-notes.index') }}" class="stat-link">Voir tout</a>
        </div>
        <div class="stat-card stat-card-sm">
            <div class="stat-value">{{ $company->purchase_orders_count }}</div>
            <div class="stat-label">Bons de commande</div>
            <a href="{{ route('manager.purchase-orders.index') }}" class="stat-link">Voir tout</a>
        </div>
    </div>

    {{-- Marchés summary --}}
    @if($data['markets']['total'] > 0)
    <div class="market-dashboard-block mt-3">
        <div class="market-dashboard-header">
            <span class="market-dashboard-title">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;vertical-align:-2px"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/></svg>
                Marchés
            </span>
            <a href="{{ route('manager.markets.index') }}" class="stat-link">Voir tout →</a>
        </div>
        <div class="market-dashboard-stats">
            <div class="mdb-stat"><span class="mdb-val">{{ $data['markets']['in_progress'] }}</span><span class="mdb-label">En cours</span></div>
            <div class="mdb-stat"><span class="mdb-val">{{ $data['markets']['total'] }}</span><span class="mdb-label">Total</span></div>
            <div class="mdb-stat"><span class="mdb-val text-success">{{ number_format($data['markets']['paid_amount'], 0, ',', ' ') }} DH</span><span class="mdb-label">Encaissé</span></div>
            <div class="mdb-stat"><span class="mdb-val {{ $data['markets']['remaining'] > 0 ? 'text-danger' : '' }}">{{ number_format($data['markets']['remaining'], 0, ',', ' ') }} DH</span><span class="mdb-label">Restant</span></div>
            <div class="mdb-stat"><span class="mdb-val">{{ $data['markets']['cautions'] }}</span><span class="mdb-label">Cautions déposées</span></div>
        </div>
    </div>
    @endif

    <div class="last-docs-grid">
        @if($data['lastQuote'])
        <div class="last-doc-card">
            <div class="last-doc-label">Dernier devis</div>
            <div class="last-doc-number">{{ $data['lastQuote']->number }}</div>
            <div class="last-doc-info">
                {{ $data['lastQuote']->client->company_name ?? '' }} —
                {{ number_format($data['lastQuote']->total_ttc, 2, ',', ' ') }} DH TTC
            </div>
            <div class="last-doc-date">{{ $data['lastQuote']->date->format('d/m/Y') }}</div>
            <a href="{{ route('manager.quotes.show', $data['lastQuote']) }}" class="btn-link">Voir →</a>
        </div>
        @endif

        @if($data['lastInvoice'])
        <div class="last-doc-card">
            <div class="last-doc-label">Dernière facture</div>
            <div class="last-doc-number">{{ $data['lastInvoice']->number }}</div>
            <div class="last-doc-info">
                {{ $data['lastInvoice']->client->company_name ?? '' }} —
                {{ number_format($data['lastInvoice']->total_ttc, 2, ',', ' ') }} DH TTC
            </div>
            <div class="last-doc-date">{{ $data['lastInvoice']->date->format('d/m/Y') }}</div>
            <a href="{{ route('manager.invoices.show', $data['lastInvoice']) }}" class="btn-link">Voir →</a>
        </div>
        @endif
    </div>

    <div class="quick-actions">
        <a href="{{ route('manager.quotes.create') }}" class="quick-btn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            Nouveau devis
        </a>
        <a href="{{ route('manager.invoices.create') }}" class="quick-btn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
            Nouvelle facture
        </a>
        <a href="{{ route('manager.delivery-notes.create') }}" class="quick-btn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 17H3a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11a2 2 0 0 1 2 2v3"/><rect x="9" y="11" width="14" height="10" rx="1"/></svg>
            Nouveau BL
        </a>
        <a href="{{ route('manager.purchase-orders.create') }}" class="quick-btn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
            Nouveau BC
        </a>
    </div>
</div>
@endsection
