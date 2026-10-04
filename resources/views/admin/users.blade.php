@extends('layouts.app')

@section('title', 'Gestion des utilisateurs | AB-Flash')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('content')
<div class="dashboard-container">
  <div class="dashboard-header">
    <h1>Gestion des utilisateurs</h1>
  </div>

  <div class="table-container">
    <div class="table-header">
      <h2>Utilisateurs ({{ $users->total() }})</h2>
      <div class="filters">
        <form method="GET" style="display: flex; gap: 12px; align-items: center;">
          <input type="text" name="search" value="{{ $search }}" placeholder="Rechercher..." style="padding: 8px 12px; border: 1.5px solid var(--l); border-radius: 8px;">
          <button type="submit" class="btn btn-sm">Rechercher</button>
        </form>
      </div>
    </div>
    <table class="data-table">
      <thead>
        <tr>
          <th>Nom</th>
          <th>Email</th>
          <th>Rôle</th>
          <th>Email vérifié</th>
          <th>Date d'inscription</th>
        </tr>
      </thead>
      <tbody>
        @foreach($users as $user)
        <tr>
          <td>{{ $user->name }}</td>
          <td>{{ $user->email }}</td>
          <td>
            <span class="status-badge {{ $user->role === 'admin' ? 'status-approved' : 'status-pending' }}">
              {{ $user->role === 'admin' ? 'Admin' : 'Agence' }}
            </span>
          </td>
          <td>{{ $user->email_verified_at ? '✓' : '✗' }}</td>
          <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
    {{ $users->links() }}
  </div>
</div>
@endsection
