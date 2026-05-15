<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sélectionner une société — BusinessDesk</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="auth-body">
<div class="company-select-wrap">
    <div class="company-select-header">
        <img src="{{ asset('images/logo.svg') }}" class="company-select-logo" alt="BusinessDesk" onerror="this.style.display='none'">
        <h1>Bienvenue, {{ auth()->user()->firstname }}</h1>
        <p class="text-muted">Sélectionnez la société sur laquelle vous souhaitez travailler.</p>
    </div>

    @if(session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif
    @if(session('info'))
        <div class="alert alert-info">{{ session('info') }}</div>
    @endif

    @if($companies->isEmpty())
        <div class="empty-card">
            <div class="empty-icon">🏢</div>
            <h3>Aucune société enregistrée</h3>
            <p>Contactez votre administrateur pour créer une société.</p>
        </div>
    @else
        <div class="company-select-grid">
            @foreach($companies as $company)
                <form method="POST" action="{{ route('manager.company-switch', $company) }}" class="company-select-card-form">
                    @csrf
                    <button type="submit" class="company-select-card">
                        <div class="company-select-logo-wrap">
                            @if($company->logo)
                                <img src="{{ asset('storage/' . $company->logo) }}" alt="{{ $company->name }}">
                            @else
                                <div class="company-select-initial">{{ strtoupper(substr($company->name, 0, 2)) }}</div>
                            @endif
                        </div>
                        <div class="company-select-info">
                            <h3>{{ $company->name }}</h3>
                            <span class="text-muted">{{ $company->address }}</span>
                        </div>
                        <div class="company-select-arrow">→</div>
                    </button>
                </form>
            @endforeach
        </div>

        <div class="company-select-footer">
            @if(session('impersonate'))
            <a href="{{ route('manager.companies.create') }}" class="btn-secondary">+ Nouvelle société</a>
            @else
            <span></span>
            @endif
            <form method="POST" action="{{ route('logout') }}" style="display:inline">
                @csrf
                <button type="submit" class="btn-link">Déconnexion</button>
            </form>
        </div>
    @endif
</div>
</body>
</html>
