<aside class="admin-sidebar" aria-label="Espace d'administration">
  <a class="admin-sidebar__brand" href="{{ route('admin.dashboard') }}">
    <span class="admin-sidebar__brand-mark">K</span>
    <span>
      <strong>KONEXA</strong>
      <small>Administration</small>
    </span>
  </a>

  <nav class="admin-sidebar__nav" aria-label="Navigation administration">
    <a href="{{ route('admin.dashboard') }}" class="admin-sidebar__link {{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}" @if(request()->routeIs('admin.dashboard')) aria-current="page" @endif>
      <svg class="admin-sidebar__icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
        <path d="m3.5 10 8.5-7 8.5 7v10.5h-6v-7h-5v7h-6V10Z" />
      </svg>
      <span>Vue d'ensemble</span>
    </a>
    <a href="{{ route('admin.agencies') }}" class="admin-sidebar__link {{ request()->routeIs('admin.agencies*') ? 'is-active' : '' }}" @if(request()->routeIs('admin.agencies*')) aria-current="page" @endif>
      <svg class="admin-sidebar__icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
        <path d="M3 21h18M5 21V7l7-4 7 4v14M9 10h.01M15 10h.01M9 14h.01M15 14h.01M10 21v-4h4v4" />
      </svg>
      <span>Agences</span>
    </a>
    <a href="{{ route('admin.flights') }}" class="admin-sidebar__link {{ request()->routeIs('admin.flights') ? 'is-active' : '' }}" @if(request()->routeIs('admin.flights')) aria-current="page" @endif>
      <svg class="admin-sidebar__icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
        <path d="m3.2 14.8 7.1-2.3L6.5 4.7a1.5 1.5 0 0 1 2.2-1.9l6.7 7.8 4.5-1.5a2 2 0 0 1 1.2 3.8L5.1 19a2 2 0 0 1-1.9-.4l-1.1-.9a1.8 1.8 0 0 1 1.1-2.9Z" />
      </svg>
      <span>Vols</span>
    </a>
    <a href="{{ route('admin.users') }}" class="admin-sidebar__link {{ request()->routeIs('admin.users') ? 'is-active' : '' }}" @if(request()->routeIs('admin.users')) aria-current="page" @endif>
      <svg class="admin-sidebar__icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
        <path d="M16 20v-1.7a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4V20M9.5 10.5a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM16 3a4 4 0 0 1 0 7.8M21 20v-1.7a4 4 0 0 0-3-3.9" />
      </svg>
      <span>Utilisateurs</span>
    </a>
    <a href="{{ route('admin.activity') }}" class="admin-sidebar__link {{ request()->routeIs('admin.activity') ? 'is-active' : '' }}" @if(request()->routeIs('admin.activity')) aria-current="page" @endif>
      <svg class="admin-sidebar__icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
        <path d="M3 12h4l2.5-7 5 14 2.5-7h4M4 4h.01M20 20h.01" />
      </svg>
      <span>Activités</span>
    </a>
  </nav>

  <div class="admin-sidebar__account">
    <span class="admin-sidebar__avatar" aria-hidden="true">{{ mb_substr(auth()->user()->name, 0, 1) }}</span>
    <span class="admin-sidebar__account-info">
      <strong>{{ auth()->user()->name }}</strong>
      <small>Administrateur</small>
    </span>
    <a href="{{ route('profile.edit') }}" class="admin-sidebar__profile-link {{ request()->routeIs('profile.edit') ? 'is-active' : '' }}" @if(request()->routeIs('profile.edit')) aria-current="page" @endif>
      Modifier mon profil
    </a>
  </div>
</aside>
