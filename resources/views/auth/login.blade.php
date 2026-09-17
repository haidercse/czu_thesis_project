<x-guest-layout>
<div class="auth-shell">
  <div class="auth-side">
    <div>
      <span class="brand-mark">Study Czechia Guide</span>
      <h1 style="margin-top:2.5rem">Everything you need to apply to a Czech university, in one place.</h1>
      <p>A free, step-by-step guide for international students applying to Czech universities.</p>
    </div>
    <div style="border-top:1px solid rgba(255,255,255,0.15); padding-top:1.25rem; font-size:0.85rem; color:#9BA2B5;">
      Built as part of a master's thesis at the Czech University of Life Sciences Prague.
    </div>
  </div>

  <div class="auth-form-wrap">
    <form class="auth-form" method="POST" action="{{ route('login') }}">
      @csrf
      <h2>Welcome back</h2>
      <p class="muted" style="margin-bottom:1.75rem">Sign in to continue your application.</p>

      @if ($errors->any())
        <div style="background:#F3E4DE;color:#9C4430;padding:0.7rem 1rem;border-radius:3px;margin-bottom:1rem;font-size:0.85rem;">
          {{ $errors->first() }}
        </div>
      @endif

      <div class="field">
        <label for="email">Email address</label>
        <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus placeholder="you@example.com" />
      </div>

      <div class="field">
        <label for="password">Password</label>
        <input type="password" name="password" id="password" required placeholder="••••••••" />
      </div>

      <button type="submit" class="btn btn-primary btn-block">Sign in</button>

      <p class="muted" style="text-align:center; margin-top:1.25rem; font-size:0.88rem">
        New here? <a href="{{ route('register') }}">Create an account</a>
      </p>
    </form>
  </div>
</div>
</x-guest-layout>