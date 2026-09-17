@extends('backend.layouts.master')

@section('title', 'Users')

@section('admin-content')
<div class="main-content">
    <div class="main-content-inner">
        <div class="row mt-5 mb-5">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">Users</h4>
                        <div class="market-status-table mt-4">
                            <div class="table-responsive">
                                <table class="dbkit-table">
                                    <tr class="heading-td">
                                        <td>Name</td>
                                        <td>Email</td>
                                        <td>Country</td>
                                        <td>Applications</td>
                                        <td>Documents</td>
                                        <td>Registered</td>
                                        <td>Actions</td>
                                    </tr>
                                    @forelse ($users as $u)
                                        <tr>
                                            <td>{{ $u->name }}</td>
                                            <td>{{ $u->email }}</td>
                                            <td>{{ $u->profile ? $u->profile->country_of_origin : 'N/A' }}</td>
                                            <td>{{ $u->applications_count }}</td>
                                            <td>{{ $u->documents_count }}</td>
                                            <td>{{ $u->created_at ? $u->created_at->format('Y-m-d') : 'N/A' }}</td>
                                            <td>
                                                <a href="{{ route('admin.users.show', $u) }}" class="btn btn-info btn-xs"><i class="ti-eye"></i> View</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7">No users found.</td>
                                        </tr>
                                    @endforelse
                                </table>
                            </div>
                        </div>
                        <div class="mt-4">
                            {{ $users->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
