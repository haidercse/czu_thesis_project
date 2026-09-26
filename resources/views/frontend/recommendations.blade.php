@extends('layouts.app')

@section('content')
    <div class="page-head">
        <div>
            <h1>Recommended programs</h1>
            <p class="lede">Programs matched to your study goals, budget and academic profile.</p>
        </div>
        <a href="{{ route('profile.edit') }}" class="btn btn-ghost">Update profile</a>
    </div>

    @if (!$profile)
        <div class="panel">
            <h2>Complete your profile first</h2>
            <p class="muted">Add your target field, GPA, language score and budget to get relevant recommendations.</p>
            <a href="{{ route('profile.edit') }}" class="btn btn-primary">Complete profile</a>
        </div>
    @elseif ($recommendations->isEmpty())
        <div class="panel">
            <h2>No programs available yet</h2>
            <p class="muted">Recommendations will appear when programs are added by the admissions team.</p>
        </div>
    @else
        <div class="recommendation-list">
            @foreach ($recommendations as $program)
                <article class="panel recommendation-card">
                    <div class="recommendation-card__header">
                        <div>
                            <p class="eyebrow">{{ $program->university->name ?? 'University' }}</p>
                            <h2>{{ $program->program_name }}</h2>
                            <p class="muted">{{ $program->field_of_study }} · EUR {{ number_format($program->tuition_fee_annual, 0) }} / year</p>
                        </div>
                        <strong class="recommendation-score">{{ $program->recommendation_score }}% match</strong>
                    </div>
                    @if (!empty($program->recommendation_reasons))
                        <ul class="recommendation-reasons">
                            @foreach ($program->recommendation_reasons as $reason)
                                <li>{{ $reason }}</li>
                            @endforeach
                        </ul>
                    @endif
                    <div class="recommendation-card__footer">
                        <span class="muted">Deadline {{ $program->application_deadline }}</span>
                        <form method="POST" action="{{ route('applications.store') }}">
                            @csrf
                            <input type="hidden" name="program_id" value="{{ $program->id }}">
                            <button type="submit" class="btn btn-primary btn-sm">Start application</button>
                        </form>
                    </div>
                </article>
            @endforeach
        </div>
    @endif
@endsection
