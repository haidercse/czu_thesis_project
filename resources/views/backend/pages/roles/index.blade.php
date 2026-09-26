@extends('backend.layouts.master')

@section('title', 'Roles')

@section('admin-content')
<div class="main-content">
    <div class="main-content-inner">
        <div class="row mt-5 mb-5">
            <div class="col-12">
                @include('backend.layouts.partials.message')
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="header-title mb-0">Roles</h4>
                            <div>
                                <a href="{{ route('admin.permissions.index') }}" class="btn btn-secondary btn-sm">Permissions</a>
                                <a href="{{ route('admin.roles.create') }}" class="btn btn-primary btn-sm"><i class="ti-plus"></i> Add Role</a>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="dbkit-table">
                                <thead>
                                    <tr class="heading-td"><th>Name</th><th>Users</th><th>Permissions</th><th>Actions</th></tr>
                                </thead>
                                <tbody>
                                @forelse ($roles as $role)
                                    <tr>
                                        <td>{{ $role->name }}</td>
                                        <td>{{ $role->users_count }}</td>
                                        <td>{{ $role->permissions_count }}</td>
                                        <td>
                                            <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-info btn-xs"><i class="ti-pencil"></i> Edit</a>
                                            @if (!in_array($role->name, ['Super Admin', 'Admission Officer', 'Student'], true))
                                                <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this role?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-xs"><i class="ti-trash"></i> Delete</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4">No roles found.</td></tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">{{ $roles->links() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
