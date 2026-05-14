<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — BusinessDesk</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="auth-body">
<div class="auth-container">
    <div class="auth-card">
        <div class="auth-logo">
            <div class="logo-mark">BD</div>
            <h1>BusinessDesk</h1>
            <p>Gestion commerciale pour SARL marocaines</p>
        </div>

        <form method="POST" action="{{ route('login.post') }}" class="auth-form">
            @csrf

            <div class="form-group">
                <label for="email">Adresse email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}"
                       placeholder="votre@email.com" required autofocus
                       class="{{ $errors->has('email') ? 'is-invalid' : '' }}">
                @error('email')
                    <span class="error-msg">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Mot de passe</label>
                <div class="input-password">
                    <input type="password" id="password" name="password"
                           placeholder="••••••••" required
                           class="{{ $errors->has('password') ? 'is-invalid' : '' }}">
                    <button type="button" class="toggle-password" onclick="togglePassword()">
                        <svg id="eye-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </div>
                @error('password')
                    <span class="error-msg">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-check">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember">Se souvenir de moi</label>
            </div>

            <button type="submit" class="btn-primary btn-full">Se connecter</button>
        </form>

        <div class="auth-back">
            <a href="{{ route('landing') }}">← Retour à l'accueil</a>
        </div>
    </div>
</div>
<style>
    .auth-back { margin-top: 18px; text-align: center; }
    .auth-back a { color: #64748b; font-size: 0.85rem; text-decoration: none; }
    .auth-back a:hover { color: #0f172a; text-decoration: underline; }
</style>
<script>
function togglePassword() {
    const input = document.getElementById('password');
    input.type = input.type === 'password' ? 'text' : 'password';
}
</script>
</body>
</html>
