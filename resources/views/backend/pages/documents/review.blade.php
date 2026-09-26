@extends('backend.layouts.master')

@section('title', 'Document Review')

@section('admin-content')
<div class="main-content">
    <div class="main-content-inner">
        <div class="row mt-5 mb-5">
            <div class="col-12">
                @include('backend.layouts.partials.message')
                <div class="card">
                    <div class="card-body">
                        <div class="d-sm-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="header-title mb-1">Document Review</h4>
                                <p class="text-muted mb-0">Review uploaded student documents and leave clear instructions when changes are needed.</p>
                            </div>
                        </div>

                        <div class="table-responsive mt-4">
                            <table class="dbkit-table" id="dataTable">
                                <thead>
                                    <tr class="heading-td">
                                        <th>Student</th>
                                        <th>Application</th>
                                        <th>Document</th>
                                        <th>Status</th>
                                        <th>Review</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($documents as $document)
                                        <tr>
                                            <td>
                                                <strong>{{ $document->user->name ?? 'Unknown student' }}</strong><br>
                                                <small>{{ $document->user->email ?? 'No email' }}</small>
                                            </td>
                                            <td>
                                                {{ $document->application->program->program_name ?? 'No application' }}<br>
                                                <small>{{ $document->application->program->university->name ?? 'No university' }}</small>
                                            </td>
                                            <td>
                                                <div>{{ $document->original_filename }}</div>
                                                <small>{{ ucfirst(str_replace('_', ' ', $document->document_type)) }} - {{ round($document->file_size / 1024, 1) }} KB</small>
                                                <div class="mt-2">
                                                    <a href="{{ route('documents.view', $document) }}" target="_blank" rel="noopener noreferrer" class="btn btn-info btn-xs"><i class="ti-eye"></i> View</a>
                                                    <a href="{{ route('documents.download', $document) }}" class="btn btn-secondary btn-xs"><i class="ti-download"></i> Download</a>
                                                </div>
                                            </td>
                                            <td>{{ ucfirst(str_replace('_', ' ', $document->review_status ?: 'uploaded')) }}</td>
                                            <td style="min-width:280px">
                                                <form method="POST" action="{{ route('admin.documents.review', $document) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <div class="form-group mb-2">
                                                        <select name="review_status" class="form-control form-control-sm" required>
                                                            @foreach (['uploaded', 'in_review', 'approved', 'rejected', 'resubmission'] as $status)
                                                                <option value="{{ $status }}" {{ ($document->review_status ?: 'uploaded') === $status ? 'selected' : '' }}>
                                                                    {{ ucfirst(str_replace('_', ' ', $status)) }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="form-group mb-2">
                                                        <textarea name="review_comment" class="form-control form-control-sm" rows="2" minlength="10" required placeholder="Review comment (required)">{{ $document->review_comment }}</textarea>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary btn-xs"><i class="ti-save"></i> Save review</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5">No documents are waiting for review.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $documents->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection