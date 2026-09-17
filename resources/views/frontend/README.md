# Study Czechia Guide — Frontend Prototype

Static HTML/CSS/jQuery prototype of the system designed in Chapter 6 and
implemented in Chapter 7 of the thesis. Open `index.html` in a browser —
no build step or server needed. Everything runs client-side with mock
data in `js/data.js`.

## How to view it
Just double-click `index.html`, or in VS Code use the "Live Server"
extension for auto-reload while you edit.

## Pages
| File | Purpose | Becomes in Laravel (Blade view) |
|---|---|---|
| `index.html` | Sign in | `resources/views/auth/login.blade.php` |
| `register.html` | Sign up | `resources/views/auth/register.blade.php` |
| `dashboard.html` | Progress overview | `resources/views/dashboard.blade.php` |
| `programs.html` | Program search/filter | `resources/views/programs/search.blade.php` |
| `checklist.html` | Application checklist | `resources/views/applications/checklist.blade.php` |
| `documents.html` | Document upload | `resources/views/documents/index.blade.php` |

## Porting to Laravel — the general pattern

1. **Layout first.** Everything between `<div class="app-shell">` and
   its closing tag is repeated on every logged-in page. Turn it into
   `resources/views/layouts/app.blade.php` with `@yield('content')`
   where the page-specific `<main>` content goes, and pull the sidebar
   `<aside class="app-nav">` into `resources/views/layouts/navigation.blade.php`,
   included with `@include('layouts.navigation')`.

2. **CSS and JS move to `public/`** (or `resources/css` + `resources/js`
   if you wire up Vite, as described in Section 7.1). No changes needed
   to `style.css` itself.

3. **Mock data → real data.** Everything in `js/data.js` mirrors a
   database table from Section 6.2. Delete the arrays and instead have
   the Controller pass real Eloquent data into the view:

   ```php
   // ProgramController@search
   $programs = Program::with('university')->get();
   return view('programs.search', compact('programs'));
   ```

   Then in the Blade view, either render the initial list server-side
   with `@foreach ($programs as $p)` and drop `applyFilters()`'s
   dependency on the static array, or — to keep the exact jQuery
   filtering behaviour — return `$programs` as JSON from an Ajax
   endpoint and reuse `renderPrograms()` almost as-is (this is the
   approach described in Section 7.5.2).

4. **Ajax calls.** Every place in `main.js` with a comment like
   `// In Laravel this becomes...` shows exactly what to replace.
   Example, the checklist status update:

   ```js
   $.ajax({
     url: '/steps/' + stepId,
     method: 'PATCH',
     data: { status: newStatus },
     success: function (response) {
       $('#progressFill').css('width', response.progress + '%');
     }
   });
   ```

   Don't forget the CSRF meta tag + `$.ajaxSetup` block from
   Section 7.4.2 in your Laravel layout — without it every PATCH/POST
   will be rejected with a 419 error.

5. **Forms.** The login/register forms currently just redirect on
   submit. Replace the `<form id="loginForm">` JS handler with a plain
   HTML form posting to Laravel's Breeze routes (`/login`, `/register`)
   — you can delete the `e.preventDefault()` redirect logic entirely
   once Breeze is handling it.

## Suggested build order
1. `composer create-project laravel/laravel` + `laravel/breeze` (Section 7.1)
2. Get the layout + navigation shell working with Breeze's default auth views first
3. Migrations from Section 7.2 → seed with the same values in `data.js`
   so what you see matches this prototype
4. Port `dashboard.html` (simplest, mostly static)
5. Port `programs.html` (introduces Ajax filtering)
6. Port `checklist.html` and `documents.html` (introduces PATCH/POST + file upload)

Good luck with the build — shout if any specific page gets stuck.
