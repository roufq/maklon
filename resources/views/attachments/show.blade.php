@extends('layouts.app')

@section('title', $attachment->name . ' - Attachment Details')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 fw-bold">{{ $attachment->name }}</h1>
            <p class="text-muted mb-0">{{ $attachment->type }} file</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('attachments.download', $attachment) }}" class="btn btn-primary">
                <i class="bi bi-download me-2"></i>Download
            </a>
            <a href="{{ route('attachments.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Attachments
            </a>
        </div>
    </div>

    <!-- Attachment Details -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">File Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">File Name</label>
                                <div>{{ $attachment->original_name }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">File Type</label>
                                <div>
                                    <span class="badge bg-primary">{{ ucfirst($attachment->type) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Size</label>
                                <div>{{ number_format($attachment->size / 1024, 2) }} KB</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">MIME Type</label>
                                <div class="text-muted small">{{ $attachment->mime_type }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Uploaded By</label>
                                <div>{{ $attachment->uploader->name }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Upload Date</label>
                                <div>{{ $attachment->created_at->format('M d, Y H:i') }}</div>
                            </div>
                        </div>
                    </div>

                    @if($attachment->attachable)
                    <div class="mb-3">
                        <label class="form-label fw-bold">Attached To</label>
                        <div>
                            @if($attachment->attachable_type === 'App\Models\Project')
                                <a href="{{ route('projects.show', $attachment->attachable) }}" class="text-decoration-none">
                                    <i class="bi bi-folder me-2"></i>{{ $attachment->attachable->name }}
                                </a>
                            @elseif($attachment->attachable_type === 'App\Models\Task')
                                <a href="{{ route('tasks.show', $attachment->attachable) }}" class="text-decoration-none">
                                    <i class="bi bi-check-circle me-2"></i>{{ $attachment->attachable->title }}
                                </a>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- File Preview -->
                    @if($attachment->type === 'image')
                    <div class="mb-3">
                        <label class="form-label fw-bold">Preview</label>
                        <div class="text-center">
                            <img src="{{ Storage::url($attachment->path) }}" alt="{{ $attachment->name }}" class="img-fluid rounded shadow-sm" style="max-height: 400px;">
                        </div>
                    </div>
                    @elseif($attachment->type === 'video')
                    <div class="mb-3">
                        <label class="form-label fw-bold">Preview</label>
                        <div class="text-center">
                            <video controls class="w-100 rounded shadow-sm" style="max-height: 400px;">
                                <source src="{{ Storage::url($attachment->path) }}" type="{{ $attachment->mime_type }}">
                                Your browser does not support the video tag.
                            </video>
                        </div>
                    </div>
                    @elseif($attachment->type === 'audio')
                    <div class="mb-3">
                        <label class="form-label fw-bold">Preview</label>
                        <div class="text-center">
                            <audio controls class="w-100">
                                <source src="{{ Storage::url($attachment->path) }}" type="{{ $attachment->mime_type }}">
                                Your browser does not support the audio element.
                            </audio>
                        </div>
                    </div>
                    @elseif($attachment->type === 'document' && $attachment->mime_type === 'application/pdf')
                    <div class="mb-3">
                        <label class="form-label fw-bold">Preview</label>
                        <div class="text-center">
                            <iframe src="{{ Storage::url($attachment->path) }}" width="100%" height="400" class="border rounded"></iframe>
                        </div>
                    </div>
                    @else
                    <div class="mb-3">
                        <label class="form-label fw-bold">File Icon</label>
                        <div class="text-center">
                            <i class="bi bi-file-earmark fs-1 text-muted"></i>
                            <p class="text-muted small mt-2">No preview available for this file type</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- File Stats -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">File Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="h4 mb-1 fw-bold text-primary">{{ number_format($attachment->size / 1024, 1) }}</div>
                            <small class="text-muted">KB</small>
                        </div>
                        <div class="col-6">
                            <div class="h4 mb-1 fw-bold text-success">{{ ucfirst($attachment->type) }}</div>
                            <small class="text-muted">Type</small>
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
                        <a href="{{ route('attachments.download', $attachment) }}" class="btn btn-primary">
                            <i class="bi bi-download me-2"></i>Download File
                        </a>
                        <a href="{{ route('attachments.create') }}" class="btn btn-outline-primary">
                            <i class="bi bi-plus-circle me-2"></i>Upload Another
                        </a>
                        <form action="{{ route('attachments.destroy', $attachment) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100"
                                    onclick="return confirm('Are you sure you want to delete this attachment?')">
                                <i class="bi bi-trash me-2"></i>Delete File
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
