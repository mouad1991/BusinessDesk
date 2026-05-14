@extends('layouts.app')
@section('title', 'BC ' . $purchaseOrder->number)
@section('page-title', $purchaseOrder->number)
@section('breadcrumb')
    <a href="{{ route('manager.purchase-orders.index') }}">Bons de commande</a> / {{ $purchaseOrder->number }}
@endsection

@section('page-actions')
    <a href="{{ route('manager.purchase-orders.pdf', $purchaseOrder) }}" class="btn-danger" target="_blank">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        Télécharger PDF
    </a>
    <a href="{{ route('manager.purchase-orders.edit', $purchaseOrder) }}" class="btn-secondary">Modifier</a>
    @if($purchaseOrder->status !== 'converted')
    <a href="{{ route('manager.purchase-orders.convert-to-invoice', $purchaseOrder) }}" class="btn-primary">
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
                        <p class="font-bold">{{ $purchaseOrder->client->company_name }}</p>
                        @if($purchaseOrder->client->address)<p>{{ $purchaseOrder->client->address }}</p>@endif
                        @if($purchaseOrder->client->phone)<p>Tél : {{ $purchaseOrder->client->phone }}</p>@endif
                        @if($purchaseOrder->client->ice)<p>ICE : {{ $purchaseOrder->client->ice }}</p>@endif
                    </div>
                    <div>
                        <h4 class="section-label">Informations</h4>
                        <table class="info-table">
                            <tr><td>Date</td><td>{{ $purchaseOrder->date->format('d/m/Y') }}</td></tr>
                            @if($purchaseOrder->order_date)
                            <tr><td>Date commande</td><td>{{ $purchaseOrder->order_date->format('d/m/Y') }}</td></tr>
                            @endif
                            @if($purchaseOrder->quote_reference)
                            <tr><td>Réf. devis</td><td>{{ $purchaseOrder->quote_reference }}</td></tr>
                            @endif
                            @if($purchaseOrder->payment_mode)
                            <tr><td>Paiement</td><td>{{ $purchaseOrder->payment_mode }}</td></tr>
                            @endif
                            @if($purchaseOrder->subject)
                            <tr><td>Objet</td><td>{{ $purchaseOrder->subject }}</td></tr>
                            @endif
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
                        @foreach($purchaseOrder->items as $item)
                        <tr>
                            <td>{{ $item->ref }}</td>
                            <td>{{ $item->description }}</td>
                            <td>{{ $item->unit }}</td>
                            <td class="text-right">{{ number_format($item->quantity, 2, ',', ' ') }}</td>
                            <td class="text-right">{{ number_format($item->unit_price_ht, 2, ',', ' ') }} DH</td>
                            <td class="text-right font-bold">{{ number_format($item->total_price_ht, 2, ',', ' ') }} DH</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Totals -->
        <div class="card mt-4">
            <div class="card-body">
                <div class="totals-view">
                    <div class="totals-row"><span>Total HT :</span><strong>{{ number_format($purchaseOrder->total_ht_before_discount, 2, ',', ' ') }} DH</strong></div>
                    @if($purchaseOrder->discount_percent > 0)
                    <div class="totals-row"><span>Remise ({{ $purchaseOrder->discount_percent }}%) :</span><strong>- {{ number_format($purchaseOrder->discount_amount, 2, ',', ' ') }} DH</strong></div>
                    <div class="totals-row"><span>Total HT après remise :</span><strong>{{ number_format($purchaseOrder->total_ht, 2, ',', ' ') }} DH</strong></div>
                    @endif
                    <div class="totals-row"><span>TVA ({{ $purchaseOrder->tva_rate }}%) :</span><strong>{{ number_format($purchaseOrder->tva_amount, 2, ',', ' ') }} DH</strong></div>
                    <div class="totals-row totals-ttc"><span>TOTAL TTC :</span><strong>{{ number_format($purchaseOrder->total_ttc, 2, ',', ' ') }} DH</strong></div>
                </div>
                <div class="amount-words mt-3">
                    <em>Arrêté le présent bon de commande à la somme de : <strong>{{ $purchaseOrder->amount_in_words }}</strong></em>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="doc-view-sidebar">
        <div class="card">
            <div class="card-body">
                @include('components.status-form', [
                    'status'    => $purchaseOrder->status,
                    'updateUrl' => route('manager.purchase-orders.update-status', $purchaseOrder),
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
