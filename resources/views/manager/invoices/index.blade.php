@extends('layouts.app')
@section('title', 'Factures — ' . $company->name)
@section('page-title', 'Factures')
@section('breadcrumb')
    <a href="{{ route('manager.dashboard') }}">Accueil</a> /
    {{ $company->name }} / Factures
@endsection

@section('page-actions')
    <a href="{{ route('manager.invoices.create') }}" class="btn-primary">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Nouvelle facture
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
                    <th>Marché n°</th>
                    <th>Total TTC</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $invoice)
                <tr>
                    <td><span class="doc-number">{{ $invoice->number }}</span></td>
                    <td>{{ $invoice->date->format('d/m/Y') }}</td>
                    <td>{{ $invoice->client->company_name }}</td>
                    <td class="text-muted">{{ $invoice->marche_number }}</td>
                    <td><strong>{{ number_format($invoice->total_ttc, 2, ',', ' ') }} DH</strong></td>
                    <td>@include('components.status-badge', ['status' => $invoice->status])</td>
                    <td>
                        <div class="action-group">
                            <a href="{{ route('manager.invoices.show', $invoice) }}" class="btn-icon" title="Voir">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </a>
                            <a href="{{ route('manager.invoices.pdf', $invoice) }}" class="btn-icon btn-icon-red" title="PDF" target="_blank">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                            </a>
                            <a href="{{ route('manager.invoices.edit', $invoice) }}" class="btn-icon" title="Modifier">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </a>
                            <form method="POST" action="{{ route('manager.invoices.duplicate', $invoice) }}" style="display:inline"
                                  onsubmit="return confirm('Dupliquer cette facture ? Une nouvelle facture brouillon sera créée.')">
                                @csrf
                                <button type="submit" class="btn-icon" title="Dupliquer">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                                </button>
                            </form>
                            <form method="POST" action="{{ route('manager.invoices.destroy', $invoice) }}" style="display:inline"
                                  onsubmit="return confirm('Supprimer cette facture ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-icon btn-icon-red" title="Supprimer">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="empty-state">Aucune facture pour cette société.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($invoices->hasPages())
    <div class="card-footer">{{ $invoices->links() }}</div>
    @endif
</div>
@endsection
