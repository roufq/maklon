@extends('layouts.app')

@section('title', 'Attachments - Project Management')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 fw-bold">Attachments</h1>
            <p class="text-muted mb-0">Manage files and documents</p>
        </div>
        @can('attachments.create')
        <a href="{{ route('attachments.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>Upload File
        </a>
        @endcan
    </div>

    <!-- Attachments Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Size</th>
                            <th>Uploaded By</th>
                            <th>Attached To</th>
                            <th>Upload Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attachments as $attachment)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-{{ $attachment->getFileIcon() }} me-2 text-primary"></i>
                                    <div>
                                        <div class="fw-bold">{{ $attachment->name }}</div>
                                        <small class="text-muted">{{ $attachment->original_name }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-{{ $attachment->getTypeColor() }}">
                                    {{ ucfirst($attachment->type) }}
                                </span>
                            </td>
                            <td>{{ $attachment->getFormattedSize() }}</td>
                            <td>{{ $attachment->uploader->name }}</td>
                            <td>
                                <small class="text-muted">
                                    {{ class_basename($attachment->attachable_type) }}: {{ $attachment->attachable->name ?? $attachment->attachable->title ?? 'Unknown' }}
                                </small>
                            </td>
                            <td>
                                <small class="text-muted">
                                    {{ $attachment->created_at->format('M d, Y') }}
                                </small>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('attachments.download', $attachment) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-download"></i>
                                    </a>
                                    <a href="{{ route('attachments.show', $attachment) }}" class="btn btn-sm btn-outline-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <form action="{{ route('attachments.destroy', $attachment) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('Are you sure you want to delete this attachment?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <i class="bi bi-file-earmark text-muted fs-1 mb-3"></i>
                                <h6 class="text-muted">No Attachments Yet</h6>
                                <p class="text-muted small">Upload your first file to get started.</p>
                                @can('attachments.create')
                                <a href="{{ route('attachments.create') }}" class="btn btn-primary">Upload File</a>
                                @endcan
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    @if($attachments->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $attachments->links() }}
    </div>
    @endif
</div>
@endsection
