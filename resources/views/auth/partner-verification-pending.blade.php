@extends('layouts.app')

@section('title', 'Vérification de votre adresse | KONEXA')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endpush

@section('content')
<main class="auth-container">
  <div class="auth-card">
    <h1>Vérifiez votre boîte e-mail</h1>
    <p>Votre compte partenaire n’est pas encore créé. Ouvrez le message de KONEXA et cliquez sur le lien de confirmation pour valider votre adresse. Vous pourrez ensuite vous connecter.</p>

    @if (session('status'))
      <p role="status" style="color: var(--v);">{{ session('status') }}</p>
    @endif

    @if (config('mail.default') === 'log')
      <p>Le serveur e-mail est en mode journalisation : les messages sont enregistrés dans les journaux de l’application, mais ne sont pas envoyés. Configurez un transport SMTP pour recevoir ce lien dans votre boîte e-mail.</p>
    @endif

    <form method="POST" action="{{ route('partner.verification.resend') }}" class="auth-form">
      @csrf
      <div>
        <label for="email">Adresse e-mail utilisée pour la demande</label>
        <input type="email" id="email" name="email" value="{{ old('email') }}" required autocomplete="email">
        @error('email') <div class="error">{{ $message }}</div> @enderror
      </div>
      <button type="submit" class="btn">Renvoyer le lien de vérification</button>
    </form>

    <div class="auth-links">
      <a href="{{ route('login') }}">Retour à la connexion</a>
    </div>
  </div>
</main>
@endsection
