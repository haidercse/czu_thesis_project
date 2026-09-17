@extends('backend.layouts.master')

@section('title', 'Programs')

@section('admin-content')
<div class="main-content">
    <div class="main-content-inner">
        <div class="row mt-5 mb-5">
            <div class="col-12">
                @include('backend.layouts.partials.message')
                <div class="card">
                    <div class="card-body">
                        <div class="d-sm-flex justify-content-between align-items-center">
                            <h4 class="header-title mb-0">Programs</h4>
                            <a href="{{ route('admin.programs.create') }}" class="btn btn-primary btn-sm"><i class="ti-plus"></i> Add Program</a>
                        </div>
                        <div class="market-status-table mt-4">
                            <div class="table-responsive">
                                <table class="dbkit-table">
                                    <tr class="heading-td">
                                        <td>Program</td>
                                        <td>University</td>
                                        <td>Field</td>
                                        <td>Tuition</td>
                                        <td>Deadline</td>
                                        <td>Actions</td>
                                    </tr>
                                    @forelse ($programs as $p)
                                        <tr>
                                            <td>{{ $p->program_name }}</td>
                                            <td>{{ $p->university ? $p->university->name : 'Unknown university' }}</td>
                                            <td>{{ $p->field_of_study }}</td>
                                            <td>{{ number_format($p->tuition_fee_annual, 2) }}</td>
                                            <td>{{ $p->application_deadline }}</td>
                                            <td>
                                                <a href="{{ route('admin.programs.edit', $p) }}" class="btn btn-info btn-xs"><i class="ti-pencil"></i> Edit</a>
                                                <form action="{{ route('admin.programs.destroy', $p) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-xs" onclick="return confirm('Delete this program?')"><i class="ti-trash"></i> Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6">No programs found.</td>
                                        </tr>
                                    @endforelse
                                </table>
                            </div>
                        </div>
                        <div class="mt-4">
                            {{ $programs->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
