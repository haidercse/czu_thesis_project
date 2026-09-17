<x-guest-layout>
<div class="auth-shell">
  <div class="auth-side">
    <div>
      <span class="brand-mark">Study Czechia Guide</span>
      <h1 style="margin-top:2.5rem">Create your account</h1>
      <p>Get a personalized checklist for your Czech university application.</p>
    </div>
  </div>

  <div class="auth-form-wrap">
    <form class="auth-form" method="POST" action="{{ route('register') }}">
      @csrf

      @if ($errors->any())
        <div style="background:#F3E4DE;color:#9C4430;padding:0.7rem 1rem;border-radius:3px;margin-bottom:1rem;font-size:0.85rem;">
          <ul style="margin:0;padding-left:1.2rem;">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <h2>Create your account</h2>
      <p class="muted" style="margin-bottom:1.75rem">Takes about two minutes.</p>

      <div class="field">
        <label for="name">Full name</label>
        <input type="text" name="name" id="name" value="{{ old('name') }}" required placeholder="Jane Doe" />
      </div>

      <div class="field">
        <label for="email">Email address</label>
        <input type="email" name="email" id="email" value="{{ old('email') }}" required placeholder="you@example.com" />
      </div>

      <div class="field">
        <label for="password">Password</label>
        <input type="password" name="password" id="password" required placeholder="At least 8 characters" />
      </div>

      <div class="field">
        <label for="password_confirmation">Confirm password</label>
        <input type="password" name="password_confirmation" id="password_confirmation" required placeholder="Repeat password" />
      </div>

      <button type="submit" class="btn btn-primary btn-block">Create account</button>

      <p class="muted" style="text-align:center; margin-top:1.25rem; font-size:0.88rem">
        Already have an account? <a href="{{ route('login') }}">Sign in</a>
      </p>
    </form>
  </div>
</div>
</x-guest-layout>