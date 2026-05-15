@extends('layouts.app')
@section('title', 'Utilisateurs associés')
@section('page-title', 'Utilisateurs associés')
@section('breadcrumb')
    <a href="{{ route('manager.dashboard') }}">Accueil</a> / Utilisateurs
@endsection

@section('page-actions')
    @if($collaborators->count() < 2)
    <a href="{{ route('manager.collaborators.create') }}" class="btn-primary">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Nouvel utilisateur
    </a>
    @endif
@endsection

@section('content')

<div class="card" style="margin-bottom:16px; background: #eff6ff; border-color: #bfdbfe;">
    <div class="card-body" style="display:flex; align-items:center; gap:12px; padding:12px 16px;">
        <svg viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" style="width:20px;height:20px;flex-shrink:0"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <span style="font-size:13px;color:#1d4ed8;">
            Vous pouvez créer jusqu'à <strong>2 utilisateurs associés</strong>.
            Ils accèdent à votre espace avec les sociétés que vous leur attribuez.
            <strong>{{ $collaborators->count() }}/2 utilisateurs</strong> créés.
        </span>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table">
            <thead>
                <tr>
                    <th>Utilisateur</th>
                    <th>Email</th>
                    <th>Sociétés attribuées</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($collaborators as $collab)
                <tr>
                    <td>
                        <div class="user-cell">
                            <div class="avatar-sm">{{ strtoupper(substr($collab->firstname, 0, 1)) }}</div>
                            <div>
                                <div class="font-bold">{{ $collab->full_name }}</div>
                                <div class="text-muted text-sm">Collaborateur</div>
                            </div>
                        </div>
                    </td>
                    <td>{{ $collab->email }}</td>
                    <td>
                        @forelse($collab->accessibleCompanies as $co)
                            <span class="badge badge-blue" style="margin:2px">{{ $co->name }}</span>
                        @empty
                            <span class="text-muted text-sm">Aucune société attribuée</span>
                        @endforelse
                    </td>
                    <td>
                        <div class="action-group">
                            <a href="{{ route('manager.collaborators.edit', $collab) }}" class="btn-icon" title="Modifier">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </a>
                            <form method="POST" action="{{ route('manager.collaborators.destroy', $collab) }}" style="display:inline"
                                  onsubmit="return confirm('Supprimer cet utilisateur ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-icon btn-icon-red" title="Supprimer">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="empty-state">
                        Aucun utilisateur associé. Créez-en un pour partager l'accès à vos sociétés.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
