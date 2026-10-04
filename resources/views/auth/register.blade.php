@extends('layouts.app')

@section('title', 'Inscription | AB-Flash')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endpush

@section('content')
<div class="auth-container">
  <div class="auth-card">
    <h1>Inscription</h1>
    <p>Créez un compte utilisateur.</p>

    <form method="POST" action="{{ route('register') }}" class="auth-form">
      @csrf

      <div>
        <label for="name">Nom</label>
        <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus autocomplete="name">
        @error('name') <div class="error">{{ $message }}</div> @enderror
      </div>

      <div>
        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="{{ old('email') }}" required autocomplete="username">
        @error('email') <div class="error">{{ $message }}</div> @enderror
      </div>

      <div>
        <label for="password">Mot de passe (min. 8 caractères)</label>
        <input type="password" id="password" name="password" required autocomplete="new-password">
        @error('password') <div class="error">{{ $message }}</div> @enderror
      </div>

      <div>
        <label for="password_confirmation">Confirmer le mot de passe</label>
        <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password">
      </div>

      <button type="submit" class="btn">S'inscrire</button>
    </form>

    <div class="auth-links">
      <a href="{{ route('login') }}">Déjà inscrit ? Connexion</a>
    </div>
  </div>
</div>
@endsection
