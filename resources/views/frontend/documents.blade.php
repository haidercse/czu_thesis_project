@extends('layouts.app')

@section('content')
    <div class="page-head">
        <div>
            <h1>Documents</h1>
            <p class="lede">Upload and manage your application documents.</p>
        </div>
    </div>

    <div class="panel" style="margin-bottom:2rem">
        <div class="field" style="max-width:280px">
            <label for="docType">Document type</label>
            <select id="docType">
                <option value="transcript">Transcript</option>
                <option value="diploma">Diploma</option>
                <option value="passport">Passport</option>
                <option value="language_test">Language test</option>
                <option value="recommendation">Recommendation letter</option>
                <option value="other">Other</option>
            </select>
        </div>

        <div class="dropzone" id="dropzone">
            <p style="margin:0"><strong>Drag a file here, or click to browse</strong></p>
            <p class="muted" style="margin:0.3rem 0 0; font-size:0.82rem">PDF, JPG or PNG, up to 5MB</p>
            <input type="file" id="fileInput" accept=".pdf,.jpg,.jpeg,.png" style="display:none" />
        </div>
    </div>

    <h2 style="margin-bottom:1rem">Your files</h2>
    <div class="panel" style="padding:0.25rem 1.75rem;" id="docList">
        @forelse($documents as $d)
            <div class="doc-row" data-id="{{ $d->id }}">
                <span>File</span>
                <div>
                    <div style="font-weight:600">{{ $d->original_filename }}</div>
                    <div class="muted" style="font-size:0.8rem">{{ $d->document_type }} -
                        {{ round($d->file_size / 1024, 1) }} KB</div>
                    @if($d->review_status)
                        <div class="muted" style="font-size:0.8rem; margin-top:0.3rem;"><strong>{{ ucfirst(str_replace('_', ' ', $d->review_status)) }}</strong></div>
                        @if($d->review_comment)
                            <div class="muted" style="font-size:0.8rem">{{ $d->review_comment }}</div>
                        @endif
                    @endif
                </div>
                <a href="{{ route('documents.view', $d) }}" target="_blank" rel="noopener noreferrer" class="btn btn-ghost btn-sm">View</a>
                <a href="{{ route('documents.download', $d) }}" class="btn btn-ghost btn-sm">Download</a>
                <button class="btn btn-ghost btn-sm remove-doc" data-id="{{ $d->id }}">Remove</button>
            </div>
        @empty
            <p class="muted">No documents uploaded yet.</p>
        @endforelse
    </div>
@endsection

@push('scripts')
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        const $dropzone = $('#dropzone'),
            $fileInput = $('#fileInput');
        $dropzone.on('click', function(e) {
            if (e.target.id === 'fileInput') return;
            $fileInput.trigger('click');
        });
        $dropzone.on('dragover', e => {
            e.preventDefault();
            $dropzone.addClass('drag-over');
        });
        $dropzone.on('dragleave', () => $dropzone.removeClass('drag-over'));
        $dropzone.on('drop', function(e) {
            e.preventDefault();
            $dropzone.removeClass('drag-over');
            handleFile(e.originalEvent.dataTransfer.files[0]);
        });
        $fileInput.on('change', function() {
            handleFile(this.files[0]);
        });

        function handleFile(file) {
            if (!file) return;
            const formData = new FormData();
            formData.append('file', file);
            formData.append('document_type', $('#docType').val());

            $.ajax({
                url: '{{ route('documents.store') }}',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function() {
                    location.reload();
                },
                error: function(xhr) {
                    alert(xhr.responseJSON?.message || 'Upload failed. Check file type and size.');
                }
            });
        }

        $(document).on('click', '.remove-doc', function() {
            const id = $(this).data('id');
            if (!confirm('Remove this document?')) return;
            $.ajax({
                url: '/documents/' + id,
                method: 'DELETE',
                success: function() {
                    location.reload();
                }
            });
        });
    </script>
@endpush
