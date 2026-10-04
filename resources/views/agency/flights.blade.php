@extends('layouts.app')

@section('title', 'Mes vols | AB-Flash')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('content')
<div class="dashboard-container">
  <div class="dashboard-header">
    <h1>Mes vols</h1>
    <div style="display: flex; gap: 12px;">
      <a href="{{ route('agency.dashboard') }}" class="btn btn-sm">← Retour</a>
      <a href="{{ route('agency.flights.create') }}" class="btn btn-sm">+ Nouveau vol</a>
    </div>
  </div>

  <div class="table-container">
    <table class="data-table">
      <thead>
        <tr>
          <th>Trajet</th>
          <th>Date</th>
          <th>Compagnie</th>
          <th>Kilos</th>
          <th>Prix</th>
          <th>Statut</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($flights as $flight)
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
          <td>
            <div class="actions">
              <a href="{{ route('agency.flights.edit', $flight) }}" class="btn btn-sm">Modifier</a>
              <form method="POST" action="{{ route('agency.flights.duplicate', $flight) }}" style="display: inline;">
                @csrf
                <button type="submit" class="btn o btn-sm">Dupliquer</button>
              </form>
              <form method="POST" action="{{ route('agency.flights.delete', $flight) }}" style="display: inline;" onsubmit="return confirm('Supprimer ce vol ?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn o btn-sm" style="color: var(--r); border-color: var(--r);">Supprimer</button>
              </form>
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
    {{ $flights->links() }}
  </div>
</div>
@endsection
