@extends('layouts.app')

@section('content')
    <div class="page-head">
        <div>
            <h1>Compare programs</h1>
            <p class="lede">Review selected programs side by side.</p>
        </div>
        <a href="{{ route('programs.index') }}" class="btn btn-ghost btn-sm">Back to search</a>
    </div>

    <div class="panel">
        @if ($programs->isEmpty())
            <p class="muted">No programs selected for comparison.</p>
        @else
            <div class="compare-table-wrap">
                <table class="compare-table">
                    <thead>
                        <tr>
                            <th>Program</th>
                            @foreach ($programs as $program)
                                <th>
                                    {{ $program->program_name }}
                                    <div style="margin-top:0.5rem;">
                                        <button type="button" class="btn btn-ghost btn-sm remove-compare" data-id="{{ $program->id }}">Remove</button>
                                    </div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th>University</th>
                            @foreach ($programs as $program)
                                <td>
                                    {{ $program->university->name ?? 'Not specified' }}
                                    @if ($program->university && $program->university->location)
                                        <br><span class="muted">{{ $program->university->location }}</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                        <tr>
                            <th>City</th>
                            @foreach ($programs as $program)
                                <td>{{ $program->university->location ?? 'Not specified' }}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <th>Field</th>
                            @foreach ($programs as $program)
                                <td>{{ $program->field_of_study ?? 'Not specified' }}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <th>Tuition</th>
                            @foreach ($programs as $program)
                                <td>{{ $program->tuition_fee_annual ? 'EUR ' . number_format($program->tuition_fee_annual) . ' / year' : 'Not specified' }}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <th>Application fee</th>
                            @foreach ($programs as $program)
                                <td>Not specified</td>
                            @endforeach
                        </tr>
                        <tr>
                            <th>Language requirement</th>
                            @foreach ($programs as $program)
                                <td>{{ $program->language_proficiency_requirement ?? 'Not specified' }}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <th>Minimum GPA</th>
                            @foreach ($programs as $program)
                                <td>{{ $program->minimum_gpa ? number_format($program->minimum_gpa, 2) : 'Not specified' }}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <th>Application deadline</th>
                            @foreach ($programs as $program)
                                <td>{{ $program->application_deadline ?? 'Not specified' }}</td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        const compareKey = 'compare_programs';

        function readIds() {
            try {
                return JSON.parse(sessionStorage.getItem(compareKey)) || [];
            } catch (e) {
                return [];
            }
        }

        function writeIds(ids) {
            sessionStorage.setItem(compareKey, JSON.stringify(ids));
        }

        $('.remove-compare').on('click', function () {
            const id = Number($(this).data('id'));
            const nextIds = readIds().filter(item => item !== id);
            writeIds(nextIds);
            window.location.reload();
        });
    </script>
@endpush
