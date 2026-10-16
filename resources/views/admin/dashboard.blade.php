@extends('layouts.app')

@section('title', 'Tableau de bord Admin | KONEXA')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('content')
<div class="admin-shell">
  @include('admin.partials.navigation')
  <main class="dashboard-container admin-main">
  <div class="dashboard-header">
    <h1>Tableau de bord</h1>
    <p>Bienvenue dans votre espace de gestion KONEXA.</p>
  </div>

  @include('admin.partials.feedback')

  <section class="stats-grid" aria-label="Indicateurs de gestion">
    <article class="stat-card">
      <div class="number">{{ $stats['total_agencies'] }}</div>
      <div class="label">Agences totales</div>
    </article>
    <article class="stat-card">
      <div class="number">{{ $stats['pending_agencies'] }}</div>
      <div class="label">En attente</div>
    </article>
    <article class="stat-card">
      <div class="number">{{ $stats['approved_agencies'] }}</div>
      <div class="label">Approuvées</div>
    </article>
    <article class="stat-card">
      <div class="number">{{ $stats['upcoming_flights'] }}</div>
      <div class="label">Vols à venir</div>
    </article>
    <article class="stat-card">
      <div class="number">{{ $stats['total_users'] }}</div>
      <div class="label">Utilisateurs</div>
    </article>
  </section>

  @if($pendingAgencies->count() > 0)
  <div class="table-container">
    <div class="table-header">
      <h2>Demandes en attente ({{ $pendingAgencies->count() }})</h2>
      <a href="{{ route('admin.agencies', ['status' => 'pending']) }}" class="btn btn-sm">Voir tout</a>
    </div>
    <div class="table-scroll" tabindex="0" role="region" aria-label="Demandes d'agences en attente">
    <table class="data-table">
      <thead>
        <tr>
          <th>Agence</th>
          <th>Email</th>
          <th>WhatsApp</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($pendingAgencies as $agency)
        <tr>
          <td>
            <strong>{{ $agency->name }}</strong><br>
            <small>{{ $agency->address }}</small>
          </td>
          <td>{{ $agency->user->email }}</td>
          <td>{{ $agency->whatsapp }}</td>
          <td>
            <div class="actions">
              <form method="POST" action="{{ route('admin.agencies.approve', $agency) }}" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-sm">Approuver</button>
              </form>
              <a href="{{ route('admin.agencies.edit', $agency) }}" class="btn o btn-sm">Modifier</a>
              <button type="button" data-reject-agency="{{ $agency->id }}" data-reject-url="{{ route('admin.agencies.reject', $agency) }}" class="btn o btn-sm">Rejeter</button>
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
    </div>
  </div>
  @endif

  <div class="table-container">
    <div class="table-header">
      <h2>Derniers inscrits</h2>
    </div>
    <div class="table-scroll" tabindex="0" role="region" aria-label="Derniers inscrits">
    <table class="data-table">
      <thead>
        <tr>
          <th>Nom</th>
          <th>Email</th>
          <th>Rôle</th>
          <th>Date</th>
        </tr>
      </thead>
      <tbody>
        @foreach($recentUsers as $user)
        <tr>
          <td>{{ $user->name }}</td>
          <td>{{ $user->email }}</td>
          <td>
            <span class="status-badge {{ $user->role === 'admin' ? 'status-approved' : 'status-pending' }}">
              {{ $user->role === 'admin' ? 'Admin' : 'Agence' }}
            </span>
          </td>
          <td>{{ $user->created_at->format('d/m/Y') }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
    </div>
  </div>

  <div class="table-container">
    <div class="table-header">
      <h2>Activité récente</h2>
      <a href="{{ route('admin.activity') }}" class="btn btn-sm">Voir tout</a>
    </div>
    <div class="table-scroll" tabindex="0" role="region" aria-label="Activité récente">
    <table class="data-table">
      <thead>
        <tr>
          <th>Utilisateur</th>
          <th>Action</th>
          <th>Description</th>
          <th>Date</th>
        </tr>
      </thead>
      <tbody>
        @foreach($recentActivity as $activity)
        <tr>
          <td>{{ $activity->user->name }}</td>
          <td>{{ $activity->action }}</td>
          <td>{{ $activity->description }}</td>
          <td>{{ $activity->created_at->format('d/m/Y H:i') }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
    </div>
  </div>
  </main>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="modal-overlay" hidden>
  <div class="modal">
    <h2>Rejeter l'agence</h2>
    <form method="POST" action="" id="rejectForm">
      @csrf
      <input type="hidden" name="agency_id" id="rejectAgencyId">
      <div class="modal-form">
        <label for="reason">Motif du rejet</label>
        <textarea id="reason" name="reason" rows="4" required></textarea>
      </div>
      <div class="modal-actions">
        <button type="button" data-close-reject-modal class="btn o">Annuler</button>
        <button type="submit" class="btn">Rejeter</button>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
const rejectModal = document.getElementById('rejectModal');
const rejectForm = document.getElementById('rejectForm');

document.querySelectorAll('[data-reject-agency]').forEach((button) => {
  button.addEventListener('click', () => {
    document.getElementById('rejectAgencyId').value = button.dataset.rejectAgency;
    rejectForm.action = button.dataset.rejectUrl;
    rejectModal.hidden = false;
    document.getElementById('reason').focus();
  });
});

document.querySelectorAll('[data-close-reject-modal]').forEach((button) => {
  button.addEventListener('click', () => {
    rejectModal.hidden = true;
  });
});
</script>
@endpush
@endsection
