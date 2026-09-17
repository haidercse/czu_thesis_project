# CZU Thesis Project — Admin Backend Implementation Spec

## Project Context (read this first)

This is a Laravel 11 application. The **frontend** (student-facing side) is already built —
see `resources/views/frontend/*.blade.php`, controllers directly under `app/Http/Controllers/`,
models under `app/Models/`. **Do not modify any frontend files, controllers, or routes** —
this spec is for the separate **admin backend** only.

**Admin theme already installed:** A third-party admin dashboard theme (Bootstrap-based, uses
`metismenu` for the sidebar, `ti-` / themify icon classes, a `#preloader` splash screen) lives
under:
- `resources/views/backend/layouts/master.blade.php` — main admin layout. Has
  `@yield('title')`, `@yield('admin-content')`, and includes partials via
  `@include('backend.layouts.partials.header|page-title|sidebar|footer|offset|style|scripts')`.
- `resources/views/backend/layouts/partials/sidebar.blade.php` — **already rewritten** (see
  below) with the app's actual menu (Dashboard, Universities, Programs, Users, Applications),
  replacing the theme's original demo menu.
- Public assets for the theme live under `public/admin/assets/...` — reference existing pages
  for exact CSS classes (cards, tables, forms, buttons) rather than guessing; open
  `resources/views/backend/pages/dashboard/index.blade.php` first and copy its card/table/form
  markup patterns exactly so new admin pages look consistent with the existing dashboard page.

**Folder conventions to follow exactly:**
- Admin controllers go in `app/Http/Controllers/Admin/` (capital A), namespace
  `App\Http\Controllers\Admin`.
- Admin views go in `resources/views/backend/pages/{feature}/{action}.blade.php`, e.g.
  `resources/views/backend/pages/universities/index.blade.php`,
  `resources/views/backend/pages/universities/create.blade.php`,
  `resources/views/backend/pages/universities/edit.blade.php`.
- Every admin view starts with `@extends('backend.layouts.master')`, sets
  `@section('title', 'Page Name')`, and wraps its content in `@section('admin-content') ... @endsection`.
- **Fix existing casing bug first:** `routes/web.php` currently has
  `use App\Http\Controllers\admin\AdminController;` (lowercase `admin`) and a route group with
  no `->name()`. Replace the existing admin route block entirely with the one in Task 0 below.

**Existing models to reuse (do not recreate):** `University` (`hasMany Program`), `Program`
(`belongsTo University`; fields: `program_name`, `field_of_study`, `degree_type`,
`duration_years`, `language_of_instruction`, `tuition_fee_annual`, `application_deadline`,
`min_gpa_requirement`, `language_proficiency_requirement`, `program_url`, `description`),
`User` (`hasMany Application`, `hasMany Document`, `hasOne UserProfile`), `Application`
(`belongsTo User`, `belongsTo Program`, `hasMany UserApplicationStep` via `steps()`, has
`getProgressPercentageAttribute()`).

**Auth note:** There is currently no admin/role distinction — any logged-in user could reach
`/admin` routes if they know the URL. For this thesis iteration, protect all admin routes with
the standard `auth` middleware only (matching Section 5.2.6's stated scope), and add a single
TODO comment in the route file noting that role-based admin access control is a known gap
(consistent with the honesty required elsewhere in this thesis — do not silently pretend
role-based access exists if it doesn't).

---

## Task 0 — Fix and Extend the Admin Route Group

Replace the existing admin block in `routes/web.php` (the one using
`App\Http\Controllers\admin\AdminController`) with:

```php
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\UniversityController;
use App\Http\Controllers\Admin\ProgramController as AdminProgramController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\ApplicationController as AdminApplicationController;

// TODO: these routes are protected by `auth` only — any logged-in user can access
// the admin panel. Role-based restriction is out of scope for this thesis iteration
// (see Section 5.2.6 / Chapter 9 limitations) and is recommended future work.
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');

    Route::resource('universities', UniversityController::class)->except(['show']);
    Route::resource('programs', AdminProgramController::class)->except(['show']);

    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');

    Route::get('/applications', [AdminApplicationController::class, 'index'])->name('applications.index');
    Route::get('/applications/{application}', [AdminApplicationController::class, 'show'])->name('applications.show');
});
```

Note the aliasing (`ProgramController as AdminProgramController`, etc.) — this avoids any
ambiguity with the frontend's existing `App\Http\Controllers\ProgramController`, which must
NOT be touched.

---

## Task 1 — Admin Dashboard (stats overview)

**Controller:** `app/Http/Controllers/Admin/AdminController.php`

```php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\University;
use App\Models\Program;
use App\Models\User;
use App\Models\Application;

class AdminController extends Controller
{
    public function index()
    {
        $stats = [
            'universities' => University::count(),
            'programs' => Program::count(),
            'users' => User::count(),
            'applications' => Application::count(),
        ];

        $recentApplications = Application::with('user', 'program.university')
            ->latest()->take(5)->get();

        return view('backend.pages.dashboard.index', compact('stats', 'recentApplications'));
    }
}
```

**View:** `resources/views/backend/pages/dashboard/index.blade.php` **already exists** — do
not recreate it from scratch. Instead:
1. Open the existing file and identify its current card/stat-widget markup (it likely has
   placeholder demo numbers from the theme).
2. Replace the placeholder numbers with `{{ $stats['universities'] }}`, `{{ $stats['programs'] }}`,
   `{{ $stats['users'] }}`, `{{ $stats['applications'] }}` in four stat cards, keeping the
   theme's existing card HTML/CSS structure.
3. Below the stat cards, add a simple table (reuse the theme's existing table markup/classes
   from elsewhere in the file, e.g. `table table-bordered` or whatever class the theme uses)
   listing `$recentApplications`: columns = Student name (`$app->user->name`), Program
   (`$app->program->program_name`), University (`$app->program->university->name`), Status
   (`$app->status`), Progress (`$app->progress_percentage . '%'`).

---

## Task 2 — Universities CRUD

**Controller:** `app/Http/Controllers/Admin/UniversityController.php`

```php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\University;
use Illuminate\Http\Request;

class UniversityController extends Controller
{
    public function index()
    {
        $universities = University::withCount('programs')->latest()->paginate(15);
        return view('backend.pages.universities.index', compact('universities'));
    }

    public function create()
    {
        return view('backend.pages.universities.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'website_url' => 'nullable|url',
        ]);

        University::create($validated);

        return redirect()->route('admin.universities.index')->with('success', 'University added.');
    }

    public function edit(University $university)
    {
        return view('backend.pages.universities.edit', compact('university'));
    }

    public function update(Request $request, University $university)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'website_url' => 'nullable|url',
        ]);

        $university->update($validated);

        return redirect()->route('admin.universities.index')->with('success', 'University updated.');
    }

    public function destroy(University $university)
    {
        $university->delete(); // cascades to programs per existing migration's onDelete('cascade')
        return redirect()->route('admin.universities.index')->with('success', 'University deleted.');
    }
}
```

**Views (3 files):**
- `backend/pages/universities/index.blade.php` — `@extends('backend.layouts.master')`,
  table listing `$universities` (Name, Location, # Programs via `$u->programs_count`, Edit/Delete
  action buttons), pagination links (`{{ $universities->links() }}`), an "Add University" button
  linking to `route('admin.universities.create')`. Show a success alert if
  `session('success')` is present (check how the theme's `partials/message.blade.php` renders
  flash messages — reuse that partial via `@include('backend.layouts.partials.message')` if it
  already handles this).
- `backend/pages/universities/create.blade.php` — form (`method="POST"`,
  `action="{{ route('admin.universities.store') }}"`, `@csrf`) with fields: name, location,
  website_url. Show validation errors (`$errors`) next to each field, matching whatever error
  display pattern the theme's `form.html` demo page used (check theme assets if unsure, else
  use a simple `<span class="text-danger">{{ $errors->first('name') }}</span>` under each field).
- `backend/pages/universities/edit.blade.php` — same form, pre-filled with `$university`
  values, `method="POST"` + `@method('PUT')`, action to `route('admin.universities.update', $university)`.

---

## Task 3 — Programs CRUD

**Controller:** `app/Http/Controllers/Admin/ProgramController.php` (namespace `App\Http\Controllers\Admin`)

```php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\University;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    public function index()
    {
        $programs = Program::with('university')->latest()->paginate(15);
        return view('backend.pages.programs.index', compact('programs'));
    }

    public function create()
    {
        $universities = University::orderBy('name')->get();
        return view('backend.pages.programs.create', compact('universities'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'university_id' => 'required|exists:universities,id',
            'program_name' => 'required|string|max:255',
            'field_of_study' => 'required|string|max:255',
            'tuition_fee_annual' => 'required|numeric|min:0',
            'application_deadline' => 'required|date',
            'language_proficiency_requirement' => 'nullable|string|max:255',
        ]);

        Program::create($validated);

        return redirect()->route('admin.programs.index')->with('success', 'Program added.');
    }

    public function edit(Program $program)
    {
        $universities = University::orderBy('name')->get();
        return view('backend.pages.programs.edit', compact('program', 'universities'));
    }

    public function update(Request $request, Program $program)
    {
        $validated = $request->validate([
            'university_id' => 'required|exists:universities,id',
            'program_name' => 'required|string|max:255',
            'field_of_study' => 'required|string|max:255',
            'tuition_fee_annual' => 'required|numeric|min:0',
            'application_deadline' => 'required|date',
            'language_proficiency_requirement' => 'nullable|string|max:255',
        ]);

        $program->update($validated);

        return redirect()->route('admin.programs.index')->with('success', 'Program updated.');
    }

    public function destroy(Program $program)
    {
        $program->delete();
        return redirect()->route('admin.programs.index')->with('success', 'Program deleted.');
    }
}
```

**Views (3 files):** same pattern as Task 2 —
- `backend/pages/programs/index.blade.php` — table: Program name, University (`$p->university->name`),
  Field, Tuition, Deadline, Edit/Delete buttons, pagination.
- `backend/pages/programs/create.blade.php` — form with a `<select name="university_id">`
  populated from `$universities`, plus text/number/date inputs for the other fields.
- `backend/pages/programs/edit.blade.php` — same, pre-filled, `@method('PUT')`.

---

## Task 4 — Users (view-only, no editing)

**Controller:** `app/Http/Controllers/Admin/UserController.php`

```php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('profile')->withCount('applications', 'documents')->latest()->paginate(15);
        return view('backend.pages.users.index', compact('users'));
    }

    public function show(User $user)
    {
        $user->load('profile', 'applications.program.university', 'documents');
        return view('backend.pages.users.show', compact('user'));
    }
}
```

**Views (2 files):**
- `backend/pages/users/index.blade.php` — table: Name, Email, Country (`$u->profile?->country_of_origin ?? '—'`),
  # Applications, # Documents, registered date, a "View" link to `route('admin.users.show', $u)`.
- `backend/pages/users/show.blade.php` — user detail: name/email/profile info at top, then a
  list of their applications (program name, status, progress %) and a list of their uploaded
  documents (filename, type, upload date). Read-only, no edit/delete actions (out of scope —
  admins should not silently edit user data for this thesis iteration).

---

## Task 5 — Applications (view-only)

**Controller:** `app/Http/Controllers/Admin/ApplicationController.php`

```php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;

class ApplicationController extends Controller
{
    public function index()
    {
        $applications = Application::with('user', 'program.university')->latest()->paginate(15);
        return view('backend.pages.applications.index', compact('applications'));
    }

    public function show(Application $application)
    {
        $application->load('user', 'program.university', 'steps.step');
        return view('backend.pages.applications.show', compact('application'));
    }
}
```

**Views (2 files):**
- `backend/pages/applications/index.blade.php` — table: Student, Program, University, Status,
  Progress %, "View" link to `route('admin.applications.show', $a)`.
- `backend/pages/applications/show.blade.php` — application detail: student + program info,
  progress bar (reuse whatever progress-bar component the theme ships, e.g. from its
  `progressbar.html` demo — check theme assets), then a list of every `UserApplicationStep`
  with step name and current status.

---

## Verification Steps (run after implementation, report output)

```bash
php artisan route:list --name=admin
```
Should list exactly: `admin.dashboard`, `admin.universities.index/create/store/edit/update/destroy`,
`admin.programs.index/create/store/edit/update/destroy`, `admin.users.index`, `admin.users.show`,
`admin.applications.index`, `admin.applications.show`.

Then manually visit each URL while logged in and confirm:
1. `/admin/dashboard` shows real counts (not demo/placeholder numbers).
2. `/admin/universities` → create → edit → delete a test university works end-to-end.
3. `/admin/programs` → create → edit → delete a test program works end-to-end, and the
   university dropdown on create/edit is populated.
4. `/admin/users` lists real registered users; clicking one shows their applications/documents.
5. `/admin/applications` lists real applications; clicking one shows the step-by-step breakdown.

## Notes for whoever runs this (Codex or otherwise)

- Do not touch anything under `resources/views/frontend/`, `app/Http/Controllers/ProgramController.php`,
  `ApplicationController.php`, `DocumentController.php`, `DashboardController.php`,
  `ProfileController.php` (all frontend-side) — only add files under `Admin/` and `backend/pages/`.
- Before writing each Blade view, open the existing `backend/pages/dashboard/index.blade.php`
  first and copy its exact card / table / button / form CSS classes — do not guess Bootstrap
  class names or introduce a different admin UI style partway through.
- If `backend/layouts/partials/message.blade.php` already renders flash messages generically,
  `@include` it rather than writing new alert markup in every view.
- After finishing, run `php artisan route:list --name=admin` and paste the output.
