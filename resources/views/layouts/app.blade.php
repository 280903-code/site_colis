<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="description" content="Comparez les agences de colis entre le Sénégal, les Comores et la France, voyez les vols et les kilos restants.">
<title>@yield('title', 'AB-Flash | Agences de colis Sénégal, Comores, France')</title>
<link rel="icon" href="data:image/svg+xml,&lt;svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22&gt;&lt;text y=%22.9em%22 font-size=%2290%22&gt;📦&lt;/text&gt;&lt;/svg&gt;">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
@stack('styles')
<link rel="stylesheet" href="{{ asset('css/base.css') }}">
<link rel="stylesheet" href="{{ asset('css/theme.css') }}">
</head>
<body>
<header>
  <div class="w">
    <a class="logo" href="{{ url('/') }}">AB-<span>Flash</span></a>
    <button class="mobile-menu-btn" id="mobileMenuBtn" aria-label="Menu">☰</button>
    <nav class="nav-menu" id="navMenu">
      <a class="n" href="{{ url('/agences') }}">Toutes les agences</a>
      @if(auth()->check())
        @if(auth()->user()->isAdmin())
          <a class="n" href="{{ url('/admin') }}">Admin</a>
        @elseif(auth()->user()->isAgencyAdmin())
          <a class="n" href="{{ url('/admin-agence') }}">Mon espace</a>
        @endif
        <form method="POST" action="{{ route('logout') }}" style="display: inline;">
          @csrf
          <button type="submit" class="n" style="background: none; border: none; color: #D5EBDC; font-size: 0.9rem; font-weight: 600; cursor: pointer;">Déconnexion</button>
        </form>
      @else
        <a class="n" href="{{ route('login') }}">Connexion</a>
        <a class="n btn-mobile" href="{{ route('partner.register.form') }}" style="background: var(--j); color: var(--t); padding: 8px 16px; border-radius: 8px;">Devenir partenaire</a>
      @endif
    </nav>
  </div>
</header>

@yield('content')

<footer>
  <div class="w">
    <b>AB-Flash</b>
    <span>Annuaire des agences de colis entre le Sénégal, les Comores et la France.</span>
    <span>Les disponibilités et tarifs sont donnés par les agences : confirmez toujours avec elles par WhatsApp avant de déposer vos colis.</span>
  </div>
</footer>
<script src="{{ asset('js/theme.js') }}"></script>
<script>
document.getElementById('mobileMenuBtn')?.addEventListener('click', function() {
  document.getElementById('navMenu').classList.toggle('active');
});
</script>
@stack('scripts')
</body>
</html>
