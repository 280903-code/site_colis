@extends('layouts.app')

@section('title', 'Mes trajets | KONEXA')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('content')
<div class="dashboard-container">
  <div class="dashboard-header">
    <h1>Mes trajets</h1>
    <a href="{{ route('agency.dashboard') }}" class="btn btn-sm">← Retour</a>
  </div>

  <section class="agency-settings-card agency-routes-card">
    <div class="agency-settings-intro">
      <span class="agency-settings-eyebrow">Destinations de l'agence</span>
      <h2>Modifier les trajets desservis</h2>
      <p>Ajoutez chaque itinéraire que votre agence propose. Vous pouvez saisir le nom de n'importe quel pays.</p>
    </div>
    <form method="POST" action="{{ route('agency.routes.update') }}" class="agency-settings-form">
      @csrf

      @php
        $routeValues = $agency->routes
          ->map(fn($route) => ['from' => $route->from_country, 'to' => $route->to_country])
          ->all();
      @endphp
      <x-route-editor :routes="$routeValues" />

      <div class="agency-settings-actions">
        <button type="submit" class="btn">Enregistrer les trajets</button>
      </div>
    </form>
  </section>
</div>
@endsection
