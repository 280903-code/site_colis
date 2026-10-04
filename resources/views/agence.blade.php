@extends('layouts.app')

@section('title', $agency->name . ' | AB-Flash')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/components.css') }}">
<link rel="stylesheet" href="{{ asset('css/agence.css') }}">
@endpush

@section('content')
  <main class="w" id="page">
    <a class="back" href="{{ url('/agences') }}?from={{ $from }}&to={{ $to }}">← Retour aux agences</a>
    <div class="agency">
      <aside class="side">
        <div class="top"><h1>{{ $agency->name }}</h1><p>{{ $agency->description }}</p></div>
        <div class="body">
          <div class="info"><span>📍 {{ $agency->address }}</span><span>🕒 {{ $agency->opening_hours }}</span></div>
          <div class="stats">
            <div><b>{{ $allFlights->count() }}</b><small>vols à venir</small></div>
            <div><b>{{ $totalKgAvailable }}</b><small>kg disponibles</small></div>
          </div>
          <a class="btn wa" href="https://wa.me/{{ $agency->whatsapp }}?text={{ urlencode('Bonjour ' . $agency->name . ', je voudrais des informations sur vos envois de colis.') }}" target="_blank" rel="noopener">Écrire sur WhatsApp</a>
          <a class="btn o" href="https://www.openstreetmap.org/search?query={{ urlencode(explode('·', $agency->address)[0]) }}" target="_blank" rel="noopener">Voir sur la carte</a>
        </div>
      </aside>
      <section>
        <div class="mh"><h2>Vols disponibles</h2><label class="chk"><input type="checkbox" id="hf"> Masquer les vols complets</label></div>
        <nav class="routes in" aria-label="Filtrer les vols">
          <a class="chip{{ $from === '_' && $to === '_' ? ' on' : '' }}" href="{{ route('agence.show', ['slug' => $agency->slug]) }}">Tous</a>
          @foreach($agency->routes as $route)
            <a class="chip{{ $route->from_country === $from && $route->to_country === $to ? ' on' : '' }}" href="{{ route('agence.show', ['slug' => $agency->slug, 'from' => $route->from_country, 'to' => $route->to_country]) }}">
              {{ $countries[$route->from_country] }} → {{ $countries[$route->to_country] }}
            </a>
          @endforeach
        </nav>
        <div class="fgrid" id="flights-grid">
          @foreach($filteredFlights as $flight)
            <x-vol-card :agency="$agency" :flight="$flight" />
          @endforeach
        </div>
        @if($filteredFlights->count() === 0)
          <p class="empty">Aucun vol à afficher pour ce trajet.</p>
        @endif
      </section>
    </div>
  </main>
@endsection

@push('scripts')
<script>
(function () {
  const $ = s => document.querySelector(s);
  let masquer = false;

  function afficher() {
    const flights = document.querySelectorAll('.fc');
    flights.forEach(f => {
      if (masquer && f.classList.contains('full')) {
        f.style.display = 'none';
      } else {
        f.style.display = 'grid';
      }
    });

    if (document.querySelectorAll('.fc:not([style*="display: none"])').length === 0) {
      const grid = document.getElementById('flights-grid');
      if (!document.querySelector('.empty')) {
        const empty = document.createElement('p');
        empty.className = 'empty';
        empty.textContent = 'Aucun vol à afficher pour ce trajet.';
        grid.after(empty);
      }
    } else {
      const empty = document.querySelector('.empty');
      if (empty) empty.remove();
    }
  }

  $("#hf").onchange = e => { masquer = e.target.checked; afficher(); };
})();
</script>
@endpush
