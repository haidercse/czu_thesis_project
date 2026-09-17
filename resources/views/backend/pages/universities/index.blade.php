@extends('backend.layouts.master')

@section('title', 'Universities')

@section('admin-content')
<div class="main-content">
    <div class="main-content-inner">
        <div class="row mt-5 mb-5">
            <div class="col-12">
                @include('backend.layouts.partials.message')
                <div class="card">
                    <div class="card-body">
                        <div class="d-sm-flex justify-content-between align-items-center">
                            <h4 class="header-title mb-0">Universities</h4>
                            <a href="{{ route('admin.universities.create') }}" class="btn btn-primary btn-sm"><i class="ti-plus"></i> Add University</a>
                        </div>
                        <div class="market-status-table mt-4">
                            <div class="table-responsive">
                                <table class="dbkit-table">
                                    <tr class="heading-td">
                                        <td>Name</td>
                                        <td>Location</td>
                                        <td>Programs</td>
                                        <td>Actions</td>
                                    </tr>
                                    @forelse ($universities as $u)
                                        <tr>
                                            <td>{{ $u->name }}</td>
                                            <td>{{ $u->location }}</td>
                                            <td>{{ $u->programs_count }}</td>
                                            <td>
                                                <a href="{{ route('admin.universities.edit', $u) }}" class="btn btn-info btn-xs"><i class="ti-pencil"></i> Edit</a>
                                                <form action="{{ route('admin.universities.destroy', $u) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-xs" onclick="return confirm('Delete this university? Related programs will also be deleted.')"><i class="ti-trash"></i> Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4">No universities found.</td>
                                        </tr>
                                    @endforelse
                                </table>
                            </div>
                        </div>
                        <div class="mt-4">
                            {{ $universities->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
