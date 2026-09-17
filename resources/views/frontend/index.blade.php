<!DOCTYPE html>
<!-- Laravel port: resources/views/auth/login.blade.php -->
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Sign in — Study Czechia Guide</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@500;600&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="css/style.css" />
</head>
<body>

<div class="auth-shell">
  <div class="auth-side">
    <div>
      <span class="brand-mark">Study Czechia Guide</span>
      <h1 style="margin-top:2.5rem">Everything you need to apply to a Czech university, in one place.</h1>
      <p>A free, step-by-step guide built from real interviews with international students and admission officers — no agency fees required.</p>
    </div>
    <div style="border-top:1px solid rgba(255,255,255,0.15); padding-top:1.25rem; font-size:0.85rem; color:#9BA2B5;">
      Built as part of a master's thesis at the Czech University of Life Sciences Prague, Faculty of Economics and Management.
    </div>
  </div>

  <div class="auth-form-wrap">
    <form class="auth-form" id="loginForm">
      <h2>Welcome back</h2>
      <p class="muted" style="margin-bottom:1.75rem">Sign in to continue your application.</p>

      <div class="field">
        <label for="email">Email address</label>
        <input type="email" id="email" required placeholder="you@example.com" />
      </div>

      <div class="field">
        <label for="password">Password</label>
        <input type="password" id="password" required placeholder="••••••••" />
      </div>

      <button type="submit" class="btn btn-primary btn-block">Sign in</button>

      <p class="muted" style="text-align:center; margin-top:1.25rem; font-size:0.88rem">
        New here? <a href="register.html">Create an account</a>
      </p>
    </form>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
  // Laravel port note: this becomes a real POST to /login handled
  // by Laravel Breeze's AuthenticatedSessionController (Section 6.4.1)
  $("#loginForm").on("submit", function (e) {
    e.preventDefault();
    window.location.href = "dashboard.html";
  });
</script>
</body>
</html>
