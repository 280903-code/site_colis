<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <meta name="description"
    content="Comparez les agences de colis entre le Sénégal, les Comores et la France, voyez les vols et les kilos restants.">
  <title>@yield('title', 'KONEXA | Agences de colis Sénégal, Comores, France')</title>
  <link rel="icon"
    href="data:image/svg+xml,&lt;svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22&gt;&lt;text y=%22.9em%22 font-size=%2290%22&gt;📦&lt;/text&gt;&lt;/svg&gt;">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  @stack('styles')
  <link rel="stylesheet" href="{{ asset('css/base.css') }}">
  <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
</head>

<body>
  <header>
    <div class="w">
      <a class="logo" href="{{ url('/') }}">KO<span>NEXA</span></a>
      <nav class="nav-menu" id="navMenu">
        <a class="n" href="{{ route('Accueil') }}">Accueil</a>
        <a class="n" href="{{ route('agences') }}">Toutes les agences</a>
        <a class="n" href="{{ route('about') }}">À propos</a>
        <a class="n" href="{{ route('contact') }}">Contact</a>
        @if(auth()->check())
          @if(auth()->user()->isAdmin())
            <a class="n" href="{{ url('/admin') }}">Admin</a>
          @elseif(auth()->user()->isAgencyAdmin())
            <a class="n" href="{{ url('/admin-agence') }}">Mon espace</a>
          @endif
          <form method="POST" action="{{ route('logout') }}" style="display: inline;">
            @csrf
            <button type="submit" class="n"
              style="background: none; border: none; color: #D5EBDC; font-size: 0.9rem; font-weight: 600; cursor: pointer;">Déconnexion</button>
          </form>
        @else
          <a class="n" href="{{ route('login') }}">Connexion</a>
          <a class="n btn-mobile" href="{{ route('partner.register.form') }}"
            style="background: var(--j); color: var(--t); padding: 8px 16px; border-radius: 8px;">Devenir partenaire</a>
        @endif
      </nav>
      <div class="header-actions">
        <button class="theme-toggle" id="themeToggle" type="button" aria-label="Activer le mode sombre"
          aria-pressed="false">
          <svg class="theme-icon theme-icon--sun" viewBox="0 0 24 24" aria-hidden="true">
            <circle cx="12" cy="12" r="4"></circle>
            <path
              d="M12 2v2m0 16v2M4.93 4.93l1.42 1.42m11.3 11.3 1.42 1.42M2 12h2m16 0h2M4.93 19.07l1.42-1.42m11.3-11.3 1.42-1.42">
            </path>
          </svg>
          <svg class="theme-icon theme-icon--moon" viewBox="0 0 24 24" aria-hidden="true">
            <path d="M20.2 15.5A8.5 8.5 0 0 1 8.5 3.8 8.5 8.5 0 1 0 20.2 15.5Z"></path>
          </svg>
        </button>
        <button class="mobile-menu-btn" id="mobileMenuBtn" type="button" aria-label="Ouvrir le menu"
          aria-controls="navMenu" aria-expanded="false">
          <svg viewBox="0 0 24 24" aria-hidden="true">
            <path d="M4 6h16M4 12h16M4 18h16"></path>
          </svg>
        </button>
      </div>
    </div>
  </header>

  @yield('content')

  <footer>
    <div class="w footer-main">
      <div class="footer-brand">
        <a class="logo" href="{{ route('home') }}">KONE<span>XA</span></a>
        <p>Comparez les agences et trouvez vos solutions d'envoi entre le Sénégal, les Comores et la France.</p>
      </div>
      <div class="footer-column">
        <h2>Découvrir</h2>
        <a href="{{ route('agences') }}">Toutes les agences</a>
        <a href="{{ route('about') }}">À propos</a>
        <a href="{{ route('partner.register.form') }}">Devenir partenaire</a>
      </div>
      <div class="footer-column footer-contact">
        <h2>Nous contacter</h2>
        <a href="mailto:elaminebenabdou68@gmail.com">elaminebenabdou68@gmail.com</a>
        <a href="tel:+221774409566">+221 77 440 95 66</a>
        <a href="https://wa.me/221774409566" target="_blank" rel="noopener noreferrer">Écrire sur WhatsApp</a>
        <a href="{{ route('contact') }}">Page contact</a>
      </div>
      <div class="footer-social">
        <h2>Suivez-nous</h2>
        <div class="social-links">
          <a href="https://www.facebook.com/votre-page" target="_blank" rel="noopener noreferrer"
            aria-label="Facebook (lien à personnaliser)">
            <svg viewBox="0 0 24 24" aria-hidden="true">
              <path
                d="M13.5 21v-8h2.7l.4-3.1h-3.1v-2c0-.9.3-1.5 1.6-1.5h1.7V3.6c-.3 0-1.3-.1-2.5-.1-2.5 0-4.2 1.5-4.2 4.3v2.1H7.3V13h2.8v8h3.4Z">
              </path>
            </svg>
          </a>
          <a href="https://www.instagram.com/votre-compte" target="_blank" rel="noopener noreferrer"
            aria-label="Instagram (lien à personnaliser)">
            <svg viewBox="0 0 24 24" aria-hidden="true">
              <rect x="3" y="3" width="18" height="18" rx="5"></rect>
              <circle cx="12" cy="12" r="4"></circle>
              <circle class="social-dot" cx="17.5" cy="6.8" r="1"></circle>
            </svg>
          </a>
          <a href="https://www.tiktok.com/@votre-compte" target="_blank" rel="noopener noreferrer"
            aria-label="TikTok (lien à personnaliser)">
            <svg viewBox="0 0 24 24" aria-hidden="true">
              <path
                d="M14 3v11.1a3.2 3.2 0 1 1-2.8-3.2V8a7.1 7.1 0 1 0 6.1 7V9.5a8.1 8.1 0 0 0 3.7.9V7.2c-3.1-.1-5-1.8-5.4-4.2H14Z">
              </path>
            </svg>
          </a>
        </div>
        <p class="social-note">Liens des réseaux à personnaliser dans le pied de page.</p>
      </div>
    </div>
    <div class="w footer-bottom">
      <span>&copy; {{ now()->year }} KONEXA. Tous droits réservés.</span>
      <span>Les tarifs et disponibilités sont communiqués par les agences. Confirmez-les avant tout dépôt.</span>
    </div>
  </footer>
  <script src="{{ asset('js/theme.js') }}"></script>
  <script>
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const navMenu = document.getElementById('navMenu');

    mobileMenuBtn?.addEventListener('click', function () {
      const isOpen = navMenu.classList.toggle('active');
      mobileMenuBtn.setAttribute('aria-expanded', String(isOpen));
      mobileMenuBtn.setAttribute('aria-label', isOpen ? 'Fermer le menu' : 'Ouvrir le menu');
    });

    navMenu?.addEventListener('click', function (event) {
      if (event.target.closest('a')) {
        navMenu.classList.remove('active');
        mobileMenuBtn?.setAttribute('aria-expanded', 'false');
        mobileMenuBtn?.setAttribute('aria-label', 'Ouvrir le menu');
      }
    });
  </script>
  @stack('scripts')
</body>

</html>