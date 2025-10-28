@extends('layouts.app')

@section('title', 'Notification Details')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 fw-bold">Notification</h1>
            <p class="text-muted mb-0">{{ $notification->type }}</p>
        </div>
        <a href="{{ route('notifications.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to Notifications
        </a>
    </div>

    <!-- Notification Details -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Notification Details</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Type</label>
                                <div>
                                    <span class="badge bg-primary">{{ ucfirst(str_replace('_', ' ', $notification->type)) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Status</label>
                                <div>
                                    @if($notification->read_at)
                                        <span class="badge bg-success">Read</span>
                                    @else
                                        <span class="badge bg-warning">Unread</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Title</label>
                        <div class="h5">{{ $notification->title }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Message</label>
                        <div class="border rounded p-3 bg-light">
                            {!! nl2br(e($notification->message)) !!}
                        </div>
                    </div>

                    @if($notification->data)
                    <div class="mb-3">
                        <label class="form-label fw-bold">Additional Data</label>
                        <div class="border rounded p-3 bg-light">
                            <pre class="mb-0 small">{{ json_encode($notification->data, JSON_PRETTY_PRINT) }}</pre>
                        </div>
                    </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Created At</label>
                                <div>{{ $notification->created_at->format('M d, Y H:i') }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Read At</label>
                                <div>{{ $notification->read_at ? $notification->read_at->format('M d, Y H:i') : 'Not read yet' }}</div>
                            </div>
                        </div>
                    </div>

                    @if($notification->action_url)
                    <div class="mb-3">
                        <label class="form-label fw-bold">Action</label>
                        <div>
                            <a href="{{ $notification->action_url }}" class="btn btn-primary">
                                <i class="bi bi-arrow-right-circle me-2"></i>{{ $notification->action_text ?? 'Take Action' }}
                            </a>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Notification Stats -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Notification Info</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-12">
                            <div class="h4 mb-1 fw-bold text-primary">{{ $notification->type }}</div>
                            <small class="text-muted">Type</small>
                        </div>
                    </div>
                    <hr>
                    <div class="small text-muted">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Status:</span>
                            <span>{{ $notification->read_at ? 'Read' : 'Unread' }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Age:</span>
                            <span>{{ $notification->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if(!$notification->read_at)
                        <form action="{{ route('notifications.mark-as-read', $notification) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-outline-primary w-100">
                                <i class="bi bi-check-circle me-2"></i>Mark as Read
                            </button>
                        </form>
                        @endif

                        @if($notification->action_url)
                        <a href="{{ $notification->action_url }}" class="btn btn-primary">
                            <i class="bi bi-arrow-right-circle me-2"></i>{{ $notification->action_text ?? 'Take Action' }}
                        </a>
                        @endif

                        <a href="{{ route('notifications.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-list me-2"></i>View All Notifications
                        </a>

                        <form action="{{ route('notifications.destroy', $notification) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100"
                                    onclick="return confirm('Are you sure you want to delete this notification?')">
                                <i class="bi bi-trash me-2"></i>Delete Notification
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
