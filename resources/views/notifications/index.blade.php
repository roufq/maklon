@extends('layouts.app')

@section('title', 'Notifications - Project Management')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 fw-bold">Notifications</h1>
            <p class="text-muted mb-0">Stay updated with your project activities</p>
        </div>
        <button type="button" class="btn btn-outline-primary" onclick="markAllAsRead()">
            <i class="bi bi-check-all me-2"></i>Mark All as Read
        </button>
    </div>

    <!-- Notifications List -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            @forelse($notifications as $notification)
            <div class="notification-item border-bottom py-3 {{ $notification->is_read ? 'opacity-50' : '' }}">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center mb-2">
                            <h6 class="mb-0 me-2">{{ $notification->title }}</h6>
                            @if(!$notification->is_read)
                                <span class="badge bg-primary">New</span>
                            @endif
                        </div>
                        <p class="mb-2 text-muted small">{{ $notification->message }}</p>
                        <div class="d-flex align-items-center">
                            <small class="text-muted me-3">
                                <i class="bi bi-clock me-1"></i>{{ $notification->created_at->diffForHumans() }}
                            </small>
                            <small class="text-muted">
                                <i class="bi bi-tag me-1"></i>{{ ucfirst($notification->type) }}
                            </small>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        @if(!$notification->is_read)
                            <button type="button" class="btn btn-sm btn-outline-primary"
                                    onclick="markAsRead({{ $notification->id }})">
                                <i class="bi bi-check"></i>
                            </button>
                        @endif
                        <form action="{{ route('notifications.destroy', $notification) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                    onclick="return confirm('Are you sure you want to delete this notification?')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-5">
                <i class="bi bi-bell text-muted fs-1 mb-3"></i>
                <h6 class="text-muted">No Notifications</h6>
                <p class="text-muted small">You're all caught up! New notifications will appear here.</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Pagination -->
    @if($notifications->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $notifications->links() }}
    </div>
    @endif
</div>

<script>
function markAsRead(notificationId) {
    fetch(`{{ route('notifications.markAsRead', ':id') }}`.replace(':id', notificationId), {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => console.error('Error:', error));
}

function markAllAsRead() {
    fetch('{{ route('notifications.markAllAsRead') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => console.error('Error:', error));
}
</script>
@endsection
