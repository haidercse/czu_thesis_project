@extends('layouts.app')

@section('content')
    @if (!$application)
        <div class="panel">
            <h2>No application yet</h2>
            <p class="muted">Search for a program and click "Start application" to begin.</p>
            <a href="{{ route('programs.index') }}" class="btn btn-primary btn-sm">Find a program</a>
        </div>
    @else
        <div class="page-head">
            <div>
                <h1>{{ $application->program->program_name }}</h1>
                <p class="lede">{{ $application->program->university->name }} · Deadline
                    {{ $application->program->application_deadline }}</p>
            </div>
        </div>

        <div class="panel" style="margin-bottom:2rem">
            <div style="display:flex; justify-content:space-between; margin-bottom:0.6rem">
                <strong>Overall progress</strong>
                <span id="progressLabel" class="muted">{{ $application->progress_percentage }}% complete</span>
            </div>
            <div class="progress-track">
                <div class="progress-fill" id="progressFill" style="width:{{ $application->progress_percentage }}%"></div>
            </div>
        </div>

        <div class="panel" style="padding:0.25rem 1.75rem;">
            @foreach ($application->steps as $s)
                @if ($s->step)
                    {{-- only show valid steps --}}
                    <div class="step {{ $s->status === 'completed' ? 'is-complete' : '' }}">
                        <div class="step-num">
                            {{ $s->status === 'completed' ? '✓' : $s->step->step_order }}
                        </div>
                        <div>
                            <div class="step-title">{{ $s->step->step_name }}</div>
                            <p class="step-desc">{{ $s->step->step_description }}</p>
                        </div>
                        <div style="text-align:right">
                            <select class="step-status-select" data-step-id="{{ $s->id }}"
                                style="font-size:0.8rem;padding:0.3rem;">
                                <option value="not_started" {{ $s->status == 'not_started' ? 'selected' : '' }}>Not started
                                </option>
                                <option value="in_progress" {{ $s->status == 'in_progress' ? 'selected' : '' }}>In progress
                                </option>
                                <option value="completed" {{ $s->status == 'completed' ? 'selected' : '' }}>Completed
                                </option>
                            </select>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    @endif
@endsection

@push('scripts')
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('.step-status-select').on('change', function() {
            const stepId = $(this).data('step-id');
            $.ajax({
                url: '/steps/' + stepId,
                method: 'PATCH',
                data: {
                    status: $(this).val()
                },
                success: function(res) {
                    $('#progressFill').css('width', res.progress + '%');
                    $('#progressLabel').text(res.progress + '% complete');
                    location.reload();
                }
            });
        });
    </script>
@endpush
