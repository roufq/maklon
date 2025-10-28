@extends('layouts.app')
@section('title','Project Summary')
@section('content')
<div class="container-fluid">
  <h3 class="mb-3">Project: {{ $project->name }}</h3>
  <div class="row g-3">
    <div class="col-md-4">
      <div class="card border-0 shadow-sm"><div class="card-body">
        <div class="text-muted small">Tasks Completed</div>
        <div class="h4 mb-0">{{ $completedTasks }} / {{ $totalTasks }}</div>
      </div></div>
    </div>
    <div class="col-md-4">
      <div class="card border-0 shadow-sm"><div class="card-body">
        <div class="text-muted small">Recent Time (30d)</div>
        <div class="h4 mb-0">{{ $recentHours }} h</div>
      </div></div>
    </div>
    <div class="col-md-4">
      <div class="card border-0 shadow-sm"><div class="card-body">
        <div class="text-muted small">Budget</div>
        @if($budget)
          <div>Total: {{ $budget['currency'] }} {{ number_format($budget['total'],2) }}</div>
          <div>Spent: {{ $budget['currency'] }} {{ number_format($budget['spent'],2) }}</div>
          <div>Variance: {{ $budget['currency'] }} {{ number_format($budget['variance'],2) }}</div>
        @else
          <div class="text-muted">No budget data</div>
        @endif
      </div></div>
    </div>
  </div>
</div>
@endsection

