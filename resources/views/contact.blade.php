@extends('layouts.app')

@section('title', 'Contact | AB-Flash')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/info-pages.css') }}">
@endpush

@section('content')
<main class="info-page">
  <section class="info-hero info-hero--compact">
    <div class="w">
      <span class="info-eyebrow">Contact</span>
      <h1>Une question ? Parlons-en.</h1>
      <p>Contactez-nous par e-mail ou WhatsApp. Nous vous répondrons dès que possible.</p>
    </div>
  </section>

  <section class="w contact-content">
    <div class="contact-intro">
      <span class="info-eyebrow">Nous sommes à votre écoute</span>
      <h2>Comment pouvons-nous vous aider ?</h2>
      <p>Pour une question sur la plateforme ou un partenariat, écrivez-nous. Pour confirmer un tarif ou une place, contactez directement l'agence concernée.</p>
    </div>
    <div class="contact-card-grid">
      <a class="contact-card" href="mailto:elaminebenabdou68@gmail.com">
        <span class="contact-card-icon" aria-hidden="true">
          <svg viewBox="0 0 24 24"><path d="M3 5h18v14H3z"></path><path d="m3 6 9 7 9-7"></path></svg>
        </span>
        <span class="contact-card-label">E-mail</span>
        <strong>elaminebenabdou68@gmail.com</strong>
        <span class="contact-card-action">Nous écrire</span>
      </a>
      <a class="contact-card" href="https://wa.me/221774409566" target="_blank" rel="noopener noreferrer">
        <span class="contact-card-icon" aria-hidden="true">
          <svg viewBox="0 0 24 24"><path d="M20.5 11.6a8.5 8.5 0 0 1-12.6 7.5L3 20l.9-4.7a8.5 8.5 0 1 1 16.6-3.7Z"></path><path d="M8.2 8.1c.2-.5.4-.5.7-.5h.5c.2 0 .3.1.4.4l.7 1.7c.1.2 0 .4-.1.5l-.5.6c-.2.2-.2.4 0 .6.5.9 1.2 1.5 2.1 2 .2.1.4.1.6-.1l.7-.8c.2-.2.3-.2.6-.1l1.6.8c.2.1.3.2.3.4 0 .4-.2 1-.6 1.3-.4.4-1 .6-1.6.5-1-.1-2.3-.7-3.5-1.7-1.4-1.2-2.4-2.7-2.6-3.7-.2-.8.2-1.5.7-1.9Z"></path></svg>
        </span>
        <span class="contact-card-label">WhatsApp / téléphone</span>
        <strong>+221 77 440 95 66</strong>
        <span class="contact-card-action">Ouvrir WhatsApp</span>
      </a>
    </div>
    <p class="contact-note">Pour réserver un envoi, adressez-vous directement à l'agence afin de confirmer le tarif et la disponibilité.</p>
  </section>
</main>
@endsection
