@extends('layouts.app')

@section('title', 'Mon profil administrateur | KONEXA')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('content')
<div class="admin-shell">
  @include('admin.partials.navigation')
  <main class="dashboard-container admin-main">
    <header class="dashboard-header">
      <h1>Mon profil</h1>
      <p>Modifiez vos informations personnelles et votre mot de passe.</p>
    </header>

    @include('admin.partials.feedback')

    @if(session('status') === 'profile-updated')
      <div class="admin-feedback admin-feedback--success" role="status">Vos informations personnelles ont été mises à jour.</div>
    @elseif(session('status') === 'password-updated')
      <div class="admin-feedback admin-feedback--success" role="status">Votre mot de passe a été mis à jour.</div>
    @endif

    <div class="admin-profile-grid">
      <section class="table-container admin-profile-card">
        <div class="table-header">
          <h2>Informations personnelles</h2>
        </div>
        <form method="POST" action="{{ route('profile.update') }}" class="admin-profile-form">
          @csrf
          @method('PATCH')

          <div>
            <label for="name">Nom</label>
            <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" autocomplete="name" required>
            @error('name') <p class="admin-profile-error">{{ $message }}</p> @enderror
          </div>
          <div>
            <label for="email">Adresse e-mail</label>
            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" autocomplete="email" required>
            @error('email') <p class="admin-profile-error">{{ $message }}</p> @enderror
          </div>

          <button type="submit" class="btn">Enregistrer les informations</button>
        </form>
      </section>

      <section class="table-container admin-profile-card">
        <div class="table-header">
          <h2>Changer le mot de passe</h2>
        </div>
        <form method="POST" action="{{ route('password.update') }}" class="admin-profile-form">
          @csrf
          @method('PUT')

          <div>
            <label for="current_password">Mot de passe actuel</label>
            <input id="current_password" name="current_password" type="password" autocomplete="current-password" required>
            @error('current_password', 'updatePassword') <p class="admin-profile-error">{{ $message }}</p> @enderror
          </div>
          <div>
            <label for="password">Nouveau mot de passe</label>
            <input id="password" name="password" type="password" autocomplete="new-password" required>
            @error('password', 'updatePassword') <p class="admin-profile-error">{{ $message }}</p> @enderror
          </div>
          <div>
            <label for="password_confirmation">Confirmer le nouveau mot de passe</label>
            <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required>
          </div>

          <button type="submit" class="btn">Mettre à jour le mot de passe</button>
        </form>
      </section>
    </div>
  </main>
</div>
@endsection
