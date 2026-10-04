@extends('layouts.app')

@section('title', 'Connexion | AB-Flash')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endpush

@section('content')
<div class="auth-container">
  <div class="auth-card">
    <h1>Connexion</h1>
    @if (session('status'))
      <p style="color: var(--v);">{{ session('status') }}</p>
    @else
      <p>Connectez-vous à votre espace.</p>
    @endif

    <form method="POST" action="{{ route('login') }}" class="auth-form">
      @csrf

      <div>
        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
        @error('email') <div class="error">{{ $message }}</div> @enderror
      </div>

      <div>
        <label for="password">Mot de passe</label>
        <input type="password" id="password" name="password" required autocomplete="current-password">
        @error('password') <div class="error">{{ $message }}</div> @enderror
      </div>

      <div>
        <label style="display: flex; align-items: center; gap: 8px; font-weight: normal;">
          <input type="checkbox" name="remember" id="remember">
          <span>Se souvenir de moi</span>
        </label>
      </div>

      @if (Route::has('password.request'))
        <div style="text-align: right;">
          <a href="{{ route('password.request') }}" style="color: var(--v); font-size: 0.85rem; text-decoration: none;">Mot de passe oublié ?</a>
        </div>
      @endif

      <button type="submit" class="btn">Se connecter</button>
    </form>

    <div class="auth-links">
      <a href="{{ route('partner.register.form') }}">Devenir partenaire</a>
    </div>
  </div>
</div>
@endsection
