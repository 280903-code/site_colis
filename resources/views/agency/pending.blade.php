@extends('layouts.app')

@section('title', 'Espace agence | AB-Flash')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('content')
<div class="dashboard-container">
  <div class="auth-card" style="text-align: center; padding: 48px;">
    <h1 style="font-size: 2rem; margin-bottom: 16px;">⏳ En attente d'approbation</h1>
    <p style="font-size: 1.1rem; color: var(--g); margin-bottom: 24px;">
      Votre demande d'inscription est en cours de traitement par l'administrateur.
      Vous recevrez un email dès que votre agence sera approuvée.
    </p>
    <p style="font-size: 0.9rem; color: var(--g);">
      Si vous avez des questions, n'hésitez pas à nous contacter par WhatsApp.
    </p>
  </div>
</div>
@endsection
