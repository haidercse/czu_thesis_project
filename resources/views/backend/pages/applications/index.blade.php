@extends('backend.layouts.master')

@section('title', 'Applications')

@section('admin-content')
<div class="main-content">
    <div class="main-content-inner">
        <div class="row mt-5 mb-5">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">Applications</h4>
                        <div class="market-status-table mt-4">
                            <div class="table-responsive">
                                <table class="dbkit-table">
                                    <tr class="heading-td">
                                        <td>Student</td>
                                        <td>Program</td>
                                        <td>University</td>
                                        <td>Status</td>
                                        <td>Progress</td>
                                        <td>Actions</td>
                                    </tr>
                                    @forelse ($applications as $a)
                                        <tr>
                                            <td>{{ $a->user ? $a->user->name : 'Unknown user' }}</td>
                                            <td>{{ $a->program ? $a->program->program_name : 'Unknown program' }}</td>
                                            <td>{{ $a->program && $a->program->university ? $a->program->university->name : 'Unknown university' }}</td>
                                            <td>{{ ucfirst(str_replace('_', ' ', $a->status)) }}</td>
                                            <td>{{ $a->progress_percentage }}%</td>
                                            <td>
                                                <a href="{{ route('admin.applications.show', $a) }}" class="btn btn-info btn-xs"><i class="ti-eye"></i> View</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6">No applications found.</td>
                                        </tr>
                                    @endforelse
                                </table>
                            </div>
                        </div>
                        <div class="mt-4">
                            {{ $applications->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
