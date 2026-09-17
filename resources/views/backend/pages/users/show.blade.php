@extends('backend.layouts.master')

@section('title', 'User Details')

@section('admin-content')
<div class="main-content">
    <div class="main-content-inner">
        <div class="row mt-5 mb-5">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-sm-flex justify-content-between align-items-center">
                            <h4 class="header-title mb-0">User Details</h4>
                            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm">Back</a>
                        </div>
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <p><strong>Name:</strong> {{ $user->name }}</p>
                                <p><strong>Email:</strong> {{ $user->email }}</p>
                                <p><strong>Registered:</strong> {{ $user->created_at ? $user->created_at->format('Y-m-d') : 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Country:</strong> {{ $user->profile ? $user->profile->country_of_origin : 'N/A' }}</p>
                                <p><strong>Previous Degree:</strong> {{ $user->profile ? $user->profile->previous_degree : 'N/A' }}</p>
                                <p><strong>Previous Institution:</strong> {{ $user->profile ? $user->profile->previous_institution : 'N/A' }}</p>
                                <p><strong>Target Field:</strong> {{ $user->profile ? $user->profile->target_field : 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-body">
                        <h4 class="header-title">Applications</h4>
                        <div class="table-responsive mt-4">
                            <table class="dbkit-table">
                                <tr class="heading-td">
                                    <td>Program</td>
                                    <td>University</td>
                                    <td>Status</td>
                                    <td>Progress</td>
                                </tr>
                                @forelse ($user->applications as $application)
                                    <tr>
                                        <td>{{ $application->program ? $application->program->program_name : 'Unknown program' }}</td>
                                        <td>{{ $application->program && $application->program->university ? $application->program->university->name : 'Unknown university' }}</td>
                                        <td>{{ ucfirst(str_replace('_', ' ', $application->status)) }}</td>
                                        <td>{{ $application->progress_percentage }}%</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4">No applications found.</td>
                                    </tr>
                                @endforelse
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">Documents</h4>
                        <div class="table-responsive mt-4">
                            <table class="dbkit-table">
                                <tr class="heading-td">
                                    <td>Filename</td>
                                    <td>Type</td>
                                    <td>Uploaded</td>
                                </tr>
                                @forelse ($user->documents as $document)
                                    <tr>
                                        <td>{{ $document->original_filename }}</td>
                                        <td>{{ $document->document_type }}</td>
                                        <td>{{ $document->created_at ? $document->created_at->format('Y-m-d') : 'N/A' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3">No documents found.</td>
                                    </tr>
                                @endforelse
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
