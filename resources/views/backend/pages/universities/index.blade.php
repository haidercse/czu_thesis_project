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
                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#universityModal" data-mode="create"><i class="ti-plus"></i> Add University</button>
                        </div>
                        <div class="market-status-table mt-4">
                            <div class="table-responsive">
                                <table class="dbkit-table" id="universitiesTable">
                                    <tr class="heading-td">
                                        <td>Name</td>
                                        <td>Location</td>
                                        <td>Programs</td>
                                        <td>Actions</td>
                                    </tr>
                                    @forelse ($universities as $u)
                                        <tr data-id="{{ $u->id }}">
                                            <td>{{ $u->name }}</td>
                                            <td>{{ $u->location }}</td>
                                            <td>{{ $u->programs_count }}</td>
                                            <td>
                                                <button type="button" class="btn btn-info btn-xs edit-university" data-id="{{ $u->id }}"><i class="ti-pencil"></i> Edit</button>
                                                <button type="button" class="btn btn-danger btn-xs delete-university" data-id="{{ $u->id }}"><i class="ti-trash"></i> Delete</button>
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

<div class="modal fade" id="universityModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="universityForm">
            @csrf
            <input type="hidden" name="_method" id="universityMethod" value="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="universityModalTitle">Add University</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="university_name">Name</label>
                        <input type="text" name="name" id="university_name" class="form-control" required>
                        <small class="text-danger d-block" data-error-for="name"></small>
                    </div>
                    <div class="form-group">
                        <label for="university_location">Location</label>
                        <input type="text" name="location" id="university_location" class="form-control" required>
                        <small class="text-danger d-block" data-error-for="location"></small>
                    </div>
                    <div class="form-group">
                        <label for="website_url">Website URL</label>
                        <input type="url" name="website_url" id="website_url" class="form-control">
                        <small class="text-danger d-block" data-error-for="website_url"></small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="universitySubmitBtn">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    const universityModal = $('#universityModal');
    const universityForm = $('#universityForm');
    const universityMethod = $('#universityMethod');
    const universitySubmitBtn = $('#universitySubmitBtn');

    $('#universityModal').on('hidden.bs.modal', function () {
        universityForm[0].reset();
        universityMethod.val('POST');
        universitySubmitBtn.text('Save');
        $('#universityModalTitle').text('Add University');
        $('[data-error-for]').text('');
    });

    $(document).on('click', '.edit-university', function () {
        const id = $(this).data('id');
        $.get('/admin/universities/' + id + '/edit', function (res) {
            const university = res.data;
            $('#university_name').val(university.name);
            $('#university_location').val(university.location);
            $('#website_url').val(university.website_url || '');
            universityMethod.val('PUT');
            $('#universityModalTitle').text('Edit University');
            universitySubmitBtn.text('Update');
            universityForm.attr('data-id', id);
            universityModal.modal('show');
        });
    });

    $(document).on('click', '.delete-university', function () {
        const id = $(this).data('id');
        if (!confirm('Delete this university?')) return;

        $.ajax({
            url: '/admin/universities/' + id,
            type: 'DELETE',
            success: function (res) {
                $('tr[data-id="' + id + '"]').remove();
                alert(res.message || 'University deleted.');
            },
            error: function (xhr) {
                alert(xhr.responseJSON?.message || 'Unable to delete university.');
            }
        });
    });

    universityForm.on('submit', function (e) {
        e.preventDefault();
        const id = universityForm.attr('data-id');
        const method = universityMethod.val();
        const url = id && method === 'PUT' ? '/admin/universities/' + id : '/admin/universities';
        const payload = universityForm.serialize();

        $('[data-error-for]').text('');
        $.ajax({
            url: url,
            type: method === 'PUT' ? 'POST' : 'POST',
            data: payload + '&_method=' + method,
            success: function (res) {
                universityModal.modal('hide');
                alert(res.message || 'University saved.');
                window.location.reload();
            },
            error: function (xhr) {
                const errors = xhr.responseJSON?.errors || {};
                Object.keys(errors).forEach(function (key) {
                    $('small[data-error-for="' + key + '"]').text(errors[key][0]);
                });
            }
        });
    });
</script>
@endpush
