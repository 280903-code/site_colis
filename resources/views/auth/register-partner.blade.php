@extends('layouts.app')

@section('title', 'Devenir partenaire | AB-Flash')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endpush

@section('content')
<div class="auth-container">
  <div class="auth-card">
    <h1>Devenir partenaire</h1>
    <p>Inscrivez votre agence sur AB-Flash pour être visible par les clients.</p>

    <form method="POST" action="{{ route('partner.register') }}" class="auth-form">
      @csrf

      <div>
        <label for="contact_name">Nom du responsable</label>
        <input type="text" id="contact_name" name="contact_name" value="{{ old('contact_name') }}" required autofocus>
        @error('contact_name') <div class="error">{{ $message }}</div> @enderror
      </div>

      <div>
        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="{{ old('email') }}" required>
        @error('email') <div class="error">{{ $message }}</div> @enderror
      </div>

      <div>
        <label for="password">Mot de passe (min. 8 caractères)</label>
        <input type="password" id="password" name="password" required>
        @error('password') <div class="error">{{ $message }}</div> @enderror
      </div>

      <div>
        <label for="password_confirmation">Confirmer le mot de passe</label>
        <input type="password" id="password_confirmation" name="password_confirmation" required>
      </div>

      <div>
        <label for="agency_name">Nom de l'agence</label>
        <input type="text" id="agency_name" name="agency_name" value="{{ old('agency_name') }}" required>
        @error('agency_name') <div class="error">{{ $message }}</div> @enderror
      </div>

      <div>
        <label for="address">Adresse</label>
        <input type="text" id="address" name="address" value="{{ old('address') }}" required>
        @error('address') <div class="error">{{ $message }}</div> @enderror
      </div>

      <div>
        <label for="opening_hours">Horaires d'ouverture</label>
        <input type="text" id="opening_hours" name="opening_hours" value="{{ old('opening_hours') }}" placeholder="Ex: Lun–Sam, 9h–19h" required>
        @error('opening_hours') <div class="error">{{ $message }}</div> @enderror
      </div>

      <div>
        <label for="whatsapp">Numéro WhatsApp (chiffres uniquement)</label>
        <input type="text" id="whatsapp" name="whatsapp" value="{{ old('whatsapp') }}" placeholder="Ex: 221770000001" required>
        @error('whatsapp') <div class="error">{{ $message }}</div> @enderror
      </div>

      <div>
        <label>Trajets desservis</label>
        <div style="display: grid; gap: 8px;">
          @foreach([['SN', 'KM'], ['KM', 'SN'], ['SN', 'FR'], ['FR', 'SN'], ['FR', 'KM'], ['KM', 'FR']] as $route)
            @php
              $routeData = ['from' => $route[0], 'to' => $route[1]];
              $oldRoutes = old('routes', []);
              $isRouteSelected = collect($oldRoutes)->contains(fn ($oldRoute) => is_array($oldRoute)
                ? $oldRoute === $routeData
                : $oldRoute === json_encode($routeData));
            @endphp
            <label style="display: flex; align-items: center; gap: 8px; font-weight: normal;">
              <input type="checkbox" name="routes[]" value="{{ json_encode($routeData) }}" {{ $isRouteSelected ? 'checked' : '' }}>
              {{ $route[0] === 'SN' ? 'Sénégal' : ($route[0] === 'KM' ? 'Comores' : 'France') }} → {{ $route[1] === 'SN' ? 'Sénégal' : ($route[1] === 'KM' ? 'Comores' : 'France') }}
            </label>
          @endforeach
        </div>
        @error('routes') <div class="error">{{ $message }}</div> @enderror
      </div>

      <!-- Honeypot -->
      <input type="text" name="website" style="display: none;" tabindex="-1" autocomplete="off">

      <button type="submit" class="btn">Envoyer la demande</button>
    </form>

    <div class="auth-links">
      <a href="{{ route('login') }}">Déjà inscrit ? Connexion</a>
    </div>
  </div>
</div>
@endsection
