<!DOCTYPE html>
<!-- Laravel port: resources/views/auth/register.blade.php -->
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Create account — Study Czechia Guide</title>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@500;600&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="css/style.css" />
</head>
<body>

<div class="auth-shell">
  <div class="auth-side">
    <div>
      <span class="brand-mark">Study Czechia Guide</span>
      <h1 style="margin-top:2.5rem">Tell us a little about you, and we'll personalize every step.</h1>
      <p>Your country of origin determines which qualification recognition and visa steps apply to you — we'll take care of figuring that out.</p>
    </div>
    <div style="border-top:1px solid rgba(255,255,255,0.15); padding-top:1.25rem; font-size:0.85rem; color:#9BA2B5;">
      Free forever. No hidden fees, no agency commission.
    </div>
  </div>

  <div class="auth-form-wrap">
    <form class="auth-form" id="registerForm">
      <h2>Create your account</h2>
      <p class="muted" style="margin-bottom:1.75rem">Takes about two minutes.</p>

      <div class="field">
        <label for="name">Full name</label>
        <input type="text" id="name" required placeholder="Jane Doe" />
      </div>

      <div class="field">
        <label for="email">Email address</label>
        <input type="email" id="email" required placeholder="you@example.com" />
      </div>

      <div class="field">
        <label for="password">Password</label>
        <input type="password" id="password" required placeholder="At least 8 characters" />
      </div>

      <div class="field">
        <label for="country">Country of origin</label>
        <select id="country" required>
          <option value="">Select your country</option>
          <option>India</option>
          <option>Vietnam</option>
          <option>Nigeria</option>
          <option>Ukraine</option>
          <option>Kenya</option>
          <option>Turkey</option>
          <option>Other</option>
        </select>
        <div class="hint">This personalizes your application checklist (Section 7.5.1).</div>
      </div>

      <div class="field">
        <label for="field">Intended field of study</label>
        <select id="field">
          <option>Business & Economics</option>
          <option>Engineering</option>
          <option>Natural Sciences</option>
          <option>Social Sciences</option>
          <option>Humanities</option>
        </select>
      </div>

      <button type="submit" class="btn btn-primary btn-block">Create account</button>

      <p class="muted" style="text-align:center; margin-top:1.25rem; font-size:0.88rem">
        Already have an account? <a href="index.html">Sign in</a>
      </p>
    </form>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
  // Laravel port note: POST to /register — RegisteredUserController
  // creates the User + User_Profile rows described in Section 6.2.1
  $("#registerForm").on("submit", function (e) {
    e.preventDefault();
    window.location.href = "dashboard.html";
  });
</script>
</body>
</html>
