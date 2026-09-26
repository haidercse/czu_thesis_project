@extends('layouts.app')

@section('content')
    <div class="page-head">
        <div>
            <h1>Saved programs</h1>
            <p class="lede">Programs you want to revisit later.</p>
        </div>
        <a href="{{ route('programs.index') }}" class="btn btn-ghost">Browse programs</a>
    </div>

    @if ($savedPrograms->isEmpty())
        <div class="panel">
            <p class="muted">You have not saved any programs yet.</p>
        </div>
    @else
        <div class="recommendation-list">
            @foreach ($savedPrograms as $program)
                <article class="panel recommendation-card">
                    <div class="recommendation-card__header">
                        <div>
                            <p class="eyebrow">{{ $program->university?->name ?? 'University' }}</p>
                            <h2>{{ $program->program_name }}</h2>
                            <p class="muted">{{ $program->field_of_study }} · EUR {{ number_format($program->tuition_fee_annual, 0) }} / year</p>
                        </div>
                        <form method="POST" action="{{ route('saved-programs.destroy', $program) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-ghost btn-sm">Unsave</button>
                        </form>
                    </div>
                    <div class="recommendation-card__footer">
                        <span class="muted">Deadline {{ $program->application_deadline ?? 'No deadline recorded' }}</span>
                        <a href="{{ route('programs.show', $program) }}" class="btn btn-primary btn-sm">View program</a>
                    </div>
                </article>
            @endforeach
        </div>
    @endif
@endsection
