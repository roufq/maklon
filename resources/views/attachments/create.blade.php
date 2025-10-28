@extends('layouts.app')

@section('title', 'Upload File - Project Management')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 fw-bold">Upload File</h1>
            <p class="text-muted mb-0">Attach files to projects or tasks</p>
        </div>
        <a href="{{ route('attachments.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to Attachments
        </a>
    </div>

    <!-- Upload Form -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form action="{{ route('attachments.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label fw-bold">File Name (Optional)</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name') }}"
                                   placeholder="Leave blank to use original filename">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="file" class="form-label fw-bold">Select File <span class="text-danger">*</span></label>
                            <input type="file" class="form-control @error('file') is-invalid @enderror"
                                   id="file" name="file" required>
                            <div class="form-text">Maximum file size: 10MB</div>
                            @error('file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="attachable_type" class="form-label fw-bold">Attach To <span class="text-danger">*</span></label>
                                <select class="form-select @error('attachable_type') is-invalid @enderror"
                                        id="attachable_type" name="attachable_type" required>
                                    <option value="">Select Type</option>
                                    <option value="App\Models\Project" {{ old('attachable_type') == 'App\Models\Project' ? 'selected' : '' }}>Project</option>
                                    <option value="App\Models\Task" {{ old('attachable_type') == 'App\Models\Task' ? 'selected' : '' }}>Task</option>
                                </select>
                                @error('attachable_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="attachable_id" class="form-label fw-bold">Select Item <span class="text-danger">*</span></label>
                                <select class="form-select @error('attachable_id') is-invalid @enderror"
                                        id="attachable_id" name="attachable_id" required>
                                    <option value="">Select Item</option>
                                    <!-- Options will be populated by JavaScript -->
                                </select>
                                @error('attachable_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-upload me-2"></i>Upload File
                            </button>
                            <a href="{{ route('attachments.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">File Upload Guidelines</h5>
                </div>
                <div class="card-body">
                    <h6>Supported File Types</h6>
                    <ul class="small mb-3">
                        <li><strong>Documents:</strong> PDF, DOC, DOCX, TXT</li>
                        <li><strong>Images:</strong> JPG, PNG, GIF, SVG</li>
                        <li><strong>Spreadsheets:</strong> XLS, XLSX, CSV</li>
                        <li><strong>Presentations:</strong> PPT, PPTX</li>
                        <li><strong>Archives:</strong> ZIP, RAR</li>
                    </ul>

                    <h6>File Size Limits</h6>
                    <ul class="small mb-3">
                        <li>Maximum file size: 10MB</li>
                        <li>Large files may take longer to upload</li>
                        <li>Consider compressing large files</li>
                    </ul>

                    <h6>Naming Conventions</h6>
                    <ul class="small mb-0">
                        <li>Use descriptive filenames</li>
                        <li>Include version numbers if applicable</li>
                        <li>Avoid special characters in filenames</li>
                        <li>Use underscores or hyphens instead of spaces</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const attachableTypeSelect = document.getElementById('attachable_type');
    const attachableIdSelect = document.getElementById('attachable_id');

    const projects = @json($projects ?? []);
    const tasks = @json($tasks ?? []);

    function updateAttachableOptions() {
        const selectedType = attachableTypeSelect.value;
        attachableIdSelect.innerHTML = '<option value="">Select Item</option>';

        let options = [];
        if (selectedType === 'App\\Models\\Project') {
            options = projects;
        } else if (selectedType === 'App\\Models\\Task') {
            options = tasks;
        }

        options.forEach(item => {
            const option = document.createElement('option');
            option.value = item.id;
            option.textContent = item.name || item.title;
            attachableIdSelect.appendChild(option);
        });
    }

    attachableTypeSelect.addEventListener('change', updateAttachableOptions);
});
</script>
@endsection
