@extends('layouts.app')

@section('title', isset($flight) ? 'Modifier vol | KONEXA' : 'Nouveau vol | KONEXA')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('content')
@php
  $fromCountry = old('from_country', $flight->from_country ?? 'Sénégal');
  $toCountry = old('to_country', $flight->to_country ?? 'Comores');
  $fromCountry = is_string($fromCountry) ? \App\Support\Country::label($fromCountry) : 'Sénégal';
  $toCountry = is_string($toCountry) ? \App\Support\Country::label($toCountry) : 'Comores';
@endphp
<div class="dashboard-container">
  <div class="dashboard-header">
    <h1>{{ isset($flight) ? 'Modifier vol' : 'Nouveau vol' }}</h1>
    <a href="{{ route('agency.flights') }}" class="btn btn-sm">← Retour</a>
  </div>

  <section class="table-container flight-form-card">
    <div class="flight-form-intro">
      <span class="flight-form-eyebrow">{{ isset($flight) ? 'Mise à jour' : 'Gestion des vols' }}</span>
      <h2>{{ isset($flight) ? 'Informations du vol' : 'Ajouter un vol' }}</h2>
      <p>Renseignez le trajet, la capacité et le tarif pour informer vos clients.</p>
    </div>
    <form method="POST" action="{{ isset($flight) ? route('agency.flights.update', $flight) : route('agency.flights.store') }}" class="auth-form flight-form">
      @csrf
      @if(isset($flight))
        @method('PUT')
      @endif

      <div>
        <label for="from_country">Pays de départ</label>
        <input type="text" id="from_country" name="from_country" value="{{ $fromCountry }}" list="flight-country-options" placeholder="Ex. Sénégal" maxlength="100" required>
        @error('from_country') <div class="error">{{ $message }}</div> @enderror
      </div>

      <div>
        <label for="to_country">Pays de destination</label>
        <input type="text" id="to_country" name="to_country" value="{{ $toCountry }}" list="flight-country-options" placeholder="Ex. Chine" maxlength="100" required>
        @error('to_country') <div class="error">{{ $message }}</div> @enderror
      </div>
      <datalist id="flight-country-options">
        @foreach(\App\Support\Country::suggestions() as $country)
          <option value="{{ $country }}"></option>
        @endforeach
      </datalist>

      <div>
        <label for="flight_date">Date du vol</label>
        <input type="date" id="flight_date" name="flight_date" value="{{ old('flight_date', isset($flight) ? $flight->flight_date->format('Y-m-d') : '') }}" required>
        @error('flight_date') <div class="error">{{ $message }}</div> @enderror
      </div>

      <div>
        <label for="airline">Compagnie aérienne</label>
        <input type="text" id="airline" name="airline" value="{{ old('airline', $flight->airline ?? '') }}" required>
        @error('airline') <div class="error">{{ $message }}</div> @enderror
      </div>

      <div>
        <label for="total_kg">Kilos totaux</label>
        <input type="number" id="total_kg" name="total_kg" value="{{ old('total_kg', $flight->total_kg ?? '') }}" min="1" required>
        @error('total_kg') <div class="error">{{ $message }}</div> @enderror
      </div>

      <div>
        <label for="remaining_kg">Kilos restants</label>
        <input type="number" id="remaining_kg" name="remaining_kg" value="{{ old('remaining_kg', $flight->remaining_kg ?? '') }}" min="0" required>
        @error('remaining_kg') <div class="error">{{ $message }}</div> @enderror
      </div>

      <div>
        <label for="price_per_kg">Prix par kg</label>
        <input type="number" id="price_per_kg" name="price_per_kg" value="{{ old('price_per_kg', $flight->price_per_kg ?? '') }}" step="0.01" min="0" required>
        @error('price_per_kg') <div class="error">{{ $message }}</div> @enderror
      </div>

      <div>
        <label for="currency">Devise</label>
        <input type="text" id="currency" name="currency" value="{{ old('currency', $flight->currency ?? 'FCFA') }}" list="currency-options" maxlength="4" placeholder="Ex. USD" required>
        <datalist id="currency-options">
          @foreach(['FCFA', 'EUR', 'USD', 'CNY', 'GBP', 'CAD', 'XOF', 'XAF', 'INR', 'JPY', 'AUD', 'MAD', 'AED'] as $currency)
            <option value="{{ $currency }}"></option>
          @endforeach
        </datalist>
        @error('currency') <div class="error">{{ $message }}</div> @enderror
      </div>

      @if(isset($flight))
      <div>
        <label for="status">Statut</label>
        <select id="status" name="status" required>
          <option value="open" {{ old('status', $flight->status) === 'open' ? 'selected' : '' }}>Ouvert</option>
          <option value="full" {{ old('status', $flight->status) === 'full' ? 'selected' : '' }}>Complet</option>
          <option value="cancelled" {{ old('status', $flight->status) === 'cancelled' ? 'selected' : '' }}>Annulé</option>
        </select>
        @error('status') <div class="error">{{ $message }}</div> @enderror
      </div>
      @endif

      <p class="flight-form-hint">Vérifiez que les kilos restants ne dépassent pas la capacité totale du vol.</p>
      <div class="flight-form-actions">
        <a href="{{ route('agency.flights') }}" class="btn o">Annuler</a>
        <button type="submit" class="btn">{{ isset($flight) ? 'Enregistrer les modifications' : 'Créer le vol' }}</button>
      </div>
    </form>
  </section>
</div>
@endsection
