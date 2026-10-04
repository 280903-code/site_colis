@extends('layouts.app')

@section('title', 'Mes trajets | AB-Flash')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('content')
<div class="dashboard-container">
  <div class="dashboard-header">
    <h1>Mes trajets</h1>
    <a href="{{ route('agency.dashboard') }}" class="btn btn-sm">← Retour</a>
  </div>

  <div class="auth-card" style="max-width: 600px;">
    <h1>Modifier les trajets desservis</h1>
    <form method="POST" action="{{ route('agency.routes.update') }}" class="auth-form">
      @csrf

      <div>
        <label>Trajets desservis</label>
        <div style="display: grid; gap: 8px;">
          @foreach([['SN', 'KM'], ['KM', 'SN'], ['SN', 'FR'], ['FR', 'SN'], ['FR', 'KM'], ['KM', 'FR']] as $route)
            <label style="display: flex; align-items: center; gap: 8px; font-weight: normal;">
              <input type="checkbox" name="routes[]" value="{{ json_encode(['from' => $route[0], 'to' => $route[1]]) }}" {{ $agency->routes->contains(fn($r) => $r->from_country === $route[0] && $r->to_country === $route[1]) ? 'checked' : '' }}>
              {{ $route[0] === 'SN' ? 'Sénégal' : ($route[0] === 'KM' ? 'Comores' : 'France') }} → {{ $route[1] === 'SN' ? 'Sénégal' : ($route[1] === 'KM' ? 'Comores' : 'France') }}
            </label>
          @endforeach
        </div>
        @error('routes') <div class="error">{{ $message }}</div> @enderror
      </div>

      <button type="submit" class="btn">Enregistrer</button>
    </form>
  </div>
</div>
@endsection
