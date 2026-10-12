@extends('layouts.app')

@section('title', 'KONEXA | Agences de colis Sénégal, Comores, France')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/hero.css') }}">
<link rel="stylesheet" href="{{ asset('css/components.css') }}">
<link rel="stylesheet" href="{{ asset('css/agences.css') }}">
@endpush

@section('content')
  <section class="home-hero">
    <div class="home-hero__layout">
      <div class="home-hero__copy">
        <span class="home-hero__eyebrow">Sénégal · Comores · France</span>
        <h1>Vos colis voyagent mieux avec KONEXA.</h1>
        <div class="home-hero__actions">
          <a class="btn" href="{{ route('agences') }}">Trouver une agence</a>
          <a class="home-hero__secondary" href="{{ route('partner.register.form') }}">Vous êtes une agence ?</a>
        </div>
      </div>
      <div class="home-hero__visual">
        <div class="home-carousel" data-home-carousel role="region" aria-roledescription="carrousel" aria-label="Découvrez les destinations KONEXA">
          <figure class="home-carousel__slide is-active" data-carousel-slide role="group" aria-roledescription="diapositive" aria-label="1 sur 3" aria-hidden="false">
            <img src="{{ asset('img/fond.jpeg') }}" alt="Le port illuminé de Moroni au bord de l'océan">
            <figcaption class="home-carousel__caption">
              <span>Nos destinations</span>
              <strong>Des liens entre les îles et les continents</strong>
            </figcaption>
          </figure>
          <figure class="home-carousel__slide" data-carousel-slide role="group" aria-roledescription="diapositive" aria-label="2 sur 3" aria-hidden="true">
            <img src="{{ asset('img/hero-flight.jpg') }}" alt="Vue sur l'aile d'un avion en plein vol">
            <figcaption class="home-carousel__caption">
              <span>Des départs réguliers</span>
              <strong>Suivez les prochains vols disponibles</strong>
            </figcaption>
          </figure>
          <figure class="home-carousel__slide" data-carousel-slide role="group" aria-roledescription="diapositive" aria-label="3 sur 3" aria-hidden="true">
            <img src="{{ asset('img/hero-airport.jpg') }}" alt="Vue d'un coucher de soleil depuis le hublot d'un avion">
            <figcaption class="home-carousel__caption">
              <span>Un envoi plus simple</span>
              <strong>Comparez avant de confier votre colis</strong>
            </figcaption>
          </figure>
          <div class="home-carousel__controls" role="group" aria-label="Choisir une photo">
            <button type="button" data-carousel-to="0" aria-label="Photo 1" aria-pressed="true"></button>
            <button type="button" data-carousel-to="1" aria-label="Photo 2" aria-pressed="false"></button>
            <button type="button" data-carousel-to="2" aria-label="Photo 3" aria-pressed="false"></button>
          </div>
        </div>
      </div>
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
  <nav class="routes" id="routes" data-route-scroller aria-label="Trajets fréquents"></nav>

  <main class="w">
    <h2 class="t">Les agences</h2>
    <p class="sub">Tous les trajets</p>
    <div id="liste">
      @php
        $agencyRows = $agencies->split(2);
      @endphp
      <div class="agency-marquees" role="group" aria-label="Agences partenaires">
        @foreach($agencyRows as $rowIndex => $row)
          @if($row->isNotEmpty())
            <div class="agency-marquee" role="region" aria-label="Agences partenaires, ligne {{ $rowIndex + 1 }}" tabindex="0">
              <div class="agency-marquee__track">
                <div class="agency-marquee__group">
                  @foreach($row as $agency)
                    <x-agence-card :agency="$agency" />
                  @endforeach
                </div>
              </div>
            </div>
          @endif
        @endforeach
      </div>
      @if($agencies->count() > 4)
        <p class="more"><a class="btn" href="{{ url('/agences') }}">Voir toutes les agences ({{ $agencies->count() }})</a></p>
      @endif
    </div>
  </main>
@endsection

@push('scripts')
<script src="{{ asset('js/home-carousel.js') }}"></script>
<script>
(function () {
  const $ = s => document.querySelector(s);
  const countries = {SN:"Sénégal", KM:"Comores", FR:"France"};
  const quick = [["SN","KM"], ["KM","SN"], ["SN","FR"], ["FR","SN"], ["FR","KM"], ["KM","FR"]];

  $("#sw").onclick = () => { const a = $("#f").value; $("#f").value = $("#t").value; $("#t").value = a; };

  $("#routes").innerHTML = quick.map(q =>
    `<a class="chip" href="{{ url('/agences') }}?from=${q[0]}&to=${q[1]}">${countries[q[0]]} → ${countries[q[1]]}</a>`
  ).join("");

  const routes = $("#routes");
  const reducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)");
  let scrollFrame;
  let lastFrameTime = 0;
  let preciseScrollLeft = routes.scrollLeft;
  let direction = 1;
  let pauseUntil = 0;
  let isPointerOver = false;
  let hasFocus = false;
  let isPointerDown = false;

  function animateRoutes(timestamp) {
    if (isPointerOver || hasFocus || isPointerDown || reducedMotion.matches || document.hidden) {
      scrollFrame = undefined;
      return;
    }

    if (lastFrameTime) {
      const elapsed = Math.min(timestamp - lastFrameTime, 50);
      const maxScroll = routes.scrollWidth - routes.clientWidth;

      if (maxScroll <= 0) {
        routes.scrollLeft = 0;
        scrollFrame = undefined;
        lastFrameTime = 0;
        return;
      }

      if (timestamp >= pauseUntil) {
        preciseScrollLeft += direction * elapsed * 0.012;

        if (preciseScrollLeft >= maxScroll || preciseScrollLeft <= 0) {
          preciseScrollLeft = Math.max(0, Math.min(preciseScrollLeft, maxScroll));
          direction = preciseScrollLeft >= maxScroll ? -1 : 1;
          pauseUntil = timestamp + 700;
        }

        routes.scrollLeft = preciseScrollLeft;
      }
    }

    lastFrameTime = timestamp;
    scrollFrame = window.requestAnimationFrame(animateRoutes);
  }

  function updateRouteScrolling() {
    window.cancelAnimationFrame(scrollFrame);
    scrollFrame = undefined;
    lastFrameTime = 0;
    preciseScrollLeft = routes.scrollLeft;
    routes.classList.toggle("is-scrollable", routes.scrollWidth > routes.clientWidth);

    if (!isPointerOver && !hasFocus && !isPointerDown && !reducedMotion.matches && !document.hidden) {
      scrollFrame = window.requestAnimationFrame(animateRoutes);
    }
  }

  routes.addEventListener("pointerenter", () => {
    isPointerOver = true;
    updateRouteScrolling();
  });
  routes.addEventListener("pointerleave", () => {
    isPointerOver = false;
    isPointerDown = false;
    updateRouteScrolling();
  });
  routes.addEventListener("focusin", () => {
    hasFocus = true;
    updateRouteScrolling();
  });
  routes.addEventListener("focusout", (event) => {
    if (!routes.contains(event.relatedTarget)) {
      hasFocus = false;
      updateRouteScrolling();
    }
  });
  routes.addEventListener("pointerdown", () => {
    isPointerDown = true;
    updateRouteScrolling();
  });
  routes.addEventListener("pointerup", () => {
    isPointerDown = false;
    updateRouteScrolling();
  });
  routes.addEventListener("pointercancel", () => {
    isPointerDown = false;
    updateRouteScrolling();
  });
  window.addEventListener("resize", updateRouteScrolling);
  document.addEventListener("visibilitychange", updateRouteScrolling);
  reducedMotion.addEventListener("change", updateRouteScrolling);
  updateRouteScrolling();
})();
</script>
@endpush
