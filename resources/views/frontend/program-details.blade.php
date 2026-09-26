@extends('layouts.app')

@section('content')
    <div class="page-head">
        <div>
            <h1>{{ $program->program_name }}</h1>
            <p class="lede">{{ $program->field_of_study ?? 'Field not specified' }} · {{ $program->university->name ?? 'University not specified' }}</p>
        </div>
        <div style="display:flex; gap:0.75rem; flex-wrap:wrap;">
            <a href="{{ route('programs.index') }}" class="btn btn-ghost btn-sm">Back to search</a>
            <form method="POST" action="{{ route('saved-programs.store', $program) }}">
                @csrf
                <button type="submit" class="btn btn-ghost btn-sm">Save program</button>
            </form>
            <form method="POST" action="{{ route('applications.store') }}">
                @csrf
                <input type="hidden" name="program_id" value="{{ $program->id }}">
                <button type="submit" class="btn btn-primary btn-sm">Start application</button>
            </form>
        </div>
    </div>

    <div class="panel" style="margin-top: 1.5rem;">
        <h2>Eligibility check</h2>
        <p><strong>{{ $eligibility['status_label'] }}</strong></p>
        @if (!empty($eligibility['reasons']))
            <ul>
                @foreach ($eligibility['reasons'] as $reason)
                    <li>{{ $reason }}</li>
                @endforeach
            </ul>
        @endif
    </div>

    <div class="panel" style="margin-top: 1.5rem;">
        <h2>Deadline status</h2>
        <p><strong>{{ $deadline['label'] }}</strong></p>
        @if ($deadline['date'])
            <p class="muted">Deadline: {{ $deadline['date']->format('Y-m-d') }}</p>
        @endif
    </div>

    <div class="panel">
        <div class="compare-table-wrap">
            <table class="compare-table">
                <tbody>
                    <tr>
                        <th>University</th>
                        <td>{{ $program->university->name ?? 'Not specified' }}</td>
                    </tr>
                    <tr>
                        <th>City</th>
                        <td>{{ $program->university->location ?? 'Not specified' }}</td>
                    </tr>
                    <tr>
                        <th>Study field</th>
                        <td>{{ $program->field_of_study ?? 'Not specified' }}</td>
                    </tr>
                    <tr>
                        <th>Tuition / year</th>
                        <td>{{ $program->tuition_fee_annual ? 'EUR ' . number_format($program->tuition_fee_annual) : 'Not specified' }}</td>
                    </tr>
                    <tr>
                        <th>Application fee</th>
                        <td>Not specified</td>
                    </tr>
                    <tr>
                        <th>Language requirement</th>
                        <td>{{ $program->language_proficiency_requirement ?? 'Not specified' }}</td>
                    </tr>
                    <tr>
                        <th>Academic requirement</th>
                        <td>{{ $program->minimum_gpa ? 'Minimum GPA: ' . number_format($program->minimum_gpa, 2) : 'Not specified' }}</td>
                    </tr>
                    <tr>
                        <th>Application deadline</th>
                        <td>{{ $program->application_deadline ?? 'Not specified' }}</td>
                    </tr>
                    <tr>
                        <th>Official source</th>
                        <td>
                            @if ($program->university && $program->university->website_url)
                                <a href="{{ $program->university->website_url }}" target="_blank" rel="noopener noreferrer">
                                    {{ $program->university->website_url }}
                                </a>
                            @else
                                Not specified
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection
