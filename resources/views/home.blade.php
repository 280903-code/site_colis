@extends('layouts.app')

@section('title', 'AB-Flash | Agences de colis Sénégal, Comores, France')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/hero.css') }}">
<link rel="stylesheet" href="{{ asset('css/components.css') }}">
<link rel="stylesheet" href="{{ asset('css/agences.css') }}">
@endpush

@section('content')
  <section class="hero">
    <div class="w">
      <h1>Envoyez vos colis entre le Sénégal, les Comores et la France.</h1>
      <p>Comparez les agences, voyez les vols disponibles et les kilos restants, puis réservez par WhatsApp.</p>
    </div>
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
    <h2 class="t">Les agences</h2>
    <p class="sub">Tous les trajets</p>
    <div id="liste">
      <div class="aglist">
        @foreach($agencies->take(4) as $agency)
          <x-agence-card :agency="$agency" />
        @endforeach
      </div>
      @if($agencies->count() > 4)
        <p class="more"><a class="btn" href="{{ url('/agences') }}">Voir toutes les agences ({{ $agencies->count() }})</a></p>
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

  $("#sw").onclick = () => { const a = $("#f").value; $("#f").value = $("#t").value; $("#t").value = a; };

  $("#routes").innerHTML = quick.map(q =>
    `<a class="chip" href="{{ url('/agences') }}?from=${q[0]}&to=${q[1]}">${countries[q[0]]} → ${countries[q[1]]}</a>`
  ).join("");
})();
</script>
@endpush
