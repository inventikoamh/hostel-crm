@extends('layouts.app')

@section('title', 'Upload Document')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-6">
        <div>
            <h1 class="h3 mb-0" style="color: var(--text-primary);">Upload Document</h1>
            <p class="text-muted mb-0">{{ $tenantDocument->document_number }} - {{ $tenantDocument->document_type_display }}</p>
        </div>
        <a href="{{ route('admin.tenant-documents.show', $tenantDocument) }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Document
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card" style="background-color: var(--card-bg); border: 1px solid var(--border-color);">
                <div class="card-header" style="background-color: var(--card-header-bg); border-bottom: 1px solid var(--border-color);">
                    <h5 class="mb-0" style="color: var(--text-primary);">Upload Document File</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.tenant-documents.store-signed', $tenantDocument) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-4">
                            <label for="signed_form" class="form-label" style="color: var(--text-primary);">Document File <span class="text-danger">*</span></label>
                            <input type="file" name="signed_form" id="signed_form" class="form-control @error('signed_form') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png" required>
                            <div class="form-text">Supported formats: PDF, JPG, JPEG, PNG (Max size: 10MB)</div>
                            @error('signed_form')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="notes" class="form-label" style="color: var(--text-primary);">Additional Notes</label>
                            <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="3" placeholder="Add any additional notes about this document...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.tenant-documents.show', $tenantDocument) }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-upload mr-2"></i>
                                Upload Document
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Document Information -->
            <div class="card mb-4" style="background-color: var(--card-bg); border: 1px solid var(--border-color);">
                <div class="card-header" style="background-color: var(--card-header-bg); border-bottom: 1px solid var(--border-color);">
                    <h5 class="mb-0" style="color: var(--text-primary);">Document Information</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <span class="font-medium" style="color: var(--text-secondary);">Document Number:</span>
                        <span style="color: var(--text-primary);">{{ $tenantDocument->document_number }}</span>
                    </div>
                    <div class="mb-3">
                        <span class="font-medium" style="color: var(--text-secondary);">Document Type:</span>
                        <span style="color: var(--text-primary);">{{ $tenantDocument->document_type_display }}</span>
                    </div>
                    <div class="mb-3">
                        <span class="font-medium" style="color: var(--text-secondary);">Tenant:</span>
                        <span style="color: var(--text-primary);">{{ $tenantDocument->tenantProfile->user->name }}</span>
                    </div>
                    <div class="mb-3">
                        <span class="font-medium" style="color: var(--text-secondary);">Priority:</span>
                        <span class="badge bg-{{ $tenantDocument->priority_badge_color }}">{{ $tenantDocument->priority_display }}</span>
                    </div>
                    @if($tenantDocument->description)
                    <div class="mb-3">
                        <span class="font-medium" style="color: var(--text-secondary);">Description:</span>
                        <p style="color: var(--text-primary);" class="mt-1 small">{{ $tenantDocument->description }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Upload Guidelines -->
            <div class="card" style="background-color: var(--card-bg); border: 1px solid var(--border-color);">
                <div class="card-header" style="background-color: var(--card-header-bg); border-bottom: 1px solid var(--border-color);">
                    <h5 class="mb-0" style="color: var(--text-primary);">Upload Guidelines</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item border-0 px-0">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-file-pdf text-danger me-3 mt-1"></i>
                                <div>
                                    <h6 class="mb-1" style="color: var(--text-primary);">PDF Documents</h6>
                                    <small class="text-muted">Preferred format for contracts and official documents</small>
                                </div>
                            </div>
                        </div>
                        <div class="list-group-item border-0 px-0">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-image text-success me-3 mt-1"></i>
                                <div>
                                    <h6 class="mb-1" style="color: var(--text-primary);">Image Files</h6>
                                    <small class="text-muted">JPG, JPEG, PNG for scanned documents and photos</small>
                                </div>
                            </div>
                        </div>
                        <div class="list-group-item border-0 px-0">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-weight-hanging text-warning me-3 mt-1"></i>
                                <div>
                                    <h6 class="mb-1" style="color: var(--text-primary);">File Size</h6>
                                    <small class="text-muted">Maximum file size: 10MB</small>
                                </div>
                            </div>
                        </div>
                        <div class="list-group-item border-0 px-0">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-shield-alt text-info me-3 mt-1"></i>
                                <div>
                                    <h6 class="mb-1" style="color: var(--text-primary);">Security</h6>
                                    <small class="text-muted">Files are stored securely and encrypted</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// File upload preview
document.getElementById('signed_form').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const fileSize = (file.size / 1024 / 1024).toFixed(2);
        const fileName = file.name;

        // Show file info
        const fileInfo = document.createElement('div');
        fileInfo.className = 'mt-2 p-2 bg-light rounded';
        fileInfo.innerHTML = `
            <small class="text-muted">
                <i class="fas fa-file mr-1"></i>
                ${fileName} (${fileSize} MB)
            </small>
        `;

        // Remove previous file info
        const existingInfo = document.querySelector('.file-info');
        if (existingInfo) {
            existingInfo.remove();
        }

        fileInfo.className += ' file-info';
        e.target.parentNode.appendChild(fileInfo);
    }
});
</script>
@endsection
