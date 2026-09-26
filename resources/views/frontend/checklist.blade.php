@extends('layouts.app')

@section('content')
    @if (!$application)
        <div class="panel">
            <h2>No application yet</h2>
            <p class="muted">Search for a program and click "Start application" to begin.</p>
            <a href="{{ route('programs.index') }}" class="btn btn-primary btn-sm">Find a program</a>
        </div>
    @else
        @php
            $program = $application->program;
            $university = $program->university ?? null;
            $userCountry = auth()->user()->profile?->country_of_origin ?? null;
            $nextStep = $application->steps
                ->sortBy(fn($step) => $step->step->step_order ?? 0)
                ->first(function ($step) {
                    return $step->step && $step->status !== 'completed';
                });
            $uploadedTypes = $application->documents->pluck('document_type')->unique()->values();
            $missingItems = $application->steps
                ->filter(fn($step) => $step->step && $step->step->related_document_type)
                ->filter(function ($step) use ($uploadedTypes) {
                    return ! $uploadedTypes->contains($step->step->related_document_type);
                })
                ->map(fn($step) => $step->step->related_document_type)
                ->unique()
                ->values();
            $hasCountrySpecificSteps = \App\Models\ApplicationStep::whereNotNull('applicable_countries')->exists();
        @endphp

        <div class="page-head">
            <div>
                <h1>Application workspace</h1>
                <p class="lede">{{ $program->program_name }} · {{ $university?->name ?? 'University not specified' }}</p>
            </div>
            <span class="badge" style="align-self:flex-start;">{{ ucfirst($application->status ?? 'planning') }}</span>
        </div>

        <div class="stat-row" style="margin-bottom:1.5rem;">
            <div class="stat">
                <div class="num">{{ $application->progress_percentage }}%</div>
                <div class="label">Progress</div>
            </div>
            <div class="stat">
                <div class="num">{{ $application->documents->count() }}</div>
                <div class="label">Uploaded documents</div>
            </div>
            <div class="stat">
                <div class="num">{{ $deadline['label'] ?? 'No deadline recorded' }}</div>
                <div class="label">Deadline</div>
            </div>
        </div>

        <div class="panel" style="margin-bottom:1.5rem;">
            <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap;">
                <div>
                    <h2 style="margin:0 0 0.25rem;">Next action</h2>
                    @if ($nextStep && $nextStep->step)
                        <p class="muted" style="margin:0;">{{ $nextStep->step->step_name }}</p>
                    @else
                        <p class="muted" style="margin:0;">All steps completed. You are ready to submit.</p>
                    @endif
                </div>
                <div class="muted">
                    {{ $university?->name ?? 'University not specified' }} · {{ $program->field_of_study ?? 'Field not specified' }}
                </div>
            </div>
        </div>

        <div class="panel" style="margin-bottom:1.5rem;">
            <h2>Application readiness</h2>
            <p><strong>{{ $readiness['label'] }}</strong> · {{ $readiness['percentage'] }}%</p>
            @if (!empty($readiness['reasons']))
                <ul>
                    @foreach ($readiness['reasons'] as $reason)
                        <li>{{ $reason }}</li>
                    @endforeach
                </ul>
            @endif
        </div>

        @if ($hasCountrySpecificSteps)
            <div class="panel" style="margin-bottom:1.5rem;">
                <p class="muted" style="margin:0;">Only relevant checklist items are shown for your profile.</p>
            </div>
        @endif

        <div class="panel" style="margin-bottom:1.5rem;">
            <h2>Missing items</h2>
            @if ($missingItems->isEmpty())
                <p class="muted">No required document items are currently missing.</p>
            @else
                <ul style="margin:0; padding-left:1.2rem;">
                    @foreach ($missingItems as $missingItem)
                        <li>{{ ucfirst(str_replace('_', ' ', $missingItem)) }}</li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div class="panel" style="margin-bottom:1.5rem;">
            <h2>Uploaded documents</h2>
            @if ($application->documents->isEmpty())
                <p class="muted">No documents uploaded yet.</p>
            @else
                <ul style="margin:0; padding-left:1.2rem;">
                    @foreach ($application->documents as $document)
                        <li>{{ ucfirst(str_replace('_', ' ', $document->document_type)) }} · {{ $document->original_filename }}</li>
                    @endforeach
                </ul>
            @endif
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
            <h2 style="margin:1rem 0 0.75rem;">Checklist</h2>
            @foreach ($application->steps as $s)
                @if ($s->step)
                    @php
                        $isRequired = $s->step->isRequiredForStudent($userCountry);
                    @endphp
                    <div class="step {{ $s->status === 'completed' ? 'is-complete' : '' }}">
                        <div class="step-num">
                            {{ $s->status === 'completed' ? '✓' : $s->step->step_order }}
                        </div>
                        <div>
                            <div style="display:flex; align-items:center; gap:0.5rem; flex-wrap:wrap;">
                                <div class="step-title" style="margin:0;">{{ $s->step->step_name }}</div>
                                <span class="badge" style="font-size:0.72rem; padding:0.2rem 0.5rem;">
                                    {{ $isRequired ? 'Required' : 'Optional' }}
                                </span>
                            </div>
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
