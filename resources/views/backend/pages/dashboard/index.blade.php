@extends('backend.layouts.master')

@section('title', 'Admin Dashboard')

@section('admin-content')
<div class="main-content">
    <div class="main-content-inner">
        <div class="sales-report-area mt-5 mb-5">
            <div class="row">
                <div class="col-md-3">
                    <div class="single-report mb-xs-30">
                        <div class="s-report-inner pr--20 pt--30 mb-3">
                            <div class="icon"><i class="ti-home"></i></div>
                            <div class="s-report-title d-flex justify-content-between">
                                <h4 class="header-title mb-0">Universities</h4>
                            </div>
                            <div class="d-flex justify-content-between pb-2">
                                <h2>{{ $stats['universities'] }}</h2>
                                <span>Total</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="single-report mb-xs-30">
                        <div class="s-report-inner pr--20 pt--30 mb-3">
                            <div class="icon"><i class="ti-book"></i></div>
                            <div class="s-report-title d-flex justify-content-between">
                                <h4 class="header-title mb-0">Programs</h4>
                            </div>
                            <div class="d-flex justify-content-between pb-2">
                                <h2>{{ $stats['programs'] }}</h2>
                                <span>Total</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="single-report mb-xs-30">
                        <div class="s-report-inner pr--20 pt--30 mb-3">
                            <div class="icon"><i class="ti-user"></i></div>
                            <div class="s-report-title d-flex justify-content-between">
                                <h4 class="header-title mb-0">Active Registered Students</h4>
                            </div>
                            <div class="d-flex justify-content-between pb-2">
                                <h2>{{ $stats['registered_students'] }}</h2>
                                <span>Total</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="single-report">
                        <div class="s-report-inner pr--20 pt--30 mb-3">
                            <div class="icon"><i class="ti-files"></i></div>
                            <div class="s-report-title d-flex justify-content-between">
                                <h4 class="header-title mb-0">Total Submitted Applications</h4>
                            </div>
                            <div class="d-flex justify-content-between pb-2">
                                <h2>{{ $stats['submitted_applications'] }}</h2>
                                <span>Total</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-5 mb-5">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">Recent Applications</h4>
                        <div class="market-status-table mt-4">
                            <div class="table-responsive">
                                <table class="dbkit-table">
                                    <tr class="heading-td">
                                        <td>Student</td>
                                        <td>Program</td>
                                        <td>University</td>
                                        <td>Status</td>
                                        <td>Progress</td>
                                    </tr>
                                    @forelse ($recentApplications as $app)
                                        <tr>
                                            <td>{{ $app->user ? $app->user->name : 'Unknown user' }}</td>
                                            <td>{{ $app->program ? $app->program->program_name : 'Unknown program' }}</td>
                                            <td>{{ $app->program && $app->program->university ? $app->program->university->name : 'Unknown university' }}</td>
                                            <td>{{ ucfirst(str_replace('_', ' ', $app->status)) }}</td>
                                            <td>{{ $app->progress_percentage }}%</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5">No recent applications found.</td>
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
</div>
@endsection
