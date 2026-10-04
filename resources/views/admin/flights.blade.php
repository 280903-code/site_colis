@extends('layouts.app')

@section('title', 'Supervision des vols | AB-Flash')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('content')
<div class="dashboard-container">
  <div class="dashboard-header">
    <h1>Supervision des vols</h1>
  </div>

  <div class="table-container">
    <div class="table-header">
      <h2>Vols à venir ({{ $flights->total() }})</h2>
      <div class="filters">
        <form method="GET" style="display: flex; gap: 12px; align-items: center;">
          <input type="text" name="search" value="{{ $search }}" placeholder="Rechercher..." style="padding: 8px 12px; border: 1.5px solid var(--l); border-radius: 8px;">
          <button type="submit" class="btn btn-sm">Rechercher</button>
        </form>
      </div>
    </div>
    <table class="data-table">
      <thead>
        <tr>
          <th>Agence</th>
          <th>Trajet</th>
          <th>Date</th>
          <th>Compagnie</th>
          <th>Kilos</th>
          <th>Prix</th>
          <th>Statut</th>
        </tr>
      </thead>
      <tbody>
        @foreach($flights as $flight)
        <tr>
          <td>{{ $flight->agency->name }}</td>
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
    {{ $flights->links() }}
  </div>
</div>
@endsection
