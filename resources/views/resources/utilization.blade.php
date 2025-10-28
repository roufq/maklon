@extends('layouts.app')

@section('title', 'Resource Utilization Report')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Resource Utilization Report</h3>
                    <div class="card-tools">
                        <a href="{{ route('resources.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Allocations
                        </a>
                        <button onclick="window.print()" class="btn btn-info btn-sm">
                            <i class="fas fa-print"></i> Print Report
                        </button>
                    </div>
                </div>

                <!-- Date Range Filter -->
                <div class="card-body border-bottom">
                    <form method="GET" class="row g-3">
                        <div class="col-md-4">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $startDate }}">
                        </div>
                        <div class="col-md-4">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $endDate }}">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="exclude_weekends" name="exclude_weekends" value="1" {{ ($excludeWeekends ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label" for="exclude_weekends">Exclude weekends</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter"></i> Update Report
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h5>Utilization Overview ({{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }})</h5>
                        </div>
                        <div class="col-md-4 text-right">
                            @php
                                $wd = ($excludeWeekends ?? false) ? (function($s,$e){ $d=0; $cs=\Carbon\Carbon::parse($s); $ce=\Carbon\Carbon::parse($e); for($dt=$cs->copy(); $dt->lte($ce); $dt->addDay()){ if(!$dt->isWeekend()) $d++; } return $d; })($startDate,$endDate) : (\Carbon\Carbon::parse($startDate)->diffInDays(\Carbon\Carbon::parse($endDate)) + 1);
                            @endphp
                            <small class="text-muted">Working Days: {{ $wd }}</small>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Position</th>
                                    <th>Total Allocated Hours</th>
                                    <th>Utilization Rate</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($utilization as $userUtilization)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($userUtilization->user->avatar)
                                                    <img src="{{ asset('storage/' . $userUtilization->user->avatar) }}" class="img-circle elevation-2" alt="User Image" style="width: 30px; height: 30px; margin-right: 10px;">
                                                @else
                                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 30px; height: 30px; margin-right: 10px; font-size: 12px;">
                                                        {{ substr($userUtilization->user->name, 0, 1) }}
                                                    </div>
                                                @endif
                                                {{ $userUtilization->user->name }}
                                            </div>
                                        </td>
                                        <td>{{ $userUtilization->user->position ?? 'Not Set' }}</td>
                                        <td>{{ number_format($userUtilization->total_allocated_hours, 1) }}h</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="progress flex-grow-1 mr-2" style="height: 20px;">
                                                    <div class="progress-bar
                                                        @if($userUtilization->utilization_percentage > 100) bg-danger
                                                        @elseif($userUtilization->utilization_percentage > 80) bg-warning
                                                        @else bg-success
                                                        @endif"
                                                        style="width: {{ min($userUtilization->utilization_percentage, 100) }}%">
                                                    </div>
                                                </div>
                                                <span class="ml-2">{{ number_format($userUtilization->utilization_percentage, 1) }}%</span>
                                            </div>
                                        </td>
                                        <td>
                                            @if($userUtilization->utilization_percentage > 100)
                                                <span class="badge badge-danger">Over-allocated</span>
                                            @elseif($userUtilization->utilization_percentage > 80)
                                                <span class="badge badge-warning">High</span>
                                            @elseif($userUtilization->utilization_percentage > 60)
                                                <span class="badge badge-success">Optimal</span>
                                            @else
                                                <span class="badge badge-info">Under-utilized</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('resources.index', ['user_id' => $userUtilization->user_id, 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i> View Allocations
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">
                                            <i class="fas fa-chart-bar fa-2x mb-2"></i>
                                            <p>No utilization data found for the selected period.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($utilization->isNotEmpty())
                        <div class="row mt-4">
                            <div class="col-md-3">
                                <div class="small-box bg-success">
                                    <div class="inner">
                                        <h3>{{ $utilization->where('utilization_percentage', '<=', 80)->count() }}</h3>
                                        <p>Well Utilized (≤80%)</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="small-box bg-warning">
                                    <div class="inner">
                                        <h3>{{ $utilization->where('utilization_percentage', '>', 80)->where('utilization_percentage', '<=', 100)->count() }}</h3>
                                        <p>High Utilization (81-100%)</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-exclamation-triangle"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="small-box bg-danger">
                                    <div class="inner">
                                        <h3>{{ $utilization->where('utilization_percentage', '>', 100)->count() }}</h3>
                                        <p>Over-allocated (>100%)</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-times-circle"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="small-box bg-info">
                                    <div class="inner">
                                        <h3>{{ $utilization->where('utilization_percentage', '<', 60)->count() }}</h3>
                                        <p>Under-utilized (<60%)</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-info-circle"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Set minimum end date to start date
    $('#start_date').change(function() {
        $('#end_date').attr('min', $(this).val());
        if ($('#end_date').val() && $('#end_date').val() < $(this).val()) {
            $('#end_date').val($(this).val());
        }
    });

    // Initialize end date min
    $('#end_date').attr('min', $('#start_date').val());
});
</script>

<style>
@media print {
    .card-tools, .btn, form {
        display: none !important;
    }
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    .table-responsive {
        overflow: visible !important;
    }
}
</style>
@endsection
