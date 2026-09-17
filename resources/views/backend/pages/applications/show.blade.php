@extends('backend.layouts.master')

@section('title', 'Application Details')

@section('admin-content')
<div class="main-content">
    <div class="main-content-inner">
        <div class="row mt-5 mb-5">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-sm-flex justify-content-between align-items-center">
                            <h4 class="header-title mb-0">Application Details</h4>
                            <a href="{{ route('admin.applications.index') }}" class="btn btn-secondary btn-sm">Back</a>
                        </div>
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <p><strong>Student:</strong> {{ $application->user ? $application->user->name : 'Unknown user' }}</p>
                                <p><strong>Email:</strong> {{ $application->user ? $application->user->email : 'N/A' }}</p>
                                <p><strong>Status:</strong> {{ ucfirst(str_replace('_', ' ', $application->status)) }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Program:</strong> {{ $application->program ? $application->program->program_name : 'Unknown program' }}</p>
                                <p><strong>University:</strong> {{ $application->program && $application->program->university ? $application->program->university->name : 'Unknown university' }}</p>
                                <p><strong>Field:</strong> {{ $application->program ? $application->program->field_of_study : 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="progress mt-4" style="height: 20px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $application->progress_percentage }}%;" aria-valuenow="{{ $application->progress_percentage }}" aria-valuemin="0" aria-valuemax="100">
                                {{ $application->progress_percentage }}%
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">Application Steps</h4>
                        <div class="table-responsive mt-4">
                            <table class="dbkit-table">
                                <tr class="heading-td">
                                    <td>Step</td>
                                    <td>Status</td>
                                </tr>
                                @forelse ($application->steps as $userStep)
                                    <tr>
                                        <td>{{ $userStep->step ? $userStep->step->step_name : 'Unknown step' }}</td>
                                        <td>{{ ucfirst(str_replace('_', ' ', $userStep->status)) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2">No steps found.</td>
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
