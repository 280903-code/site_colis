@extends('layouts.app')

@section('title', 'Espace agence | AB-Flash')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('content')
<div class="dashboard-container">
  <div class="dashboard-header">
    <h1>Mon espace</h1>
    <div style="display: flex; gap: 12px;">
      <a href="{{ route('agency.profile') }}" class="btn btn-sm">Mon profil</a>
      <a href="{{ route('agency.flights') }}" class="btn btn-sm">Mes vols</a>
      <a href="{{ route('agency.routes') }}" class="btn btn-sm">Mes trajets</a>
    </div>
  </div>

  <div class="stats-grid">
    <div class="stat-card">
      <div class="number">{{ $agency->name }}</div>
      <div class="label">Statut: {{ $agency->status === 'approved' ? 'Approuvée' : ($agency->status === 'suspended' ? 'Suspendue' : $agency->status) }}</div>
    </div>
    <div class="stat-card">
      <div class="number">{{ $stats['upcoming_flights'] }}</div>
      <div class="label">Vols à venir</div>
    </div>
    <div class="stat-card">
      <div class="number">{{ $stats['total_kg_available'] }}</div>
      <div class="label">Kilos disponibles</div>
    </div>
  </div>

  <div class="table-container">
    <div class="table-header">
      <h2>Prochains vols</h2>
      <a href="{{ route('agency.flights') }}" class="btn btn-sm">Voir tout</a>
    </div>
    <table class="data-table">
      <thead>
        <tr>
          <th>Trajet</th>
          <th>Date</th>
          <th>Compagnie</th>
          <th>Kilos</th>
          <th>Prix</th>
          <th>Statut</th>
        </tr>
      </thead>
      <tbody>
        @foreach($upcomingFlights as $flight)
        <tr>
          <td>{{ $flight->from_country }} → {{ $flight->to_country }}</td>
          <td>{{ $flight->flight_date->format('d/m/Y') }}</td>
          <td>{{ $flight->airline }}</td>
          <td>{{ $flight->remaining_kg }} / {{ $flight->total_kg }} kg</td>
          <td>{{ $flight->price_per_kg }} {{ $flight->currency }}/kg</td>
          <td>
            <span class="status-badge {{ $flight->status === 'open' ? 'status-approved' : ($flight->status === 'full' ? 'status-pending' : 'status-rejected') }}">
              {{ $flight->status === 'open' ? 'Ouvert' : ($flight->status === 'full' ? 'Complet' : 'Annulé') }}
            </span>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection
