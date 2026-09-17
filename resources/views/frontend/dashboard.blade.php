@extends('layouts.app')

@section('content')
    <div class="page-head">
        <div>
            <h1>Welcome back, {{ explode(' ', auth()->user()->name)[0] }}</h1>
            <p class="lede">Here's where your application stands.</p>
        </div>
        @if (!$application)
            <a href="{{ route('programs.index') }}" class="btn btn-gold">Find a program</a>
        @endif
    </div>

    <div class="stat-row">
        <div class="stat">
            <div class="num">{{ $progress }}%</div>
            <div class="label">Application progress</div>
        </div>
        <div class="stat">
            <div class="num">{{ $documentCount }}</div>
            <div class="label">Documents uploaded</div>
        </div>
        @if ($application)
            <div class="stat">
                <div class="num">
                    {{ \Carbon\Carbon::parse($application->program->application_deadline)->diffInDays(now()) }}</div>
                <div class="label">Days to deadline</div>
            </div>
        @endif
    </div>

    @if ($application)
        <div class="panel">
            <h2>Next step</h2>
            <p class="muted" style="margin-bottom:1.25rem">{{ $application->program->program_name }} —
                {{ $application->program->university->name }}</p>

            @if ($nextStep)
                <div class="step" style="border-bottom:none; padding-top:0">
                    <div class="step-num">{{ $nextStep->step->step_order }}</div>
                    <div>
                        <div class="step-title">{{ $nextStep->step->step_name }}</div>
                        <p class="step-desc">{{ $nextStep->step->step_description }}</p>
                    </div>
                </div>
            @else
                <p class="muted">All steps completed! 🎉</p>
            @endif

            <a href="{{ route('applications.index') }}" class="btn btn-primary btn-sm" style="margin-top:0.5rem">Open
                checklist</a>
        </div>
    @else
        <div class="panel">
            <h2>Get started</h2>
            <p class="muted">Search for a program to begin your personalized checklist.</p>
            <a href="{{ route('programs.index') }}" class="btn btn-primary btn-sm">Find a program</a>
        </div>
    @endif
@endsection
