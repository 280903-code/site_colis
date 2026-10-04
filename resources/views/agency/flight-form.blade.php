@extends('layouts.app')

@section('title', isset($flight) ? 'Modifier vol | AB-Flash' : 'Nouveau vol | AB-Flash')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('content')
<div class="dashboard-container">
  <div class="dashboard-header">
    <h1>{{ isset($flight) ? 'Modifier vol' : 'Nouveau vol' }}</h1>
    <a href="{{ route('agency.flights') }}" class="btn btn-sm">← Retour</a>
  </div>

  <div class="auth-card" style="max-width: 600px;">
    <form method="POST" action="{{ isset($flight) ? route('agency.flights.update', $flight) : route('agency.flights.store') }}" class="auth-form">
      @csrf
      @if(isset($flight))
        @method('PUT')
      @endif

      <div>
        <label for="from_country">Pays de départ</label>
        <select id="from_country" name="from_country" required>
          <option value="SN" {{ isset($flight) && $flight->from_country === 'SN' ? 'selected' : '' }}>Sénégal</option>
          <option value="KM" {{ isset($flight) && $flight->from_country === 'KM' ? 'selected' : '' }}>Comores</option>
          <option value="FR" {{ isset($flight) && $flight->from_country === 'FR' ? 'selected' : '' }}>France</option>
        </select>
        @error('from_country') <div class="error">{{ $message }}</div> @enderror
      </div>

      <div>
        <label for="to_country">Pays de destination</label>
        <select id="to_country" name="to_country" required>
          <option value="SN" {{ isset($flight) && $flight->to_country === 'SN' ? 'selected' : '' }}>Sénégal</option>
          <option value="KM" {{ isset($flight) && $flight->to_country === 'KM' ? 'selected' : '' }}>Comores</option>
          <option value="FR" {{ isset($flight) && $flight->to_country === 'FR' ? 'selected' : '' }}>France</option>
        </select>
        @error('to_country') <div class="error">{{ $message }}</div> @enderror
      </div>

      <div>
        <label for="flight_date">Date du vol</label>
        <input type="date" id="flight_date" name="flight_date" value="{{ isset($flight) ? $flight->flight_date->format('Y-m-d') : '' }}" required>
        @error('flight_date') <div class="error">{{ $message }}</div> @enderror
      </div>

      <div>
        <label for="airline">Compagnie aérienne</label>
        <input type="text" id="airline" name="airline" value="{{ isset($flight) ? $flight->airline : '' }}" required>
        @error('airline') <div class="error">{{ $message }}</div> @enderror
      </div>

      <div>
        <label for="total_kg">Kilos totaux</label>
        <input type="number" id="total_kg" name="total_kg" value="{{ isset($flight) ? $flight->total_kg : '' }}" min="1" required>
        @error('total_kg') <div class="error">{{ $message }}</div> @enderror
      </div>

      <div>
        <label for="remaining_kg">Kilos restants</label>
        <input type="number" id="remaining_kg" name="remaining_kg" value="{{ isset($flight) ? $flight->remaining_kg : '' }}" min="0" required>
        @error('remaining_kg') <div class="error">{{ $message }}</div> @enderror
      </div>

      <div>
        <label for="price_per_kg">Prix par kg</label>
        <input type="number" id="price_per_kg" name="price_per_kg" value="{{ isset($flight) ? $flight->price_per_kg : '' }}" step="0.01" min="0" required>
        @error('price_per_kg') <div class="error">{{ $message }}</div> @enderror
      </div>

      <div>
        <label for="currency">Devise</label>
        <select id="currency" name="currency" required>
          <option value="FCFA" {{ isset($flight) && $flight->currency === 'FCFA' ? 'selected' : '' }}>FCFA</option>
          <option value="EUR" {{ isset($flight) && $flight->currency === 'EUR' ? 'selected' : '' }}>EUR</option>
        </select>
        @error('currency') <div class="error">{{ $message }}</div> @enderror
      </div>

      @if(isset($flight))
      <div>
        <label for="status">Statut</label>
        <select id="status" name="status" required>
          <option value="open" {{ $flight->status === 'open' ? 'selected' : '' }}>Ouvert</option>
          <option value="full" {{ $flight->status === 'full' ? 'selected' : '' }}>Complet</option>
          <option value="cancelled" {{ $flight->status === 'cancelled' ? 'selected' : '' }}>Annulé</option>
        </select>
        @error('status') <div class="error">{{ $message }}</div> @enderror
      </div>
      @endif

      <button type="submit" class="btn">{{ isset($flight) ? 'Mettre à jour' : 'Créer' }}</button>
    </form>
  </div>
</div>
@endsection
