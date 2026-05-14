@extends('layouts.app')
@section('title', 'Bons de commande — ' . $company->name)
@section('page-title', 'Bons de commande')
@section('breadcrumb')
    <a href="{{ route('manager.dashboard') }}">Accueil</a> /
    {{ $company->name }} / Bons de commande
@endsection

@section('page-actions')
    <a href="{{ route('manager.purchase-orders.create') }}" class="btn-primary">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Nouveau bon de commande
    </a>
@endsection

@section('content')
<div class="card">
    <div class="card-body p-0">
        <table class="table">
            <thead>
                <tr>
                    <th>N°</th>
                    <th>Date</th>
                    <th>Client</th>
                    <th>Réf. devis</th>
                    <th>Total TTC</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                <tr>
                    <td><span class="doc-number">{{ $order->number }}</span></td>
                    <td>{{ $order->date->format('d/m/Y') }}</td>
                    <td>{{ $order->client->company_name }}</td>
                    <td class="text-muted">{{ $order->quote_reference }}</td>
                    <td><strong>{{ number_format($order->total_ttc, 2, ',', ' ') }} DH</strong></td>
                    <td>@include('components.status-badge', ['status' => $order->status])</td>
                    <td>
                        <div class="action-group">
                            <a href="{{ route('manager.purchase-orders.show', $order) }}" class="btn-icon" title="Voir">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </a>
                            <a href="{{ route('manager.purchase-orders.pdf', $order) }}" class="btn-icon btn-icon-red" title="PDF" target="_blank">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                            </a>
                            <a href="{{ route('manager.purchase-orders.edit', $order) }}" class="btn-icon" title="Modifier">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </a>
                            @if($order->status !== 'converted')
                            <a href="{{ route('manager.purchase-orders.convert-to-invoice', $order) }}" class="btn-icon btn-icon-green" title="Convertir en facture">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="17 1 21 5 17 9"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/><polyline points="7 23 3 19 7 15"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/></svg>
                            </a>
                            @endif
                            <form method="POST" action="{{ route('manager.purchase-orders.destroy', $order) }}" style="display:inline"
                                  onsubmit="return confirm('Supprimer ce bon de commande ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-icon btn-icon-red" title="Supprimer">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="empty-state">Aucun bon de commande pour cette société.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($orders->hasPages())
    <div class="card-footer">{{ $orders->links() }}</div>
    @endif
</div>
@endsection
