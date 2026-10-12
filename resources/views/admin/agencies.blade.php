@extends('layouts.app')

@section('title', 'Gestion des agences | KONEXA')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('content')
<div class="dashboard-container">
  <div class="dashboard-header">
    <h1>Gestion des agences</h1>
  </div>

  @include('admin.partials.feedback')

  <div class="table-container">
    <div class="table-header">
      <h2>Agences ({{ $agencies->total() }})</h2>
      <div class="filters">
        <form method="GET" style="display: flex; gap: 12px; align-items: center;">
          <select name="status" onchange="this.form.submit()">
            <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Tous les statuts</option>
            <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>En attente</option>
            <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Approuvées</option>
            <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Rejetées</option>
            <option value="suspended" {{ $status === 'suspended' ? 'selected' : '' }}>Suspendues</option>
          </select>
          <input type="text" name="search" value="{{ $search }}" placeholder="Rechercher..." style="padding: 8px 12px; border: 1.5px solid var(--l); border-radius: 8px;">
          <button type="submit" class="btn btn-sm">Filtrer</button>
        </form>
      </div>
    </div>
    <div class="table-scroll" tabindex="0" role="region" aria-label="Liste des agences">
    <table class="data-table">
      <thead>
        <tr>
          <th>Agence</th>
          <th>Email</th>
          <th>WhatsApp</th>
          <th>Statut</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($agencies as $agency)
        <tr>
          <td>
            <strong>{{ $agency->name }}</strong><br>
            <small>{{ $agency->address }}</small>
          </td>
          <td>{{ $agency->user?->email }}</td>
          <td>{{ $agency->whatsapp }}</td>
          <td>
            <span class="status-badge status-{{ $agency->status }}">
              {{ $agency->status === 'pending' ? 'En attente' : ($agency->status === 'approved' ? 'Approuvée' : ($agency->status === 'rejected' ? 'Rejetée' : 'Suspendue')) }}
            </span>
          </td>
          <td>
            <div class="actions">
              @if($agency->status === 'pending')
                <form method="POST" action="{{ route('admin.agencies.approve', $agency) }}" style="display: inline;">
                  @csrf
                  <button type="submit" class="btn btn-sm">Approuver</button>
                </form>
                <button type="button" data-reject-agency="{{ $agency->id }}" data-reject-url="{{ route('admin.agencies.reject', $agency) }}" class="btn o btn-sm">Rejeter</button>
              @elseif($agency->status === 'approved')
                <form method="POST" action="{{ route('admin.agencies.suspend', $agency) }}" style="display: inline;">
                  @csrf
                  <button type="submit" class="btn o btn-sm">Suspendre</button>
                </form>
              @elseif($agency->status === 'suspended')
                <form method="POST" action="{{ route('admin.agencies.reactivate', $agency) }}" style="display: inline;">
                  @csrf
                  <button type="submit" class="btn btn-sm">Réactiver</button>
                </form>
              @endif
              <form method="POST" action="{{ route('admin.agencies.delete', $agency) }}" style="display: inline;" onsubmit="return confirm('Supprimer cette agence ?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn o btn-sm" style="color: var(--r); border-color: var(--r);">Supprimer</button>
              </form>
              <a href="{{ route('admin.agencies.edit', $agency) }}" class="btn o btn-sm">Modifier</a>
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
    </div>
    {{ $agencies->links() }}
  </div>
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
