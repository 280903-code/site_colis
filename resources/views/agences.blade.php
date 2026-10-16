@extends('layouts.app')

@section('title', 'Trouver une agence | KONEXA')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/hero.css') }}">
<link rel="stylesheet" href="{{ asset('css/components.css') }}">
<link rel="stylesheet" href="{{ asset('css/agences.css') }}">
@endpush

@section('content')
  <section class="hero hero--compact">
    <div class="w"><h1>Trouver une agence</h1></div>
  </section>

  <div class="w">
    <form class="search" id="sf" action="{{ url('/agences') }}" method="get">
      <div>
        <label for="f">Départ</label>
        <input id="f" name="from" value="{{ $from === '_' ? '' : \App\Support\Country::label($from) }}" list="country-options" placeholder="Tous les pays" autocomplete="off">
      </div>
      <button type="button" class="swap" id="sw" aria-label="Inverser départ et destination">⇄</button>
      <div>
        <label for="t">Destination</label>
        <input id="t" name="to" value="{{ $to === '_' ? '' : \App\Support\Country::label($to) }}" list="country-options" placeholder="Tous les pays" autocomplete="off">
      </div>
      <button class="go">Rechercher</button>
    </form>
    <datalist id="country-options">
      @foreach($countryOptions as $country)
        <option value="{{ $country }}"></option>
      @endforeach
    </datalist>
  </div>
  <nav class="routes" id="routes" aria-label="Exemples de trajets internationaux">
    @foreach(\App\Support\Country::popularRoutes() as [$routeFrom, $routeTo])
      <a class="chip{{ $routeFrom === $from && $routeTo === $to ? ' on' : '' }}" href="{{ route('agences', ['from' => $routeFrom, 'to' => $routeTo]) }}">{{ \App\Support\Country::label($routeFrom) }} → {{ \App\Support\Country::label($routeTo) }}</a>
    @endforeach
  </nav>

  <main class="w">
    <h2 class="t">{{ $filteredAgencies->count() }} agence{{ $filteredAgencies->count() > 1 ? 's' : '' }}{{ $hasFilter ? ' pour ce trajet' : '' }}</h2>
    <p class="sub">{{ $subtitle }}</p>
    <div id="liste">
      @if($filteredAgencies->count() > 0)
        <div class="aglist">
          @foreach($filteredAgencies as $agency)
            <x-agence-card :agency="$agency" :from="$from" :to="$to" />
          @endforeach
        </div>
      @else
        <p class="empty">Aucune agence pour ce trajet pour le moment. Essayez un autre trajet.</p>
      @endif
    </div>
  </main>
@endsection

@push('scripts')
<script>
(function () {
  const $ = s => document.querySelector(s);
  $("#sw").onclick = () => { const a = $("#f").value; $("#f").value = $("#t").value; $("#t").value = a; };
})();
</script>
@endpush
