# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project overview

A Laravel 10 LMS (Learning Management System) with three user-facing roles — **admin**, **instructor**, **student** — plus an `event_manager` role for event administration. Roles/permissions are managed via `spatie/laravel-permission`. The frontend is server-rendered Blade + Tailwind + Alpine.js (no SPA framework), bundled with Vite.

## Commands

```bash
# Install
composer install
npm install
cp .env.example .env
php artisan key:generate

# Local dev (run in parallel)
php artisan serve
npm run dev            # Vite dev server (HMR)

# Build frontend assets for production
npm run build

# Database
php artisan migrate
php artisan db:seed                     # runs DatabaseSeeder (roles, users, courses, etc.)
php artisan migrate:fresh --seed        # reset + reseed

# Tests (PHPUnit, not Pest)
php artisan test
php artisan test --filter=TestClassName
php artisan test tests/Feature/Auth/AuthenticationTest.php
vendor/bin/phpunit

# Code style
vendor/bin/pint                # Laravel Pint, fixes in place
vendor/bin/pint --test         # check only, no changes

# Tinker
php artisan tinker
```

Tests use `RefreshDatabase`/the configured `DB_CONNECTION` (no sqlite in-memory override is active in `phpunit.xml` — the sqlite lines are commented out), so a real DB connection matching `.env` is required unless that's changed.

## Architecture

### Role-based routing (routes/web.php)

All authenticated traffic goes through `routes/web.php` inside a single `Route::middleware(['auth', 'verified'])` group, then splits into three role-scoped subgroups keyed by the Spatie `role:` middleware:

- `role:admin` → prefix `admin.`, controllers in `App\Http\Controllers\Admin\*`
- `role:instructor` → prefix `instructor.`, controllers in `App\Http\Controllers\Instructor\*`
- `role:student` → prefix `student.`, controllers in `App\Http\Controllers\Student\*`
- `role:admin|event_manager` → separate admin event-management group
- `role:instructor|student` → shared forum routes (course-level Q&A/discussion)

`GET /dashboard` is a role-dispatch route: it inspects `auth()->user()->hasRole(...)` and redirects to the matching `{role}.dashboard` named route. When adding a feature for a given role, mirror the existing controller namespace/prefix pattern rather than introducing new conventions.

Views follow the same three-way split under `resources/views/{admin,instructor,student}/...`, plus shared/public trees: `courses/`, `articles/`, `certificates/`, `forums/`, `public/`, `client/`, `components/` (including `components/sidebar/{instructor,mobile-instructor}-sidebar.blade.php` for role nav).

`App\Http\Controllers\CourseController`, `ForumController`, `ArticleController`, `ProfileController`, `UserSettingController` at the top level are shared across roles (not role-namespaced).

### Domain model

Core entity graph (see `app/Models/`): `Course` → `Section` → `Lesson`, with `Enrollment` (pivot with progress/status) linking `User` ↔ `Course`. Course also has `Quiz` (→ `QuizQuestion` → `QuizOption`, and `QuizAttempt` → `QuizAnswer`), `Assignment` (→ `Submission`, `AssignmentRubric`), `Review`, `Certificate`, `CoursePricing`/`CourseBundle`, `Forum` (→ `Thread` → `Post`/`PostLike`/`PostReport`).

Gamification: `Achievement`/`Badge` (→ `UserAchievement`/`UserBadge`), `PointActivity`/`UserPoint`.

Content/community: `Article`/`ArticleCategory`, `Event`/`EventRegistration`, tagging via a polymorphic `Tag`/`Taggable`.

`User` uses `HasRoles` (Spatie), `HasApiTokens` (Sanctum), and `SoftDeletes`. Role checks are commonly done via `hasRole('admin'|'instructor'|'student')` or the `isAdmin()`/`isInstructor()`/`isStudent()` helpers on the model (these also fall back to the `default_role` column).

Many models auto-generate slugs on `creating`/`updating` in `boot()` (see `Course`) — follow that pattern for new sloggable models rather than handling slugs in controllers.

### Authorization gotcha

`App\Providers\AuthServiceProvider::$policies` maps `CoursePolicy` to `Google\Service\Classroom\Course` (an import mistake), **not** `App\Models\Course`. `CoursePolicy` is effectively not wired up to the app's `Course` model via Laravel's policy auto-discovery/`Gate`. Be aware of this if you rely on `$this->authorize('...', $course)` for the LMS `Course` model — verify it's actually being enforced, or fix the import if you touch this area.

### Middleware & auth

`app/Http/Kernel.php` (Laravel 10 style, not the new `bootstrap/app.php` middleware API) registers custom middleware:
- `Localization` — resolves app locale from session → `UserSetting->language` → `config('app.locale')`, in that order.
- `UpdateUserLastSeen` — runs on every `web` request.
- Spatie's `role`, `permission`, `role_or_permission` middleware aliases are available.
- `CheckTokenAbility` (`ability` alias) — Sanctum token ability checks for the API.

API auth (`routes/api.php`) is Sanctum-based (`auth:sanctum`), separate from the session-based web auth.

### Background jobs & PDFs

`app/Jobs/`: `GenerateCertificateJob`, `SendAssignmentNotificationJob` — queued work dispatched from controllers/models rather than done synchronously. Email verification uses Laravel's built-in `SendEmailVerificationNotification` listener on the `Registered` event, not a custom job. `barryvdh/laravel-dompdf` generates certificate PDFs; `endroid/qr-code` generates certificate verification QR codes (see the public `certificate.verify` route).

### Seeding

`database/seeders/DatabaseSeeder.php` orchestrates `RolesAndPermissionsSeeder` (must run before role-dependent seeders), `UserSeeder`, `CategorySeeder`, `TagSeeder`, `CourseSeeder`, `SectionSeeder`, `LessonSeeder`, `QuizSeeder`, `EventSeeder`, `GamificationSeeder`. Run order matters — respect existing dependencies when adding new seeders.
