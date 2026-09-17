# CZU Thesis Project — Remaining Implementation Tasks

## Project Context (read this first)

This is a Laravel 11 application (`czu_thesis_project`) for international students applying
to Czech universities. Follow the existing conventions below exactly — do not introduce a
different architecture, CSS framework, or JS library.

**Stack:** Laravel 11, MySQL, Blade, jQuery 3.7 (via CDN, already loaded globally), plain CSS
(`public/css/style.css` — do not use Tailwind, Bootstrap, or any new framework).

**Existing structure:**
- Views live in `resources/views/frontend/*.blade.php` and extend `layouts.app`
  (`resources/views/layouts/app.blade.php`), which provides the sidebar nav, CSRF meta tag,
  and a `@stack('scripts')` before `</body>`.
- Every authenticated page uses `@extends('layouts.app')`, `@section('content') ... @endsection`,
  and optionally `@push('scripts') <script>...</script> @endpush` for page JS.
- All authenticated routes are grouped under `Route::middleware(['auth'])->group(...)` in
  `routes/web.php`.
- Ajax calls use jQuery with this CSRF setup pattern already used on other pages:
  ```js
  $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
  ```
- Controllers use explicit `belongsTo(Model::class, 'foreign_key_column')` — **do not rely on
  Laravel's default FK guessing**, it caused a bug before (see `UserApplicationStep::step()`).
- CSS classes already available and styled — reuse these, don't invent new ones:
  `.panel`, `.field` (+ `label`, `input`, `select`), `.btn .btn-primary/.btn-ghost/.btn-gold/.btn-sm`,
  `.page-head`, `.stat-row` / `.stat`, `.step` / `.step-num` / `.step-title` / `.step-desc`,
  `.badge .badge-pending/.badge-progress/.badge-complete`, `.doc-row`, `.program-row`,
  `.progress-track` / `.progress-fill`, `.filter-bar`.

**Existing models (do not recreate, only modify where instructed):**
- `User` — has `applications()`, `documents()` relationships already.
- `UserProfile` — **does NOT exist yet**, needs creating (Task 1).
- `University hasMany Program`.
- `Program belongsTo University`.
- `Application belongsTo User, belongsTo Program, hasMany UserApplicationStep (via steps())`,
  has `getProgressPercentageAttribute()`.
- `ApplicationStep` — fields: `step_name`, `step_description`, `step_order`.
  **Does NOT yet have `applicable_countries` column** (needed for Task 3).
- `UserApplicationStep belongsTo Application, belongsTo ApplicationStep (via step(), explicit FK 'application_step_id')`.
- `Document belongsTo User`. Has `application_id` (nullable FK), `document_type`,
  `original_filename`, `stored_filename`, `file_path`, `file_size`.

**Existing controllers:** `AdminController`, `ProgramController` (`index`, `search`),
`ApplicationController` (`index`, `store`, `updateStep`), `DocumentController`
(`index`, `store`, `download`, `destroy`), `DashboardController` (`index`).

**Existing routes (all under `auth` middleware unless noted):**
`/dashboard`, `/programs` (`programs.index`), `/programs/search` (`programs.search`, public),
`/applications` (`applications.index`, `applications.store`), `/steps/{step}` (PATCH, `steps.update`),
`/documents` (`documents.index`, `documents.store`), `/documents/{document}/download`,
`/documents/{document}` (DELETE), `/faq` (`faq.index`).

**File storage:** `local` disk root is `storage_path('app')` (not `app/private`). Documents
stored at `documents/{user_id}/{random_filename}`.

---

## Task 1 — FR-1.3: User Profile Management

**Goal:** Let users view/edit country of origin, field of study, previous degree info.

1. Migration: `php artisan make:model UserProfile -m`
   ```php
   Schema::create('user_profiles', function (Blueprint $table) {
       $table->id();
       $table->foreignId('user_id')->constrained()->onDelete('cascade');
       $table->string('country_of_origin')->nullable();
       $table->string('previous_degree')->nullable();
       $table->string('previous_institution')->nullable();
       $table->string('target_field')->nullable();
       $table->timestamps();
   });
   ```
2. `app/Models/UserProfile.php` — `fillable`: all fields above except id/timestamps.
   `belongsTo(User::class)`.
3. In `app/Models/User.php` add: `public function profile() { return $this->hasOne(UserProfile::class); }`
4. New controller `ProfileController` with `edit()` (show form) and `update()` (validate + `updateOrCreate`).
5. Routes (inside the `auth` group):
   ```php
   Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
   Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
   ```
6. New view `resources/views/frontend/profile.blade.php` — a `.panel` with a form (`.field` blocks
   for each input, `.btn .btn-primary` submit), pre-filled with `old()` / existing profile values.
7. Add a "Profile" link to the sidebar nav in `layouts/app.blade.php`, alongside Dashboard/Program search/etc.
8. **Important:** the registration flow does not currently collect country/field — for now
   the profile page is the only place to set them. Do not modify Breeze's `RegisteredUserController`.

**Acceptance:** Logged-in user can visit `/profile`, fill in country + field, save, reload page,
values persist and show pre-filled.

---

## Task 2 — FR-2.4: Program Comparison

**Goal:** Select up to 5 programs on the search page, view them side-by-side.

1. No migration needed — comparison is a client-side, session-based selection (matches the
   thesis design in Section 6.3.2 / 7.5.4).
2. In `frontend/programs.blade.php`, add a "+ Compare" button to each program row in
   `renderPrograms()` (jQuery), storing selected program IDs in `sessionStorage` as a JSON array
   (key: `compare_programs`). Cap at 5 — show an alert if the user tries to add a 6th.
3. Add a small floating "Compare (n)" button/badge (only visible when 1+ selected) linking to
   a new `/programs/compare` page.
4. New controller method `ProgramController@compare(Request $request)`:
   - Accepts a `ids` query param (comma-separated program IDs, e.g. `?ids=1,3,5`) since server-side
     rendering can't read sessionStorage directly — pass the IDs from JS via `window.location.href`
     when the user clicks "Compare (n)".
   - Loads `Program::with('university')->whereIn('id', $ids)->get()`.
   - Returns `frontend.programs-compare` view with the programs.
5. Route: `Route::get('/programs/compare', [ProgramController::class, 'compare'])->name('programs.compare');`
6. New view `frontend/programs-compare.blade.php` — a `.panel` containing an HTML `<table>`
   with programs as columns and rows for: University, Tuition, Deadline, Field, Language
   requirement. Keep table styling minimal/inherit existing panel look — wrap in a div with
   `overflow-x:auto` for mobile.

**Acceptance:** From `/programs`, select 2–5 programs via "+ Compare", click the floating
compare button, land on a table comparing exactly the selected programs.

---

## Task 3 — FR-3.1 (completion): Country-Based Checklist Personalization

**Goal:** Currently every application gets the same 5 steps regardless of country. Make steps
conditionally apply based on the user's `country_of_origin` (requires Task 1 completed first).

1. Migration to add column: `php artisan make:migration add_applicable_countries_to_application_steps_table`
   ```php
   Schema::table('application_steps', function (Blueprint $table) {
       $table->json('applicable_countries')->nullable()->after('step_order');
       // null/empty = applies to everyone
   });
   ```
2. Update `ApplicationStep` model `$fillable` to include `applicable_countries`, and cast it:
   ```php
   protected $casts = ['applicable_countries' => 'array'];
   ```
3. Update `ApplicationStepSeeder` — for the "Research qualification recognition" and "Submit
   qualification recognition application" steps, set `applicable_countries` to a sample list,
   e.g. `['Bangladesh', 'India', 'Pakistan', 'Nigeria']` (non-EU examples), leave others `null`
   (applies to all).
4. In `ApplicationController@store`, when generating steps for a new application, filter:
   ```php
   $userCountry = auth()->user()->profile->country_of_origin ?? null;
   foreach (ApplicationStep::orderBy('step_order')->get() as $step) {
       if ($step->applicable_countries && $userCountry && !in_array($userCountry, $step->applicable_countries)) {
           continue; // skip step not applicable to this user
       }
       UserApplicationStep::create([...]);
   }
   ```
5. Handle the case where the user has no profile/country set yet (show all steps by default,
   don't crash — use the `?? null` null-safe pattern above).

**Acceptance:** A user with country_of_origin = "Germany" (EU, not in the sample list) gets
fewer steps than a user with country_of_origin = "Bangladesh" when starting a new application.

---

## Task 4 — FR-4.3: Document–Checklist Integration

**Goal:** Uploading a document of a given type should be able to mark a matching checklist
step as "in_progress" automatically (per the thesis design, Section 7.5.1/FR-4.3).

1. Add a `document_type` hint field to `ApplicationStep` — simplest approach: add a
   `related_document_type` nullable string column matching the `document_type` enum values
   used in `Document` (`transcript`, `diploma`, `passport`, `language_test`, `recommendation`,
   `other`).
   ```php
   Schema::table('application_steps', function (Blueprint $table) {
       $table->string('related_document_type')->nullable()->after('applicable_countries');
   });
   ```
2. In `ApplicationStepSeeder`, set `related_document_type` = `'transcript'` on the "Prepare and
   certify academic documents" step (and any other step where it makes sense).
3. In `DocumentController@store`, after successfully creating the `Document`, look up whether
   the current user has an active `Application` with a `UserApplicationStep` whose
   `ApplicationStep->related_document_type` matches the uploaded `document_type`, and if that
   step's status is `not_started`, update it to `in_progress` (do not auto-mark `completed` —
   the user should still confirm completion manually, matching the existing UX).
   ```php
   $application = auth()->user()->applications()->latest()->first();
   if ($application) {
       $matchingStep = $application->steps->first(function ($s) use ($request) {
           return $s->step && $s->step->related_document_type === $request->document_type
               && $s->status === 'not_started';
       });
       if ($matchingStep) {
           $matchingStep->update(['status' => 'in_progress']);
       }
   }
   ```
4. Return this info in the JSON response so the frontend could show a toast (optional —
   `'checklist_updated' => (bool) $matchingStep`).

**Acceptance:** Uploading a "Transcript" document, then visiting `/applications`, shows the
"Prepare and certify academic documents" step already set to "In progress" without manually
changing the dropdown.

---

## Task 5 — FR-5.2 + FR-5.3: Glossary and External Resource Links

**Goal:** Two simple static content additions to the FAQ/help area — no migration needed,
same pattern as the existing `frontend/faq.blade.php`.

1. In `frontend/faq.blade.php`, add two new sections below the existing FAQ list (same page,
   not a separate route), each in its own `.panel`:

   **Glossary** — a definition list styled as simple `.panel` rows (term in `<strong>`,
   definition below in `.muted`):
   - Apostille — official certification verifying a document's authenticity for international use.
   - Nostrifikace (qualification recognition) — the Czech process confirming a foreign degree is equivalent to a Czech one.
   - ECTS credits — European Credit Transfer System, the standard unit for measuring study workload in EU higher education.

   **External Resources** — a simple bullet/link list (`<a>` tags, `target="_blank"`):
   - Ministry of Education qualification recognition portal — https://www.msmt.cz/
   - Study in Czechia (official government study portal) — https://www.studyin.cz/
   - Czech embassy locator — https://www.mzv.cz/jnp/en/about_the_ministry/czech_embassies_abroad/index.html

2. Keep this as static Blade/PHP arrays (like the existing `$faqs` array in the same file) —
   do not build a database-backed CMS for this, it's out of scope per Section 5.2.6.

**Acceptance:** `/faq` page now shows three sections: FAQ (existing), Glossary, External
Resources, all on one page.

---

## Notes for whoever runs this (Codex or otherwise)

- Run `php artisan migrate` after each migration task, don't batch them all silently — if one
  fails, stop and report which one.
- After Task 1 and Task 3, existing seeded/test data will need `php artisan migrate:fresh --seed`
  to get a clean state with a profile — warn before running this, since it deletes existing users.
- Do not modify `AuthenticatedSessionController`, `RegisteredUserController`, or any other
  Breeze-generated file.
- Do not change `public/css/style.css` class names — only add new rules if a genuinely new
  UI pattern is needed (e.g. the compare table), and keep new rules consistent with the
  existing palette (navy `#14203B`, gold `#B98A2E`, paper background `#FAF7F0`).
- After finishing, run `php artisan route:list` and paste the output so the routes can be
  sanity-checked against this spec.
