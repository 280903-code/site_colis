@extends('layouts.app')

@section('title', 'Trouver une agence | AB-Flash')

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
        <select id="f" name="from">
          <option value="_">Tous les pays</option><option value="SN">Sénégal</option><option value="KM">Comores</option><option value="FR">France</option>
        </select>
      </div>
      <button type="button" class="swap" id="sw" aria-label="Inverser départ et destination">⇄</button>
      <div>
        <label for="t">Destination</label>
        <select id="t" name="to">
          <option value="_">Tous les pays</option><option value="SN">Sénégal</option><option value="KM">Comores</option><option value="FR">France</option>
        </select>
      </div>
      <button class="go">Rechercher</button>
    </form>
  </div>
  <nav class="routes" id="routes" aria-label="Trajets fréquents"></nav>

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
  const countries = {SN:"Sénégal", KM:"Comores", FR:"France"};
  const quick = [["SN","KM"], ["KM","SN"], ["SN","FR"], ["FR","SN"], ["FR","KM"], ["KM","FR"]];
  const from = "{{ $from }}";
  const to = "{{ $to }}";

  $("#f").value = from;
  $("#t").value = to;

  $("#sw").onclick = () => { const a = $("#f").value; $("#f").value = $("#t").value; $("#t").value = a; };

  $("#routes").innerHTML = quick.map(q =>
    `<a class="chip${q[0] === from && q[1] === to ? " on" : ""}" href="{{ url('/agences') }}?from=${q[0]}&to=${q[1]}">${countries[q[0]]} → ${countries[q[1]]}</a>`
  ).join("");
})();
</script>
@endpush
