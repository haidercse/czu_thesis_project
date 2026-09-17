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
                                <th>{{ $program->program_name }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th>University</th>
                            @foreach ($programs as $program)
                                <td>{{ $program->university->name }}<br><span class="muted">{{ $program->university->location }}</span></td>
                            @endforeach
                        </tr>
                        <tr>
                            <th>Tuition</th>
                            @foreach ($programs as $program)
                                <td>EUR {{ number_format($program->tuition_fee_annual) }} / year</td>
                            @endforeach
                        </tr>
                        <tr>
                            <th>Deadline</th>
                            @foreach ($programs as $program)
                                <td>{{ $program->application_deadline }}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <th>Field</th>
                            @foreach ($programs as $program)
                                <td>{{ $program->field_of_study }}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <th>Language requirement</th>
                            @foreach ($programs as $program)
                                <td>{{ $program->language_proficiency_requirement ?? 'Not specified' }}</td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
