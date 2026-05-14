@extends('layouts.app')
@section('title', 'Clients — ' . $company->name)
@section('page-title', 'Clients')
@section('breadcrumb')
    <a href="{{ route('manager.dashboard') }}">Accueil</a> /
    {{ $company->name }} / Clients
@endsection

@section('page-actions')
    <a href="{{ route('manager.clients.create') }}" class="btn-primary">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Nouveau client
    </a>
@endsection

@section('content')
<div class="card">
    <div class="card-body p-0">
        <table class="table">
            <thead>
                <tr>
                    <th>Société</th>
                    <th>Adresse</th>
                    <th>Téléphone</th>
                    <th>ICE</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($clients as $client)
                <tr>
                    <td><strong>{{ $client->company_name }}</strong></td>
                    <td class="text-muted">{{ $client->address }}</td>
                    <td>{{ $client->phone }}</td>
                    <td><span class="text-muted">{{ $client->ice }}</span></td>
                    <td>
                        <div class="action-group">
                            <a href="{{ route('manager.clients.edit', $client) }}" class="btn-icon" title="Modifier">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </a>
                            <form method="POST" action="{{ route('manager.clients.destroy', $client) }}" style="display:inline"
                                  onsubmit="return confirm('Supprimer ce client ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-icon btn-icon-red" title="Supprimer">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="empty-state">Aucun client enregistré pour cette société.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($clients->hasPages())
    <div class="card-footer">{{ $clients->links() }}</div>
    @endif
</div>
@endsection
