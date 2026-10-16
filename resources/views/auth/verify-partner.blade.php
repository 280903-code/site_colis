@extends('layouts.app')

@section('title', 'Vérifier votre adresse e-mail | KONEXA')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endpush

@section('content')
<main class="auth-container">
  <div class="auth-card">
    <h1>Confirmer votre adresse e-mail</h1>
    <p>Confirmez votre adresse pour créer votre compte partenaire et envoyer votre demande d’agence à notre équipe.</p>

    <form method="POST" action="{{ url()->full() }}" class="auth-form">
      @csrf
      <button type="submit" class="btn">Confirmer mon adresse e-mail</button>
    </form>

    <div class="auth-links">
      <a href="{{ route('partner.register.form') }}">Retour au formulaire partenaire</a>
    </div>
  </div>
</main>
@endsection
