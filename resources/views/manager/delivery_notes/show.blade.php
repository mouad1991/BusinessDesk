@extends('layouts.app')
@section('title', 'BL ' . $deliveryNote->number)
@section('page-title', $deliveryNote->number)
@section('breadcrumb')
    <a href="{{ route('manager.delivery-notes.index') }}">Bons de livraison</a> / {{ $deliveryNote->number }}
@endsection

@section('page-actions')
    <a href="{{ route('manager.delivery-notes.pdf', $deliveryNote) }}" class="btn-danger" target="_blank">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        Télécharger PDF
    </a>
    <a href="{{ route('manager.delivery-notes.edit', $deliveryNote) }}" class="btn-secondary">Modifier</a>
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
                        <p class="font-bold">{{ $deliveryNote->client->company_name }}</p>
                        @if($deliveryNote->client->address)<p>{{ $deliveryNote->client->address }}</p>@endif
                        @if($deliveryNote->client->phone)<p>Tél : {{ $deliveryNote->client->phone }}</p>@endif
                        @if($deliveryNote->client->ice)<p>ICE : {{ $deliveryNote->client->ice }}</p>@endif
                        @if($deliveryNote->delivery_address)
                        <p class="mt-2"><strong>Adresse de livraison :</strong><br>{{ $deliveryNote->delivery_address }}</p>
                        @endif
                    </div>
                    <div>
                        <h4 class="section-label">Informations</h4>
                        <table class="info-table">
                            <tr><td>Date</td><td>{{ $deliveryNote->date->format('d/m/Y') }}</td></tr>
                            @if($deliveryNote->order_reference)
                            <tr><td>Réf. commande</td><td>{{ $deliveryNote->order_reference }}</td></tr>
                            @endif
                            @if($deliveryNote->quote_reference)
                            <tr><td>Réf. devis</td><td>{{ $deliveryNote->quote_reference }}</td></tr>
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
                            <th>Description</th>
                            <th>Unité</th>
                            <th class="text-right">Qté commandée</th>
                            <th class="text-right">Qté expédiée</th>
                            <th class="text-right">Qté en attente</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($deliveryNote->items as $item)
                        <tr>
                            <td>{{ $item->ref }}</td>
                            <td class="desc-html">{!! $item->description !!}</td>
                            <td>{{ $item->unit }}</td>
                            <td class="text-right">{{ number_format($item->qty_ordered, 0, ',', ' ') }}</td>
                            <td class="text-right font-bold">{{ number_format($item->qty_shipped, 0, ',', ' ') }}</td>
                            <td class="text-right text-muted">{{ number_format($item->qty_pending, 0, ',', ' ') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="doc-view-sidebar">
        <div class="card">
            <div class="card-body">
                @include('components.status-form', [
                    'status'    => $deliveryNote->status,
                    'updateUrl' => route('manager.delivery-notes.update-status', $deliveryNote),
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
