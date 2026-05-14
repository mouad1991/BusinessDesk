@extends('layouts.app')
@section('title', 'Bons de livraison — ' . $company->name)
@section('page-title', 'Bons de livraison')
@section('breadcrumb')
    <a href="{{ route('manager.dashboard') }}">Accueil</a> /
    {{ $company->name }} / Bons de livraison
@endsection

@section('page-actions')
    <a href="{{ route('manager.delivery-notes.create') }}" class="btn-primary">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Nouveau bon de livraison
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
                    <th>Réf. commande</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($notes as $note)
                <tr>
                    <td><span class="doc-number">{{ $note->number }}</span></td>
                    <td>{{ $note->date->format('d/m/Y') }}</td>
                    <td>{{ $note->client->company_name }}</td>
                    <td class="text-muted">{{ $note->order_reference }}</td>
                    <td>@include('components.status-badge', ['status' => $note->status])</td>
                    <td>
                        <div class="action-group">
                            <a href="{{ route('manager.delivery-notes.show', $note) }}" class="btn-icon" title="Voir">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </a>
                            <a href="{{ route('manager.delivery-notes.pdf', $note) }}" class="btn-icon btn-icon-red" title="PDF" target="_blank">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                            </a>
                            <a href="{{ route('manager.delivery-notes.edit', $note) }}" class="btn-icon" title="Modifier">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </a>
                            <form method="POST" action="{{ route('manager.delivery-notes.destroy', $note) }}" style="display:inline"
                                  onsubmit="return confirm('Supprimer ce bon de livraison ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-icon btn-icon-red" title="Supprimer">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="empty-state">Aucun bon de livraison pour cette société.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($notes->hasPages())
    <div class="card-footer">{{ $notes->links() }}</div>
    @endif
</div>
@endsection
