@extends('layouts.app')

@section('title', 'Mon profil | AB-Flash')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('content')
<div class="dashboard-container">
  <div class="dashboard-header">
    <h1>Mon profil</h1>
    <a href="{{ route('agency.dashboard') }}" class="btn btn-sm">← Retour</a>
  </div>

  <div class="auth-card" style="max-width: 600px;">
    <h1>Modifier mon profil</h1>
    <form method="POST" action="{{ route('agency.profile.update') }}" class="auth-form">
      @csrf

      <div>
        <label for="name">Nom de l'agence</label>
        <input type="text" id="name" name="name" value="{{ $agency->name }}" required>
        @error('name') <div class="error">{{ $message }}</div> @enderror
      </div>

      <div>
        <label for="description">Description</label>
        <textarea id="description" name="description" rows="3" required>{{ $agency->description }}</textarea>
        @error('description') <div class="error">{{ $message }}</div> @enderror
      </div>

      <div>
        <label for="address">Adresse</label>
        <input type="text" id="address" name="address" value="{{ $agency->address }}" required>
        @error('address') <div class="error">{{ $message }}</div> @enderror
      </div>

      <div>
        <label for="opening_hours">Horaires d'ouverture</label>
        <input type="text" id="opening_hours" name="opening_hours" value="{{ $agency->opening_hours }}" required>
        @error('opening_hours') <div class="error">{{ $message }}</div> @enderror
      </div>

      <div>
        <label for="whatsapp">Numéro WhatsApp (chiffres uniquement)</label>
        <input type="text" id="whatsapp" name="whatsapp" value="{{ $agency->whatsapp }}" required>
        @error('whatsapp') <div class="error">{{ $message }}</div> @enderror
      </div>

      <button type="submit" class="btn">Enregistrer</button>
    </form>
  </div>
</div>
@endsection
