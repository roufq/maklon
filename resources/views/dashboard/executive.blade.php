@extends('layouts.app')

@section('title', 'Executive Dashboard')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 fw-bold mb-0">Executive Dashboard</h1>
        <span class="text-muted">Cached 5 minutes</span>
    </div>

    <div class="row g-3">
        @if(in_array('on_time_rate', $allowedWidgets))
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small mb-1">On-time Completion Rate</div>
                    <div class="h3 fw-bold text-primary">{{ $metrics['on_time_rate'] }}%</div>
                    <div class="small text-muted">Completed tasks finished by due date</div>
                </div>
            </div>
        </div>
        @endif

        @if(in_array('high_risks', $allowedWidgets))
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small mb-1">Active High Risks</div>
                    <div class="h3 fw-bold text-danger">{{ $metrics['high_risks'] }}</div>
                    <div class="small text-muted">High + Very High risk items</div>
                </div>
            </div>
        </div>
        @endif

        @if(in_array('avg_utilization', $allowedWidgets))
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small mb-1">Team Utilization (Avg)</div>
                    <div class="h3 fw-bold text-success">{{ $metrics['avg_utilization'] }}%</div>
                    <div class="small text-muted">7-day window vs 8h/day baseline</div>
                </div>
            </div>
        </div>
        @endif

        @if(in_array('top_variance', $allowedWidgets))
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small mb-1">Budget Variance Watch</div>
                    <div class="small">Top 5 (most negative)</div>
                    <ul class="list-unstyled mt-2 mb-0">
                        @forelse($metrics['top_variance'] as $row)
                        <li class="d-flex justify-content-between border-bottom py-1">
                            <span class="text-truncate me-2" title="{{ $row['project'] }}">{{ $row['project'] }}</span>
                            <span class="fw-semibold {{ $row['variance'] < 0 ? 'text-danger' : 'text-muted' }}">
                                {{ $row['currency'] }} {{ number_format($row['variance'], 2) }}
                            </span>
                        </li>
                        @empty
                        <li class="text-muted">No data</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

