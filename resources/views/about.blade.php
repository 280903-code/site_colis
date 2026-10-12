@extends('layouts.app')

@section('title', 'À propos | KONEXA')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/info-pages.css') }}">
@endpush

@section('content')
<main class="info-page">
  <section class="info-hero">
    <div class="w">
      <span class="info-eyebrow">À propos de KONEXA</span>
      <h1>Les envois de colis, plus simples et plus clairs.</h1>
      <p>KONEXA aide chacun à trouver une agence et un vol adaptés pour envoyer des colis entre le Sénégal, les Comores et la France.</p>
      <a href="{{ route('agences') }}" class="btn">Trouver une agence</a>
    </div>
  </section>

  <section class="w info-content">
    <div class="info-section-heading">
      <span class="info-eyebrow">Notre rôle</span>
      <h2>Une plateforme pour mieux s'y retrouver</h2>
      <p>Nous rassemblons les informations utiles des agences au même endroit, pour faciliter la recherche et la prise de contact.</p>
    </div>
    <div class="info-card-grid">
      <article class="info-card">
        <span class="info-card-number">01</span>
        <h3>Comparer les agences</h3>
        <p>Découvrez les agences disponibles et consultez leurs trajets desservis.</p>
      </article>
      <article class="info-card">
        <span class="info-card-number">02</span>
        <h3>Consulter les vols</h3>
        <p>Repérez les prochaines dates, les kilos restants et les tarifs communiqués.</p>
      </article>
      <article class="info-card">
        <span class="info-card-number">03</span>
        <h3>Contacter directement</h3>
        <p>Échangez avec l'agence pour confirmer les disponibilités et organiser votre envoi.</p>
      </article>
    </div>
    <div class="info-notice">
      <h2>Une information importante</h2>
      <p>Les tarifs et les disponibilités sont renseignés par les agences et peuvent évoluer. Contactez toujours l'agence avant de déposer un colis.</p>
    </div>
  </section>
</main>
@endsection
