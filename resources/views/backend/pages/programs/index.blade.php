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
                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#programModal" data-mode="create"><i class="ti-plus"></i> Add Program</button>
                        </div>
                        <div class="market-status-table mt-4">
                            <div class="table-responsive">
                                <table class="dbkit-table" id="programsTable">
                                    <tr class="heading-td">
                                        <td>Program</td>
                                        <td>University</td>
                                        <td>Field</td>
                                        <td>Tuition</td>
                                        <td>Deadline</td>
                                        <td>Actions</td>
                                    </tr>
                                    @forelse ($programs as $p)
                                        <tr data-id="{{ $p->id }}">
                                            <td>{{ $p->program_name }}</td>
                                            <td>{{ $p->university ? $p->university->name : 'Unknown university' }}</td>
                                            <td>{{ $p->field_of_study }}</td>
                                            <td>{{ number_format($p->tuition_fee_annual, 2) }}</td>
                                            <td>{{ $p->application_deadline }}</td>
                                            <td>
                                                <button type="button" class="btn btn-info btn-xs edit-program" data-id="{{ $p->id }}"><i class="ti-pencil"></i> Edit</button>
                                                <button type="button" class="btn btn-danger btn-xs delete-program" data-id="{{ $p->id }}"><i class="ti-trash"></i> Delete</button>
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

<div class="modal fade" id="programModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="programForm">
            @csrf
            <input type="hidden" name="_method" id="programMethod" value="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="programModalTitle">Add Program</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="program_university_id">University</label>
                        <select name="university_id" id="program_university_id" class="form-control" required>
                            <option value="">Select university</option>
                            @foreach ($universities as $university)
                                <option value="{{ $university->id }}">{{ $university->name }}</option>
                            @endforeach
                        </select>
                        <small class="text-danger d-block" data-error-for="university_id"></small>
                    </div>
                    <div class="form-group">
                        <label for="program_name">Program Name</label>
                        <input type="text" name="program_name" id="program_name" class="form-control" required>
                        <small class="text-danger d-block" data-error-for="program_name"></small>
                    </div>
                    <div class="form-group">
                        <label for="field_of_study">Field of Study</label>
                        <input type="text" name="field_of_study" id="field_of_study" class="form-control" required>
                        <small class="text-danger d-block" data-error-for="field_of_study"></small>
                    </div>
                    <div class="form-group">
                        <label for="tuition_fee_annual">Annual Tuition Fee</label>
                        <input type="number" step="0.01" min="0" name="tuition_fee_annual" id="tuition_fee_annual" class="form-control" required>
                        <small class="text-danger d-block" data-error-for="tuition_fee_annual"></small>
                    </div>
                    <div class="form-group">
                        <label for="application_deadline">Application Deadline</label>
                        <input type="date" name="application_deadline" id="application_deadline" class="form-control" required>
                        <small class="text-danger d-block" data-error-for="application_deadline"></small>
                    </div>
                    <div class="form-group">
                        <label for="language_proficiency_requirement">Language Proficiency Requirement</label>
                        <input type="text" name="language_proficiency_requirement" id="language_proficiency_requirement" class="form-control">
                        <small class="text-danger d-block" data-error-for="language_proficiency_requirement"></small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="programSubmitBtn">Save</button>
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

    const programModal = $('#programModal');
    const programForm = $('#programForm');
    const programMethod = $('#programMethod');
    const programSubmitBtn = $('#programSubmitBtn');

    $('#programModal').on('hidden.bs.modal', function () {
        programForm[0].reset();
        programMethod.val('POST');
        programSubmitBtn.text('Save');
        $('#programModalTitle').text('Add Program');
        $('[data-error-for]').text('');
    });

    $(document).on('click', '.edit-program', function () {
        const id = $(this).data('id');
        $.get('/admin/programs/' + id + '/edit', function (res) {
            const program = res.data;
            $('#program_university_id').val(program.university_id);
            $('#program_name').val(program.program_name);
            $('#field_of_study').val(program.field_of_study);
            $('#tuition_fee_annual').val(program.tuition_fee_annual);
            $('#application_deadline').val(program.application_deadline);
            $('#language_proficiency_requirement').val(program.language_proficiency_requirement || '');
            programMethod.val('PUT');
            $('#programModalTitle').text('Edit Program');
            programSubmitBtn.text('Update');
            programForm.attr('data-id', id);
            programModal.modal('show');
        });
    });

    $(document).on('click', '.delete-program', function () {
        const id = $(this).data('id');
        if (!confirm('Delete this program?')) return;

        $.ajax({
            url: '/admin/programs/' + id,
            type: 'DELETE',
            success: function (res) {
                $('tr[data-id="' + id + '"]').remove();
                alert(res.message || 'Program deleted.');
            },
            error: function (xhr) {
                alert(xhr.responseJSON?.message || 'Unable to delete program.');
            }
        });
    });

    programForm.on('submit', function (e) {
        e.preventDefault();
        const id = programForm.attr('data-id');
        const method = programMethod.val();
        const url = id && method === 'PUT' ? '/admin/programs/' + id : '/admin/programs';

        $('[data-error-for]').text('');
        $.ajax({
            url: url,
            type: 'POST',
            data: programForm.serialize() + '&_method=' + method,
            success: function (res) {
                programModal.modal('hide');
                alert(res.message || 'Program saved.');
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
