@extends('layouts.app')

@section('title', 'Tableau de bord Admin | AB-Flash')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('content')
<div class="dashboard-container">
  <div class="dashboard-header">
    <h1>Tableau de bord</h1>
  </div>

  <div class="stats-grid">
    <div class="stat-card">
      <div class="number">{{ $stats['total_agencies'] }}</div>
      <div class="label">Agences totales</div>
    </div>
    <div class="stat-card">
      <div class="number">{{ $stats['pending_agencies'] }}</div>
      <div class="label">En attente</div>
    </div>
    <div class="stat-card">
      <div class="number">{{ $stats['approved_agencies'] }}</div>
      <div class="label">Approuvées</div>
    </div>
    <div class="stat-card">
      <div class="number">{{ $stats['upcoming_flights'] }}</div>
      <div class="label">Vols à venir</div>
    </div>
    <div class="stat-card">
      <div class="number">{{ $stats['total_users'] }}</div>
      <div class="label">Utilisateurs</div>
    </div>
  </div>

  @if($pendingAgencies->count() > 0)
  <div class="table-container">
    <div class="table-header">
      <h2>Demandes en attente ({{ $pendingAgencies->count() }})</h2>
      <a href="{{ route('admin.agencies', ['status' => 'pending']) }}" class="btn btn-sm">Voir tout</a>
    </div>
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
              <button onclick="showRejectModal({{ $agency->id }}, '{{ $agency->name }}')" class="btn o btn-sm">Rejeter</button>
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  @endif

  <div class="table-container">
    <div class="table-header">
      <h2>Derniers inscrits</h2>
    </div>
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

  <div class="table-container">
    <div class="table-header">
      <h2>Activité récente</h2>
      <a href="{{ route('admin.activity') }}" class="btn btn-sm">Voir tout</a>
    </div>
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

<!-- Reject Modal -->
<div id="rejectModal" class="modal-overlay" style="display: none;">
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
        <button type="button" onclick="hideRejectModal()" class="btn o">Annuler</button>
        <button type="submit" class="btn">Rejeter</button>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
function showRejectModal(id, name) {
  document.getElementById('rejectAgencyId').value = id;
  document.getElementById('rejectForm').action = '{{ route('admin.agencies.reject', ':id') }}'.replace(':id', id);
  document.getElementById('rejectModal').style.display = 'flex';
}

function hideRejectModal() {
  document.getElementById('rejectModal').style.display = 'none';
}
</script>
@endpush
@endsection
