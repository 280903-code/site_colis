@extends('layouts.app')

@section('title', 'Journal d\'activité | AB-Flash')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('content')
<div class="dashboard-container">
  <div class="dashboard-header">
    <h1>Journal d'activité</h1>
  </div>

  <div class="table-container">
    <div class="table-header">
      <h2>Actions administrateur ({{ $activities->total() }})</h2>
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
        @foreach($activities as $activity)
        <tr>
          <td>{{ $activity->user->name }}</td>
          <td>{{ $activity->action }}</td>
          <td>{{ $activity->description }}</td>
          <td>{{ $activity->created_at->format('d/m/Y H:i:s') }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
    {{ $activities->links() }}
  </div>
</div>
@endsection
