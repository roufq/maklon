@extends('layouts.app')

@section('title', 'Dashboard - Modern Bootstrap Admin')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 fw-bold">Dashboard</h1>
                    <p class="text-muted mb-0">Welcome back, {{ Auth::user()->name }}!</p>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <span class="badge bg-primary fs-6">{{ Auth::user()->getRoleNames()->first() ?? 'User' }}</span>
                    <div class="dropdown">
                        <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-gear me-2"></i>Actions
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#"><i class="bi bi-plus-circle me-2"></i>Create Project</a></li>
                            <li><a class="dropdown-item" href="#"><i class="bi bi-plus-square me-2"></i>Add Task</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#"><i class="bi bi-bar-chart me-2"></i>View Reports</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards Row -->
    <div class="row mb-4" x-data="statsCards">
        <!-- Total Projects -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-muted small mb-1">Total Projects</div>
                            <div class="h4 mb-0 fw-bold text-primary" x-text="formatNumber(projects)">{{ \App\Models\Project::count() }}</div>
                            <div class="text-success small">
                                <i class="bi bi-arrow-up me-1"></i>+12% from last month
                            </div>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                <i class="bi bi-folder-fill text-primary fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Tasks -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-muted small mb-1">Active Tasks</div>
                            <div class="h4 mb-0 fw-bold text-success" x-text="formatNumber(tasks)">{{ \App\Models\Task::where('status', 'in_progress')->count() }}</div>
                            <div class="text-success small">
                                <i class="bi bi-arrow-up me-1"></i>+8% from last week
                            </div>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 rounded-circle p-3">
                                <i class="bi bi-check-circle-fill text-success fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Team Members -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-muted small mb-1">Team Members</div>
                            <div class="h4 mb-0 fw-bold text-info" x-text="formatNumber(members)">{{ \App\Models\User::count() }}</div>
                            <div class="text-info small">
                                <i class="bi bi-arrow-up me-1"></i>+5% from last month
                            </div>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-opacity-10 rounded-circle p-3">
                                <i class="bi bi-people-fill text-info fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Completion Rate -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-muted small mb-1">Completion Rate</div>
                            <div class="h4 mb-0 fw-bold text-warning" x-text="completionRate + '%'">85%</div>
                            <div class="text-warning small">
                                <i class="bi bi-arrow-up me-1"></i>+3% from last week
                            </div>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                                <i class="bi bi-trophy-fill text-warning fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- Revenue Overview -->
        <div class="col-xl-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold">Revenue Overview</h5>
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                Last 30 days
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">Last 7 days</a></li>
                                <li><a class="dropdown-item" href="#">Last 30 days</a></li>
                                <li><a class="dropdown-item" href="#">Last 3 months</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Project Status -->
        <div class="col-xl-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">Project Status</h5>
                </div>
                <div class="card-body">
                    <canvas id="projectStatusChart" height="250"></canvas>
                    <div class="mt-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary rounded-circle me-2" style="width: 12px; height: 12px;"></div>
                                <span class="small">Active</span>
                            </div>
                            <span class="small fw-bold">{{ \App\Models\Project::where('status', 'active')->count() }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center">
                                <div class="bg-warning rounded-circle me-2" style="width: 12px; height: 12px;"></div>
                                <span class="small">Planning</span>
                            </div>
                            <span class="small fw-bold">{{ \App\Models\Project::where('status', 'planning')->count() }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center">
                                <div class="bg-success rounded-circle me-2" style="width: 12px; height: 12px;"></div>
                                <span class="small">Completed</span>
                            </div>
                            <span class="small fw-bold">{{ \App\Models\Project::where('status', 'completed')->count() }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="bg-danger rounded-circle me-2" style="width: 12px; height: 12px;"></div>
                                <span class="small">On Hold</span>
                            </div>
                            <span class="small fw-bold">{{ \App\Models\Project::where('status', 'on_hold')->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity & Tasks Row -->
    <div class="row">
        <!-- Recent Activity -->
        <div class="col-xl-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">Recent Activity</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @forelse(\App\Models\Task::latest()->take(5)->get() as $task)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">{{ $task->title }}</h6>
                                        <p class="text-muted small mb-0">Task {{ $task->status }} by {{ $task->created_by_user->name ?? 'Unknown' }}</p>
                                    </div>
                                    <small class="text-muted">{{ $task->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-4">
                            <i class="bi bi-graph-up text-muted fs-1 mb-3"></i>
                            <h6 class="text-muted">No Recent Activity</h6>
                            <p class="text-muted small">Your project activity will appear here once you start working on projects.</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Tasks -->
        <div class="col-xl-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold">Recent Tasks</h5>
                        <a href="#" class="btn btn-outline-primary btn-sm">View All</a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="border-0 ps-3">Task</th>
                                    <th class="border-0">Status</th>
                                    <th class="border-0">Priority</th>
                                    <th class="border-0 pe-3">Due Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse(\App\Models\Task::with('project')->latest()->take(5)->get() as $task)
                                <tr>
                                    <td class="ps-3">
                                        <div>
                                            <div class="fw-bold">{{ $task->title }}</div>
                                            <small class="text-muted">{{ $task->project->name ?? 'No Project' }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $task->status === 'completed' ? 'success' : ($task->status === 'in_progress' ? 'primary' : 'secondary') }} badge-sm">
                                            {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $task->priority === 'urgent' ? 'danger' : ($task->priority === 'high' ? 'warning' : 'info') }} badge-sm">
                                            {{ ucfirst($task->priority) }}
                                        </span>
                                    </td>
                                    <td class="pe-3">
                                        <small class="text-muted">{{ $task->due_date ? $task->due_date->format('M d') : 'No due date' }}</small>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4">
                                        <i class="bi bi-check-circle text-muted fs-1 mb-3"></i>
                                        <h6 class="text-muted">No Tasks Yet</h6>
                                        <p class="text-muted small">Create your first task to get started.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Stats Cards Alpine.js component
    Alpine.data('statsCards', () => ({
        projects: {{ \App\Models\Project::count() }},
        tasks: {{ \App\Models\Task::where('status', 'in_progress')->count() }},
        members: {{ \App\Models\User::count() }},
        completionRate: 85,

        formatNumber(num) {
            return num.toLocaleString();
        }
    }));

    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                label: 'Revenue',
                data: [12000, 19000, 15000, 25000, 22000, 30000, 28000, 35000, 32000, 40000, 38000, 45000],
                borderColor: '#6366f1',
                backgroundColor: 'rgba(99, 102, 241, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Project Status Chart
    const statusCtx = document.getElementById('projectStatusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Active', 'Planning', 'Completed', 'On Hold'],
            datasets: [{
                data: [
                    {{ \App\Models\Project::where('status', 'active')->count() }},
                    {{ \App\Models\Project::where('status', 'planning')->count() }},
                    {{ \App\Models\Project::where('status', 'completed')->count() }},
                    {{ \App\Models\Project::where('status', 'on_hold')->count() }}
                ],
                backgroundColor: ['#6366f1', '#f59e0b', '#10b981', '#ef4444'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
});
</script>
@endsection
