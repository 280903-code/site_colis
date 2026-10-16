@extends('layouts.app')

@section('title', 'Mon profil | KONEXA')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('content')
<div class="dashboard-container">
  <div class="dashboard-header">
    <h1>Mon profil</h1>
    <a href="{{ route('agency.dashboard') }}" class="btn btn-sm">← Retour</a>
  </div>

  <section class="agency-settings-card">
    <div class="agency-settings-intro">
      <span class="agency-settings-eyebrow">Informations publiques</span>
      <h2>Modifier mon profil</h2>
      <p>Ces informations aident les clients à découvrir votre agence et à vous contacter.</p>
    </div>
    <form method="POST" action="{{ route('agency.profile.update') }}" class="agency-settings-form">
      @csrf

      <div class="agency-settings-fields">
      <div class="agency-settings-field">
        <label for="name">Nom de l'agence</label>
        <input type="text" id="name" name="name" value="{{ $agency->name }}" required>
        @error('name') <div class="error">{{ $message }}</div> @enderror
      </div>

      <div class="agency-settings-field">
        <label for="description">Description</label>
        <textarea id="description" name="description" rows="3" required>{{ $agency->description }}</textarea>
        @error('description') <div class="error">{{ $message }}</div> @enderror
      </div>

      <div class="agency-settings-field">
        <label for="address">Adresse</label>
        <input type="text" id="address" name="address" value="{{ $agency->address }}" required>
        @error('address') <div class="error">{{ $message }}</div> @enderror
      </div>

      <div class="agency-settings-field">
        <label for="opening_hours">Horaires d'ouverture</label>
        <input type="text" id="opening_hours" name="opening_hours" value="{{ $agency->opening_hours }}" required>
        @error('opening_hours') <div class="error">{{ $message }}</div> @enderror
      </div>

      <div class="agency-settings-field">
        <label for="whatsapp">Numéro WhatsApp</label>
        <input type="text" id="whatsapp" name="whatsapp" value="{{ $agency->whatsapp }}" required>
        @error('whatsapp') <div class="error">{{ $message }}</div> @enderror
      </div>
      </div>

      <div class="agency-settings-actions">
        <button type="submit" class="btn">Enregistrer le profil</button>
      </div>
    </form>
  </section>
</div>
@endsection
