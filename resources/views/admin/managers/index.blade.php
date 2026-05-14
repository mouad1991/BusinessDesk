@extends('layouts.app')
@section('title', 'Gérants')
@section('page-title', 'Gérants')

@section('page-actions')
    <a href="{{ route('admin.managers.create') }}" class="btn-primary">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Nouveau gérant
    </a>
@endsection

@section('content')
<div class="card">
    <div class="card-body p-0">
        <table class="table">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>Sociétés</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($managers as $manager)
                <tr>
                    <td>
                        <div class="user-cell">
                            <div class="avatar-sm">{{ strtoupper(substr($manager->firstname, 0, 1)) }}</div>
                            <span>{{ $manager->full_name }}</span>
                        </div>
                    </td>
                    <td>{{ $manager->email }}</td>
                    <td>{{ $manager->mobile ?? '—' }}</td>
                    <td><span class="badge badge-blue">{{ $manager->companies_count }}</span></td>
                    <td>
                        <div class="action-group">
                            <a href="{{ route('admin.managers.edit', $manager) }}" class="btn-icon" title="Modifier">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </a>
                            <form method="POST" action="{{ route('admin.managers.impersonate', $manager) }}" style="display:inline">
                                @csrf
                                <button type="submit" class="btn-icon btn-icon-green" title="Naviguer comme ce gérant">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.managers.destroy', $manager) }}" style="display:inline"
                                  onsubmit="return confirm('Supprimer ce gérant ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-icon btn-icon-red" title="Supprimer">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="empty-state">Aucun gérant.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($managers->hasPages())
    <div class="card-footer">{{ $managers->links() }}</div>
    @endif
</div>
@endsection
