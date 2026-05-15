<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'BusinessDesk') — BusinessDesk</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')
    <script>
        // Apply saved theme before render to avoid flash
        (function(){var t=localStorage.getItem('bd-theme');if(t==='dark')document.documentElement.setAttribute('data-theme','dark');})();
    </script>
</head>
<body>

@if(session()->has('impersonate'))
<div class="impersonate-bar">
    <span>🔑 Vous naviguez en tant que <strong>{{ auth()->user()->full_name }}</strong></span>
    <form method="POST" action="{{ route('logout') }}" style="display:inline">
        @csrf
        <button type="submit" class="btn-stop-impersonate">Quitter l'impersonation</button>
    </form>
</div>
@endif

<div class="layout">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-brand">
            <img src="{{ asset('images/logo.svg') }}" class="brand-logo" alt="BD">
            <span class="brand-name">BusinessDesk</span>
        </div>

        <nav class="sidebar-nav">
            @if(auth()->user()->isAdmin() && !session()->has('impersonate'))
                <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
                    Tableau de bord
                </a>
                <a href="{{ route('admin.managers.index') }}" class="nav-item {{ request()->routeIs('admin.managers.*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    Gérants
                </a>
            @else
                @php
                    $userCompanies = auth()->user()->getWorkableCompanies();
                    $currentCo = app()->bound('currentCompany') ? app('currentCompany') : null;
                @endphp

                {{-- Company switcher --}}
                @if($currentCo)
                <div class="company-switcher">
                    <div class="company-switcher-current">
                        <div class="company-switcher-logo">
                            @if($currentCo->logo)
                                <img src="{{ asset('storage/' . $currentCo->logo) }}" alt="{{ $currentCo->name }}">
                            @else
                                <div class="company-switcher-initial">{{ strtoupper(substr($currentCo->name, 0, 2)) }}</div>
                            @endif
                        </div>
                        <div class="company-switcher-info">
                            <div class="company-switcher-label">Société active</div>
                            <div class="company-switcher-name">{{ $currentCo->name }}</div>
                        </div>
                        @if($userCompanies->count() > 1)
                            <button type="button" class="company-switcher-toggle" onclick="document.getElementById('company-switcher-menu').classList.toggle('open')" title="Changer de société">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                            </button>
                        @endif
                    </div>
                    @if($userCompanies->count() > 1)
                    <div id="company-switcher-menu" class="company-switcher-menu">
                        @foreach($userCompanies as $co)
                            @if($co->id !== $currentCo->id)
                                <form method="POST" action="{{ route('manager.company-switch', $co) }}">
                                    @csrf
                                    <button type="submit" class="company-switcher-item">
                                        @if($co->logo)
                                            <img src="{{ asset('storage/' . $co->logo) }}" alt="{{ $co->name }}">
                                        @else
                                            <div class="company-switcher-initial">{{ strtoupper(substr($co->name, 0, 2)) }}</div>
                                        @endif
                                        <span>{{ $co->name }}</span>
                                    </button>
                                </form>
                            @endif
                        @endforeach
                        <a href="{{ route('manager.company-select') }}" class="company-switcher-item company-switcher-all">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
                            <span>Toutes les sociétés</span>
                        </a>
                    </div>
                    @endif
                </div>
                @endif

                {{-- Global navigation --}}
                <a href="{{ route('manager.dashboard') }}" class="nav-item {{ request()->routeIs('manager.dashboard') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
                    Tableau de bord
                </a>
                <a href="{{ route('manager.clients.index') }}" class="nav-item {{ request()->routeIs('manager.clients.*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                    Clients
                </a>
                <a href="{{ route('manager.quotes.index') }}" class="nav-item {{ request()->routeIs('manager.quotes.*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    Devis
                </a>
                <a href="{{ route('manager.invoices.index') }}" class="nav-item {{ request()->routeIs('manager.invoices.*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                    Factures
                </a>
                <a href="{{ route('manager.delivery-notes.index') }}" class="nav-item {{ request()->routeIs('manager.delivery-notes.*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 17H3a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11a2 2 0 0 1 2 2v3"/><rect x="9" y="11" width="14" height="10" rx="1"/><polyline points="12 17 14 19 18 15"/></svg>
                    Bons de livraison
                </a>
                <a href="{{ route('manager.markets.index') }}" class="nav-item {{ request()->routeIs('manager.markets.*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/><line x1="9" y1="12" x2="15" y2="12"/><line x1="9" y1="16" x2="13" y2="16"/></svg>
                    Marchés
                </a>
                <a href="{{ route('manager.purchase-orders.index') }}" class="nav-item {{ request()->routeIs('manager.purchase-orders.*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                    Bons de commande
                </a>

                <div class="nav-divider"></div>

                @if(!auth()->user()->isCollaborator())
                <a href="{{ route('manager.companies.index') }}" class="nav-item nav-item-secondary {{ request()->routeIs('manager.companies.*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    Mes sociétés
                </a>
                @if(auth()->user()->isManager())
                <a href="{{ route('manager.collaborators.index') }}" class="nav-item nav-item-secondary {{ request()->routeIs('manager.collaborators.*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    Utilisateurs
                </a>
                @endif
                @endif
            @endif
        </nav>

        <div class="sidebar-footer">
            <a href="{{ route('profile.edit') }}" class="user-info user-info-link {{ request()->routeIs('profile.*') ? 'active' : '' }}" title="Mon profil">
                <div class="user-avatar">{{ strtoupper(substr(auth()->user()->firstname, 0, 1)) }}</div>
                <div class="user-details">
                    <div class="user-name">{{ auth()->user()->full_name }}</div>
                    <div class="user-role">
                        @if(auth()->user()->isAdmin()) Administrateur
                        @elseif(auth()->user()->isCollaborator()) Collaborateur
                        @else Gérant
                        @endif
                    </div>
                </div>
                <svg class="user-info-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
            <button id="theme-toggle" class="theme-toggle" title="Changer le thème">
                <svg id="theme-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                </svg>
            </button>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn-logout" title="Déconnexion">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main content -->
    <main class="main-content">
        <header class="page-header">
            <div class="page-title">
                <h1>@yield('page-title')</h1>
                @hasSection('breadcrumb')
                <nav class="breadcrumb">@yield('breadcrumb')</nav>
                @endif
            </div>
            <div class="page-actions">
                @yield('page-actions')
            </div>
        </header>

        <div class="page-body">
            @if(session('success'))
                <div class="alert alert-success">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-error">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                    {{ session('error') }}
                </div>
            @endif
            @if(session('info'))
                <div class="alert alert-info">{{ session('info') }}</div>
            @endif

            @yield('content')
        </div>
    </main>
</div>

<script src="{{ asset('js/app.js') }}"></script>
@stack('scripts')
</body>
</html>
