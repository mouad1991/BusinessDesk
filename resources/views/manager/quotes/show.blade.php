@extends('layouts.app')
@section('title', 'Devis ' . $quote->number)
@section('page-title', $quote->number)
@section('breadcrumb')
    <a href="{{ route('manager.quotes.index') }}">Devis</a> / {{ $quote->number }}
@endsection

@section('page-actions')
    <a href="{{ route('manager.quotes.pdf', $quote) }}" class="btn-danger" target="_blank">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        Télécharger PDF
    </a>
    <a href="{{ route('manager.quotes.edit', $quote) }}" class="btn-secondary">Modifier</a>
    @if($quote->status !== 'converted')
    <a href="{{ route('manager.quotes.convert-to-invoice', $quote) }}" class="btn-primary">
        Convertir en facture
    </a>
    @endif
@endsection

@section('content')
<div class="doc-view-grid">
    <div class="doc-view-main">
        <!-- Header info -->
        <div class="doc-info-grid card">
            <div class="card-body">
                <div class="doc-info-2col">
                    <div>
                        <h4 class="section-label">Client / Destinataire</h4>
                        <p class="font-bold">{{ $quote->client->company_name }}</p>
                        @if($quote->client->address)<p>{{ $quote->client->address }}</p>@endif
                        @if($quote->client->phone)<p>Tél : {{ $quote->client->phone }}</p>@endif
                        @if($quote->client->ice)<p>ICE : {{ $quote->client->ice }}</p>@endif
                    </div>
                    <div>
                        <h4 class="section-label">Informations</h4>
                        <table class="info-table">
                            <tr><td>Date</td><td>{{ $quote->date->format('d/m/Y') }}</td></tr>
                            <tr><td>Validité</td><td>{{ $quote->validity_days }} jours</td></tr>
                            @if($quote->payment_mode)<tr><td>Paiement</td><td>{{ $quote->payment_mode }}</td></tr>@endif
                            <tr><td>Objet</td><td>{{ $quote->subject }}</td></tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Items table -->
        <div class="card mt-4">
            <div class="card-body p-0">
                <table class="table items-view-table">
                    <thead>
                        <tr>
                            <th>Réf.</th>
                            <th>Désignation / Description</th>
                            <th>Unité</th>
                            <th class="text-right">Qté</th>
                            <th class="text-right">P.U HT</th>
                            <th class="text-right">P.T HT</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($quote->items as $item)
                        @if($item->is_category)
                        <tr class="item-category-view">
                            <td colspan="6">{{ $item->description }}</td>
                        </tr>
                        @else
                        <tr>
                            <td>{{ $item->ref }}</td>
                            <td class="desc-html">{!! $item->description !!}</td>
                            <td>{{ $item->unit }}</td>
                            <td class="text-right">{{ number_format($item->quantity, 2, ',', ' ') }}</td>
                            <td class="text-right">{{ number_format($item->unit_price_ht, 2, ',', ' ') }} DH</td>
                            <td class="text-right font-bold">{{ number_format($item->total_price_ht, 2, ',', ' ') }} DH</td>
                        </tr>
                        @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Totals -->
        <div class="card mt-4">
            <div class="card-body">
                <div class="totals-view">
                    <div class="totals-row"><span>Total HT :</span><strong>{{ number_format($quote->total_ht_before_discount, 2, ',', ' ') }} DH</strong></div>
                    @if($quote->discount_percent > 0)
                    <div class="totals-row"><span>Remise ({{ $quote->discount_percent }}%) :</span><strong>- {{ number_format($quote->discount_amount, 2, ',', ' ') }} DH</strong></div>
                    <div class="totals-row"><span>Total HT après remise :</span><strong>{{ number_format($quote->total_ht, 2, ',', ' ') }} DH</strong></div>
                    @endif
                    <div class="totals-row"><span>TVA ({{ $quote->tva_rate }}%) :</span><strong>{{ number_format($quote->tva_amount, 2, ',', ' ') }} DH</strong></div>
                    <div class="totals-row totals-ttc"><span>TOTAL TTC :</span><strong>{{ number_format($quote->total_ttc, 2, ',', ' ') }} DH</strong></div>
                </div>
                <div class="amount-words mt-3">
                    <em>Arrêtée le présent devis à la somme de : <strong>{{ $quote->amount_in_words }}</strong></em>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="doc-view-sidebar">
        <div class="card">
            <div class="card-body">
                @include('components.status-form', [
                    'status'    => $quote->status,
                    'updateUrl' => route('manager.quotes.update-status', $quote),
                ])
            </div>
        </div>
        <div class="card mt-3">
            <div class="card-body">
                <h4>Société</h4>
                <p class="font-bold">{{ $company->name }}</p>
                <p class="text-muted text-sm">{{ $company->address }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
