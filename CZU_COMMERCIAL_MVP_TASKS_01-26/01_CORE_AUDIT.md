# 01 --- Core Audit

## Objective

Audit the existing CZU Thesis Project before making commercial changes and identify whether the current Laravel application is ready for the next roadmap milestone.

## Executive summary

The codebase already contains a functional MVP foundation for a Czech university application assistant. Students can register, maintain a profile, browse programs, create application checklists, upload documents, and receive basic recommendations. The application also includes an admin area for universities, programs, users, and roles.

However, the project is still in an implementation phase rather than a finished commercial platform. The majority of the core flow is present, but several areas remain partial or brittle:

- Student profile data is present but incomplete for the later personalization and eligibility engine.
- Program detail pages are not implemented as dedicated views or controller flows.
- Verification, role enforcement, and document/application workflows are partially scaffolded but not yet deeply hardened.
- The admin and recommendation layers exist, but they are not yet aligned with a full production student journey.

Overall status: the project is a viable MVP foundation, but it still needs focused improvements around profile completeness, verification logic, and data consistency before commercialization.

## Audit findings by area

### 1. Authentication and user onboarding

- Existing feature: Standard Laravel auth flow is in place.
- Current implementation: Login, registration, password reset, email verification, logout, and confirm-password routes are present in `routes/auth.php`. Related controllers live under `app/Http/Controllers/Auth`.
- Status: DONE
- Relevant files:
  - `routes/auth.php`
  - `app/Http/Controllers/Auth/*.php`
  - `app/Http/Middleware/RedirectIfAuthenticated.php`
  - `app/Http/Middleware/Authenticate.php`
- Database dependencies:
  - `users` table via Laravel auth defaults
  - `password_resets` table
- Problems discovered:
  - The `User` model imports `MustVerifyEmail` but does not implement it, so email verification is not truly enforced in the app despite routes existing.
  - Registration onboarding is minimal and does not assign a default student role automatically.
- Recommended next task:
  - Confirm whether email verification is required and, if so, activate it consistently with user model and middleware.

### 2. Dashboard

- Existing feature: Student dashboard exists.
- Current implementation: `DashboardController` loads the latest application, counts uploaded documents, calculates progress, and shows the next application step. The view is `resources/views/frontend/dashboard.blade.php`.
- Status: DONE / IMPROVE
- Relevant files:
  - `app/Http/Controllers/DashboardController.php`
  - `resources/views/frontend/dashboard.blade.php`
- Database dependencies:
  - `applications`, `user_application_steps`, `application_steps`, `documents`, `programs`, `universities`
- Problems discovered:
  - The dashboard assumes there is a single latest application, which may not fit users with multiple applications or saved program interest lists.
  - It is useful for MVP but not yet a fully strategic student workspace.
- Recommended next task:
  - Extend the dashboard from a single-application tracker into a multi-application overview when the project grows.

### 3. Student profile

- Existing feature: A student profile model and flow exist.
- Current implementation: `UserProfile` stores country, degree, institution, target field, GPA, language test type/score, and annual budget. `ProfileController` supports editing and storing profile values. Profile views exist in `resources/views/frontend/profile.blade.php`.
- Status: IMPROVE
- Relevant files:
  - `app/Models/UserProfile.php`
  - `app/Http/Controllers/ProfileController.php`
  - `database/migrations/2026_09_16_214841_create_user_profiles_table.php`
  - `database/migrations/2026_09_22_220000_add_recommendation_fields_to_profiles_and_programs.php`
  - `resources/views/frontend/profile.blade.php`
- Database dependencies:
  - `user_profiles`
  - `users`
- Problems discovered:
  - The profile is still not complete for the later roadmap: it is missing fields called out in task 02 such as preferred city and target intake.
  - Validation is present but limited; there is no advanced completion status, required-field logic, or profile-specific request object.
  - There is no clear profile validation for “required for recommendation eligibility” vs. “optional for later use.”
- Recommended next task:
  - Task 02 (Student Profile Improvement) is the highest-value next task because recommendations, eligibility checks, and application personalization all depend on it.

### 4. Program search and filtering

- Existing feature: Program search is implemented.
- Current implementation: `ProgramController` exposes `index`, `search`, and `compare` methods. Search supports field selection, keyword search, and max tuition filtering. The view is `resources/views/frontend/programs.blade.php` and uses jQuery to fetch JSON from the backend.
- Status: DONE / IMPROVE
- Relevant files:
  - `app/Http/Controllers/ProgramController.php`
  - `resources/views/frontend/programs.blade.php`
  - `routes/web.php`
- Database dependencies:
  - `programs`, `universities`
- Problems discovered:
  - Search is limited to simple matching and does not include broader filters such as deadline, language score, field synonyms, or intake cycle.
  - The `search` endpoint does exact-field filtering rather than a broader eligibility-aware query for later commercial features.
- Recommended next task:
  - Extend search filtering into a reusable program discovery query instead of a bare index filter.

### 5. Program details

- Existing feature: Program detail pages are not implemented as a dedicated experience.
- Current implementation: Search and compare exist, but there is no dedicated `show` route or detail view for a single program. The app supports searching and comparing programs, but not deep program detail browsing.
- Status: MISSING
- Relevant files:
  - `app/Http/Controllers/ProgramController.php`
  - `resources/views/frontend/*`
- Database dependencies:
  - `programs`, `universities`
- Problems discovered:
  - This is a gap for user decision making because students can compare only selected programs, not inspect a full detail page with admissions requirements and study outcomes.
- Recommended next task:
  - Add a single-program detail view and route, then link it from list and recommendation cards.

### 6. Program comparison

- Existing feature: Comparison is implemented.
- Current implementation: `ProgramController::compare()` accepts comma-separated program IDs, loads the corresponding records, and renders `resources/views/frontend/programs-compare.blade.php`.
- Status: DONE
- Relevant files:
  - `app/Http/Controllers/ProgramController.php`
  - `resources/views/frontend/programs-compare.blade.php`
  - `resources/views/frontend/programs.blade.php`
- Database dependencies:
  - `programs`, `universities`
- Problems discovered:
  - Comparison uses browser session storage instead of a server-side saved shortlist, which is acceptable for MVP but weak for a multi-device or persistent student flow.
  - There is no explicit comparison detail page beyond a basic list layout.
- Recommended next task:
  - Add a more complete comparison matrix with admissions criteria, scholarship notes, and requirement fit indicators.

### 7. Recommendations

- Existing feature: Basic recommendation engine exists.
- Current implementation: `ProgramRecommendationService` scores programs based on target field, budget, GPA, and language test score. The controller passes the recommendation list to `resources/views/frontend/recommendations.blade.php`.
- Status: DONE / IMPROVE
- Relevant files:
  - `app/Services/ProgramRecommendationService.php`
  - `app/Http/Controllers/RecommendationController.php`
  - `resources/views/frontend/recommendations.blade.php`
  - `database/migrations/2026_09_22_220000_add_recommendation_fields_to_profiles_and_programs.php`
- Database dependencies:
  - `user_profiles`, `programs`, `universities`
- Problems discovered:
  - The recommendation score is useful but still simple and not tied to deeper eligibility logic.
  - It does not consider application deadlines, document requirements, source legitimacy, or profile completeness beyond a few fields.
  - The view and tests expect a 95% match text, which suggests the UX is tuned around a scoring concept but not a full business-grade algorithm.
- Recommended next task:
  - Upgrade the recommendation engine to an eligibility-first algorithm before scaling the platform.

### 8. Applications and application steps

- Existing feature: A program application workflow and step model exist.
- Current implementation: `ApplicationController` creates an application tied to a selected program and creates one `UserApplicationStep` per `ApplicationStep`. The step statuses are handled via `PATCH /steps/{step}`.
- Status: DONE / IMPROVE
- Relevant files:
  - `app/Http/Controllers/ApplicationController.php`
  - `app/Models/Application.php`
  - `app/Models/ApplicationStep.php`
  - `app/Models/UserApplicationStep.php`
  - `database/migrations/2026_09_15_215745_create_applications_table.php`
  - `database/migrations/2026_09_15_215801_create_application_steps_table.php`
  - `database/migrations/2026_09_15_215813_create_user_application_steps_table.php`
- Database dependencies:
  - `applications`, `application_steps`, `user_application_steps`, `programs`, `users`
- Problems discovered:
  - The workflow is more of a checklist tracker than a true application pipeline.
  - There is no explicit “submit application” state or multi-stage admin review/outcome handling.
  - The progress model is coarse and does not represent actual admission lifecycle logic.
- Recommended next task:
  - Define the real application phases (`draft`, `submitted`, `under_review`, `offer`, `rejected`) before expanding this module.

### 9. Checklist

- Existing feature: Application checklist exists and is interactive.
- Current implementation: `resources/views/frontend/checklist.blade.php` shows a list of steps and allows status updates via AJAX. `ApplicationController::updateStep()` persists step changes.
- Status: DONE
- Relevant files:
  - `app/Http/Controllers/ApplicationController.php`
  - `resources/views/frontend/checklist.blade.php`
  - `app/Models/UserApplicationStep.php`
- Database dependencies:
  - `user_application_steps`, `application_steps`, `applications`
- Problems discovered:
  - It is an operational checklist, not yet an admissions workflow with document verification or submission gating.
  - Because the step update endpoint is available without a proper role/ownership layer beyond the policy, it should not be treated as final product logic.
- Recommended next task:
  - Add a formal application state machine and better process gating before adding more checklist complexity.

### 10. Documents

- Existing feature: Document upload, download, and delete is implemented.
- Current implementation: `DocumentController` handles upload to `storage/app/documents/{user_id}` and saves metadata in the `documents` table. Ownership is checked via `DocumentPolicy`.
- Status: DONE / IMPROVE
- Relevant files:
  - `app/Http/Controllers/DocumentController.php`
  - `app/Models/Document.php`
  - `app/Policies/DocumentPolicy.php`
  - `database/migrations/2026_09_16_102126_create_documents_table.php`
  - `resources/views/frontend/documents.blade.php`
- Database dependencies:
  - `documents`, `users`, `applications`
- Problems discovered:
  - Uploaded documents are stored with metadata, but there is no review or verification status separate from upload.
  - The `Document` model includes an `application_id` field that is not actively used, creating a partial relationship and potential confusion.
  - There is no obvious cleanup for orphaned files or administration review logic.
- Recommended next task:
  - Standardize document categorization and add a review status before building external verification flows.

### 11. FAQ

- Existing feature: FAQ page is present.
- Current implementation: A simple FAQ route and static Blade page exist at `routes/web.php` and `resources/views/frontend/faq.blade.php`.
- Status: DONE
- Relevant files:
  - `routes/web.php`
  - `resources/views/frontend/faq.blade.php`
- Database dependencies:
  - None
- Problems discovered:
  - This is a static placeholder rather than a content-managed FAQ system.
- Recommended next task:
  - Replace static content with editable FAQ entries if the commercial product requires content management.

### 12. Admin dashboard

- Existing feature: Admin dashboard exists and includes basic stats.
- Current implementation: `AdminController` calculates counts for universities, programs, registered students, submitted applications, and application totals. It also lists recent applications and documents.
- Status: DONE
- Relevant files:
  - `app/Http/Controllers/admin/AdminController.php`
  - `resources/views/backend/pages/dashboard/index.blade.php`
- Database dependencies:
  - `universities`, `programs`, `users`, `applications`, `documents`
- Problems discovered:
  - The admin dashboard is sufficient for early operational insight but does not yet support verification workflows or advanced admissions analytics.
- Recommended next task:
  - Add verification queue management and document review reporting before exposing the dashboard more widely.

### 13. University CRUD

- Existing feature: University management is implemented.
- Current implementation: `UniversityController` offers index, create, store, edit, update, and destroy endpoints. The admin routes provide full CRUD access under `/admin/universities`.
- Status: DONE
- Relevant files:
  - `app/Http/Controllers/admin/UniversityController.php`
  - `routes/web.php`
- Database dependencies:
  - `universities`
- Problems discovered:
  - This is solid for admin configuration, but it does not yet include source verification or a check against official registry records.
- Recommended next task:
  - Add official source data tracking to support the later verification features.

### 14. Program CRUD

- Existing feature: Program management is implemented.
- Current implementation: `Admin
ogramController` supports creating, editing, listing, and deleting programs and associates them with universities.
- Status: DONE
- Relevant files:
  - `app/Http/Controllers/admin/ProgramController.php`
  - `routes/web.php`
  - `database/seeders/ProgramSeeder.php`
- Database dependencies:
  - `programs`, `universities`
- Problems discovered:
  - The data model is still narrow compared with the commercial roadmap; the platform needs more fields for official source status, admissions requirements, and additional program metadata.
- Recommended next task:
  - Expand the program data model to support eligibility, verification, and official source metadata.

### 15. Users

- Existing feature: User management is implemented for admins.
- Current implementation: `AdminUserController` lists and shows users, and `User` models include role support via Spatie Permission. `User` has `HasRoles` and `is_admin` flag support.
- Status: DONE / IMPROVE
- Relevant files:
  - `app/Models/User.php`
  - `app/Http/Controllers/admin/UserController.php`
  - `database/seeders/RolesAndPermissionsSeeder.php`
  - `database/seeders/DefaultAdminSeeder.php`
- Database dependencies:
  - `users`, `roles`, `permissions`, `model_has_roles`
- Problems discovered:
  - There is a split between the boolean `is_admin` flag and the Spatie role system. This is workable but can lead to inconsistencies if not managed carefully.
  - Student or admissions roles are seeded but not always assigned during onboarding.
- Recommended next task:
  - Centralize role assignment logic so onboarding and admin access decisions follow one path.

### 16. Applications (admin-side)

- Existing feature: Admin application listing and detail endpoints exist.
- Current implementation: `AdminApplicationController` exposes list and detail pages for applications.
- Status: IMPROVE
- Relevant files:
  - `app/Http/Controllers/admin/ApplicationController.php`
  - `routes/web.php`
- Database dependencies:
  - `applications`, `user_application_steps`, `users`, `programs`
- Problems discovered:
  - The controller exists, but the visible workflow does not yet include admission review controls, status changes, or resolution actions.
- Recommended next task:
  - Implement the admin review flow as a first-class part of the application lifecycle.

### 17. Verification workflow

- Existing feature: There is a verification concept in the routes and some admin/credential logic.
- Current implementation: Laravel auth contains email verification routes, and admin middleware checks for admin/super-admin roles. This is a reasonable foundation but not yet the full workflow expected by the roadmap.
- Status: IMPROVE
- Relevant files:
  - `routes/auth.php`
  - `app/Http/Middleware/EnsureUserIsAdmin.php`
  - `app/Http/Middleware/EnsureUserIsSuperAdmin.php`
  - `app/Models/User.php`
- Database dependencies:
  - `users`, `roles`, `permissions`
- Problems discovered:
  - Email verification is not fully enabled because the user model does not implement `MustVerifyEmail` as intended.
  - There is no formal document verification workflow or official source review pipeline yet.
- Recommended next task:
  - Complete the verification layer before scaling admissions operations.

### 18. Database structure and relationships

- Existing feature: The database already includes the main entities needed for the MVP.
- Current implementation: Core tables include `users`, `universities`, `programs`, `applications`, `application_steps`, `user_application_steps`, `documents`, `user_profiles`, plus roles/permission tables.
- Status: IMPROVE
- Relevant files:
  - `database/migrations/*.php`
  - `app/Models/*.php`
- Database dependencies:
  - `users` -> `applications`, `documents`, `user_profiles`
  - `universities` -> `programs`
  - `applications` -> `user_application_steps`
  - `application_steps` -> `user_application_steps`
- Problems discovered:
  - The schema is evolving quickly and some fields are still partial or inconsistent with the commercial roadmap.
  - `Document` has an `application_id` column in the migration but the controller does not consistently assign it.
  - `UserProfile` started with basic columns and later grew to include recommendation fields, which is fine for MVP but indicates scope changes during development.
  - `User` keeps both `is_admin` and role assignments, which should be consolidated to avoid confusion.
- Recommended next task:
  - Normalize the schema and model responsibilities before adopting more advanced commercial features.

### 19. Routes and middleware

- Existing feature: The application routes are organized into a recognizable MVP architecture.
- Current implementation: `routes/web.php` contains authenticated student routes, admin routes under `/admin`, and `routes/auth.php` contains standard auth flow. Middleware is defined in `app/Http/Kernel.php` with `auth`, `admin`, and `super-admin` gates.
- Status: DONE / IMPROVE
- Relevant files:
  - `routes/web.php`
  - `routes/auth.php`
  - `app/Http/Kernel.php`
  - `app/Http/Middleware/EnsureUserIsAdmin.php`
  - `app/Http/Middleware/EnsureUserIsSuperAdmin.php`
- Problems discovered:
  - Middleware controls are present, but there is no explicit role separation for the student-facing routes beyond `auth`.
  - The current structure works but is not yet a formal authorization model for all commercial roles.
- Recommended next task:
  - Formalize the role/permission model and define the student/admissions/admin matrix clearly before broad rollout.

### 20. Existing tests

- Existing feature: There are tests covering auth, recommendation logic, role checks, and admin access.
- Current implementation: Test coverage includes registration, password reset, admin access, and recommendation ranking. Files are under `tests/Feature/*`.
- Status: IMPROVE
- Relevant files:
  - `tests/Feature/AdminAccessTest.php`
  - `tests/Feature/RecommendationTest.php`
  - `tests/Feature/OwnershipAuthorizationTest.php`
  - `tests/Feature/RolePermissionManagementTest.php`
- Problems discovered:
  - Coverage is encouraging but still limited to a few main behaviors.
  - There are no tests validating profile completeness, document verification, true application lifecycle transitions, or admin review workflow.
- Recommended next task:
  - Add regression tests around profile data integrity and application submission/review states.

## High-risk issues and blockers

1. Profile data is not yet strong enough to support a scalable personalization and eligibility engine.
2. Email verification is partly scaffolded but not fully activated.
3. The application lifecycle is more of a checklist than a production admissions workflow.
4. There is a split between `is_admin` and the role-based permission system, which can create hidden access inconsistencies.
5. The project has multiple valid MVP features but no single source of truth for official data verification and document review.
6. Program and university data are present but not yet sufficiently rich for real commercial use.

## Database and feature dependency map

- User and profile data drive:
  - recommendations
  - eligibility checks
  - dashboard personalization
  - application matching

- Program and university data drive:
  - search
  - compare
  - recommendations
  - admin management

- Application and document tables drive:
  - checklist tracking
  - dashboard next-step logic
  - document-related process flow
  - admin review workflows

- Role and permission tables drive:
  - admin access
  - super-admin separation
  - access control patterns

## Recommended next task

The best next task is Task 02: Student Profile Improvement.

Reason:

- The profile is already the shared foundation for recommendations and application logic.
- Current profile fields are not yet complete enough for later eligibility or personalization features.
- Most of the remaining roadmap depends on profile completeness and data consistency.
- Fixing the profile data model first reduces risk for later work on program search, recommendations, checklist logic, and document verification.

This task should focus on:

- completing the profile schema to match the roadmap requirements
- validating required profile data for recommendation and eligibility logic
- ensuring the profile is consistently used by the dashboard, recommendations, and application workflow
- adding tests for validation and profile persistence

## Conclusion

The current codebase is a credible MVP scaffold for a Czech university application platform, but it is not yet a production-ready commercial product. The project already contains the essential building blocks for student onboarding, search, recommendations, application tracking, and admin management. The main risk is not missing basic features; it is that several key systems are only partially implemented and depend on a stronger, more consistent data model and authorization layer.

The next priority should not be a full rebuild. It should be a disciplined improvement of the existing foundation, especially around the student profile and verification architecture.
