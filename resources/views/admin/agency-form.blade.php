@extends('layouts.app')

@section('title', 'Modifier une agence | KONEXA')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('content')
<div class="dashboard-container">
  <div class="dashboard-header">
    <h1>Modifier l'agence</h1>
    <a href="{{ route('admin.agencies') }}" class="btn o btn-sm">Retour aux agences</a>
  </div>

  <div class="table-container flight-form-card">
    <form method="POST" action="{{ route('admin.agencies.update', $agency) }}" class="admin-agency-form">
      @csrf
      @method('PUT')

      <div>
        <label for="name">Nom de l'agence</label>
        <input id="name" name="name" type="text" value="{{ old('name', $agency->name) }}" required>
        @error('name') <div class="error">{{ $message }}</div> @enderror
      </div>

      <div>
        <label for="description">Description</label>
        <textarea id="description" name="description" required>{{ old('description', $agency->description) }}</textarea>
        @error('description') <div class="error">{{ $message }}</div> @enderror
      </div>

      <div>
        <label for="address">Adresse</label>
        <input id="address" name="address" type="text" value="{{ old('address', $agency->address) }}" required>
        @error('address') <div class="error">{{ $message }}</div> @enderror
      </div>

      <div>
        <label for="opening_hours">Horaires d'ouverture</label>
        <input id="opening_hours" name="opening_hours" type="text" value="{{ old('opening_hours', $agency->opening_hours) }}" required>
        @error('opening_hours') <div class="error">{{ $message }}</div> @enderror
      </div>

      <div>
        <label for="whatsapp">Numéro WhatsApp (chiffres uniquement)</label>
        <input id="whatsapp" name="whatsapp" type="text" value="{{ old('whatsapp', $agency->whatsapp) }}" required>
        @error('whatsapp') <div class="error">{{ $message }}</div> @enderror
      </div>

      <div class="admin-agency-form__actions">
        <button type="submit" class="btn">Enregistrer les modifications</button>
        @if($agency->status === 'pending')
          <button type="submit" form="approve-agency-form" class="btn o">Approuver l'agence</button>
        @endif
      </div>
    </form>
    @if($agency->status === 'pending')
      <form id="approve-agency-form" method="POST" action="{{ route('admin.agencies.approve', $agency) }}">
        @csrf
      </form>
    @endif
  </div>
</div>
@endsection
