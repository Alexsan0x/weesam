# Dalili — Laravel + Tailwind Migration Plan

> **Project:** Dalili (دليلي) — Bilingual Jordan Tourism Platform
> **Current Stack:** Vanilla PHP 8.3, PostgreSQL (Neon Cloud), Vanilla CSS, Vanilla JS
> **Target Stack:** Laravel 11, Tailwind CSS 4, Alpine.js, Inertia.js + Vue 3 (or Blade), RESTful API v1
> **Repo:** `github.com/Alexsan0x/weesam` (branch: `main`)
> **Last checkpoint commit:** `01b858d` — "Replace images for 7 places"

---

## TABLE OF CONTENTS

1. [Current State Checkpoint](#1-current-state-checkpoint)
2. [Architecture Overview — Current vs Target](#2-architecture-overview)
3. [Phase 0 — Pre-Migration Setup](#phase-0--pre-migration-setup)
4. [Phase 1 — Laravel Skeleton + Auth](#phase-1--laravel-skeleton--auth)
5. [Phase 2 — Database & Models](#phase-2--database--models)
6. [Phase 3 — API v1 (RESTful)](#phase-3--api-v1-restful)
7. [Phase 4 — Frontend (Tailwind + Blade/Vue)](#phase-4--frontend-tailwind--bladevue)
8. [Phase 5 — Features Migration](#phase-5--features-migration)
9. [Phase 6 — AI Chat & External Services](#phase-6--ai-chat--external-services)
10. [Phase 7 — Testing, Docs, Deploy](#phase-7--testing-docs-deploy)
11. [File-by-File Migration Map](#file-by-file-migration-map)
12. [Database Schema Reference](#database-schema-reference)
13. [API v1 Endpoint Specification](#api-v1-endpoint-specification)
14. [Environment Variables](#environment-variables)
15. [External Services & Keys](#external-services--keys)
16. [Current Codebase Audit Summary](#current-codebase-audit-summary)

---

## 1. CURRENT STATE CHECKPOINT

### What Exists & Works

| Feature | Status | Files |
|---------|--------|-------|
| User auth (register/login/logout) | ✅ Working | `login.php`, `register.php`, `logout.php`, `api/auth.php` |
| Role system (user/admin) | ✅ Working | `config.php` (`isAdmin()`), `admin.php` |
| Admin panel (CRUD places, manage users) | ✅ Working | `admin.php` |
| 13 tourist places with bilingual fields | ✅ Working | `data/places.json`, PostgreSQL `places` table |
| Interactive Google Maps page | ✅ Working | `map.php`, `js/map.js`, `api/places.php` |
| AI Chat (Abu Mahmoud via Gemini) | ✅ Working | `api/chat.php`, `js/chat.js`, `includes/footer.php` |
| Timeline view (chronological) | ✅ Working | `timeline.php` |
| Favorites system | ✅ Working | `favorites.php`, `api/favorites.php` |
| User settings (profile + password) | ✅ Working | `settings.php` |
| About page (team, tech stack) | ✅ Working | `about.php` |
| Welcome splash page | ✅ Working | `welcome.php` (standalone) |
| Bilingual EN/AR with RTL | ✅ Working | `t($en, $ar)` helper, `$isArabic`, CSS RTL rules |
| Dark theme design | ✅ Working | `css/style.css` (~2960 lines) |
| Mobile responsive | ✅ Working | Media queries at 1024/768/480px |
| Deployment (Docker + Railway) | ✅ Working | `Dockerfile`, deployed at Railway + Linux server |

### Known Issues / Tech Debt

- **No CSRF protection** anywhere
- **Credentials hardcoded** in `config.php` (DB, API keys)
- **No input validation library** — manual validation per-endpoint
- **No rate limiting** on API
- **No tests** of any kind
- **`init_db.php` is outdated** — `migrate.php` is the correct schema
- **Arabic translations incomplete** — `city_ar`, `category_ar`, `era_ar`, `description_ar` missing from JSON seed
- **Chat API has no auth check** server-side
- **Images hotlinked from Unsplash** — no local storage/CDN
- **CSS is 2960 lines** in a single file — no build system
- **No API versioning** — endpoints at `api/*.php`
- **No pagination** on any list
- **`btn-lang` positioned outside `nav-links`** causing hamburger menu crowding on mobile

---

## 2. ARCHITECTURE OVERVIEW

### Current (Vanilla PHP)
```
Browser → PHP Files (routing by filename) → db.php (PDO) → PostgreSQL
                                          → Gemini API (curl)
                                          → Google Maps API (client-side)
```

### Target (Laravel)
```
Browser → Laravel Router → Controllers → Services → Eloquent Models → PostgreSQL
     ↕                  ↕                         → GeminiService (HTTP client)
  Tailwind CSS      Middleware                     → Google Maps (client JS)
  Alpine.js         (auth, rate-limit,
  Vue 3 (optional)   CSRF, lang, admin)

API:  /api/v1/* → API Controllers → Resources → JSON
Web:  /*        → Web Controllers → Blade Views → HTML
```

---

## PHASE 0 — PRE-MIGRATION SETUP

**Goal:** Create the Laravel project alongside the old code, configure environment.

### Tasks

- [ ] **0.1** Create new branch `feature/laravel-migration`
- [ ] **0.2** Install Laravel 11: `composer create-project laravel/laravel dalili-laravel`
- [ ] **0.3** Move old code to `_legacy/` subfolder for reference
- [ ] **0.4** Install Tailwind CSS 4: `npm install tailwindcss @tailwindcss/vite`
- [ ] **0.5** Install Alpine.js: `npm install alpinejs`
- [ ] **0.6** Configure `.env` (see [Environment Variables](#environment-variables))
- [ ] **0.7** Configure `config/database.php` for PostgreSQL (Neon)
- [ ] **0.8** Set up Vite config for Tailwind + Alpine
- [ ] **0.9** Configure Laravel localization (`config/app.php` locale, `lang/en/`, `lang/ar/`)
- [ ] **0.10** Set up RTL middleware or Blade directive

### Deliverables
- Fresh Laravel 11 project compiles with `npm run dev`
- `php artisan serve` shows default Laravel page
- `.env` has all DB + API credentials
- Tailwind utilities work in a test Blade view

---

## PHASE 1 — LARAVEL SKELETON + AUTH

**Goal:** Authentication system, middleware, user model.

### Tasks

- [ ] **1.1** Install Laravel Breeze (Blade or Inertia+Vue): `composer require laravel/breeze --dev && php artisan breeze:install`
- [ ] **1.2** Customize User model:
  - Add `role` field (enum: `user`, `admin`)
  - Add `name` (already exists)
  - Fillable: `name, email, password, role`
- [ ] **1.3** Create `AdminMiddleware` — checks `$request->user()->role === 'admin'`
- [ ] **1.4** Create `SetLocale` middleware — reads `?lang=` or session, sets `app()->setLocale()` and `$isArabic` shared view variable
- [ ] **1.5** Register middleware in `bootstrap/app.php`
- [ ] **1.6** Customize auth views (login, register) with Tailwind — dark theme, Petra background
- [ ] **1.7** After-register redirect to login (not auto-login)
- [ ] **1.8** Seed default admin: `admin@dalili.jo` / `admin123`
- [ ] **1.9** Create `UserSeeder` with admin account

### Key Decisions
- **Auth package:** Laravel Breeze (lightweight, Blade-based) OR Breeze + Inertia/Vue (SPA-like)
- **Recommendation:** Start with Breeze Blade for simplicity, upgrade to Inertia later if needed

### Deliverables
- Register, login, logout working
- Admin middleware protecting `/admin` routes
- Language switching works via middleware
- Dark-themed auth pages matching current design

---

## PHASE 2 — DATABASE & MODELS

**Goal:** Eloquent models, migrations, seeders matching current schema.

### Migrations to Create

```bash
php artisan make:migration create_users_table        # (Breeze handles this)
php artisan make:migration create_places_table
php artisan make:migration create_favorites_table
```

#### `places` table
```php
Schema::create('places', function (Blueprint $table) {
    $table->string('id', 50)->primary();       // e.g. "petra", "wadi-rum"
    $table->string('name');
    $table->string('name_ar')->nullable();
    $table->string('city')->nullable();
    $table->string('city_ar')->nullable();
    $table->string('category')->nullable();
    $table->string('category_ar')->nullable();
    $table->float('lat')->nullable();
    $table->float('lng')->nullable();
    $table->integer('year_established')->nullable();
    $table->string('era')->nullable();
    $table->string('era_ar')->nullable();
    $table->text('image')->nullable();
    $table->text('description')->nullable();
    $table->text('description_ar')->nullable();
    $table->timestamps();
});
```

#### `favorites` table
```php
Schema::create('favorites', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->string('place_id', 50);
    $table->foreign('place_id')->references('id')->on('places')->cascadeOnDelete();
    $table->timestamps();
    $table->unique(['user_id', 'place_id']);
});
```

### Models

| Model | File | Relationships |
|-------|------|---------------|
| `User` | `app/Models/User.php` | `hasMany(Favorite)`, `favorites()` accessor |
| `Place` | `app/Models/Place.php` | `hasMany(Favorite)`, `favoritedBy()` (belongsToMany User through favorites) |
| `Favorite` | `app/Models/Favorite.php` | `belongsTo(User)`, `belongsTo(Place)` |

### Seeders

- [ ] **2.1** `PlaceSeeder` — reads `_legacy/data/places.json`, inserts all 13 places
- [ ] **2.2** `UserSeeder` — creates admin account
- [ ] **2.3** `DatabaseSeeder` calls both

### Tasks

- [ ] **2.4** Create all 3 migrations
- [ ] **2.5** Create `Place`, `Favorite` models with relationships
- [ ] **2.6** Add `role` column to users migration (or separate migration)
- [ ] **2.7** Update `User` model: add `role` to `$fillable`, add `isAdmin()` method
- [ ] **2.8** Create seeders
- [ ] **2.9** Run `php artisan migrate --seed` and verify

### Deliverables
- All tables created via Artisan migrations
- 13 places seeded from JSON
- Eloquent relationships tested in Tinker

---

## PHASE 3 — API v1 (RESTful)

**Goal:** Clean, versioned, documented REST API under `/api/v1/`.

### Route Structure (`routes/api.php`)

```php
Route::prefix('v1')->group(function () {

    // Public
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::get('/places', [PlaceController::class, 'index']);
    Route::get('/places/{place}', [PlaceController::class, 'show']);

    // Authenticated
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/user', [AuthController::class, 'user']);

        Route::get('/favorites', [FavoriteController::class, 'index']);
        Route::post('/favorites', [FavoriteController::class, 'store']);
        Route::delete('/favorites/{place}', [FavoriteController::class, 'destroy']);

        Route::post('/chat', [ChatController::class, 'send']);

        // Admin only
        Route::middleware('admin')->group(function () {
            Route::apiResource('admin/places', AdminPlaceController::class);
            Route::apiResource('admin/users', AdminUserController::class);
            Route::get('admin/stats', [AdminController::class, 'stats']);
        });
    });
});
```

### Controllers to Create

| Controller | Namespace | Responsibility |
|-----------|-----------|----------------|
| `AuthController` | `Api\V1` | register, login (Sanctum token), logout, current user |
| `PlaceController` | `Api\V1` | list (with search/filter/pagination), show single |
| `FavoriteController` | `Api\V1` | list user favorites, add, remove |
| `ChatController` | `Api\V1` | proxy message to Gemini, return response |
| `AdminPlaceController` | `Api\V1` | CRUD places (admin only) |
| `AdminUserController` | `Api\V1` | CRUD users (admin only) |
| `AdminController` | `Api\V1` | dashboard stats |

### API Resources (JSON Transformers)

```php
// app/Http/Resources/PlaceResource.php
class PlaceResource extends JsonResource {
    public function toArray($request) {
        return [
            'id'               => $this->id,
            'name'             => $this->name,
            'name_ar'          => $this->name_ar,
            'city'             => $this->city,
            'city_ar'          => $this->city_ar,
            'category'         => $this->category,
            'category_ar'      => $this->category_ar,
            'lat'              => $this->lat,
            'lng'              => $this->lng,
            'year_established' => $this->year_established,
            'era'              => $this->era,
            'era_ar'           => $this->era_ar,
            'image'            => $this->image,
            'description'      => $this->description,
            'description_ar'   => $this->description_ar,
            'is_favorited'     => $this->when(auth()->check(), fn() =>
                auth()->user()->favorites()->where('place_id', $this->id)->exists()
            ),
            'created_at'       => $this->created_at,
        ];
    }
}
```

### Form Requests (Validation)
- `LoginRequest` — email (required, email), password (required)
- `RegisterRequest` — name (required, max:100), email (required, email, unique:users), password (required, min:6, confirmed)
- `StorePlaceRequest` — id (required, unique:places), name (required), lat (numeric), lng (numeric), etc.
- `UpdatePlaceRequest` — same but id is sometimes:required
- `ChatRequest` — message (required, string, max:2000)

### Tasks

- [ ] **3.1** Install Sanctum: `php artisan install:api` (Laravel 11 includes it)
- [ ] **3.2** Create all API controllers in `app/Http/Controllers/Api/V1/`
- [ ] **3.3** Create `PlaceResource`, `UserResource`, `FavoriteResource`
- [ ] **3.4** Create Form Request classes for validation
- [ ] **3.5** Define all routes in `routes/api.php`
- [ ] **3.6** Implement search/filter on `PlaceController@index` with query params: `?search=`, `?category=`, `?page=`, `?per_page=`
- [ ] **3.7** Implement auth flow with Sanctum tokens
- [ ] **3.8** Add rate limiting: 60 req/min general, 10 req/min chat
- [ ] **3.9** Test all endpoints with Postman/Insomnia
- [ ] **3.10** Generate API docs (OpenAPI/Swagger via `l5-swagger` or `scribe`)

### Deliverables
- All endpoints functional and returning proper JSON
- Sanctum token auth working
- Rate limiting active
- API documentation auto-generated

---

## PHASE 4 — FRONTEND (TAILWIND + BLADE/VUE)

**Goal:** Rebuild all pages with Tailwind CSS, matching the current dark theme aesthetic.

### Layout Structure

```
resources/views/
├── layouts/
│   ├── app.blade.php          # Main layout (nav, footer, chat widget)
│   └── guest.blade.php        # Auth pages layout
├── components/
│   ├── navbar.blade.php
│   ├── footer.blade.php
│   ├── chat-widget.blade.php
│   ├── place-card.blade.php
│   ├── lang-toggle.blade.php
│   └── user-dropdown.blade.php
├── pages/
│   ├── welcome.blade.php      # Splash/intro page
│   ├── home.blade.php         # Homepage (hero, features, places grid, CTA)
│   ├── map.blade.php          # Google Maps interactive page
│   ├── timeline.blade.php     # Chronological place timeline
│   ├── about.blade.php        # About page
│   ├── favorites.blade.php    # User favorites
│   └── settings.blade.php     # Account settings
├── admin/
│   ├── dashboard.blade.php
│   ├── places/
│   │   ├── index.blade.php
│   │   └── form.blade.php     # Create/edit modal or page
│   └── users/
│       ├── index.blade.php
│       └── form.blade.php
├── auth/
│   ├── login.blade.php
│   └── register.blade.php
└── partials/
    └── ...
```

### Tailwind Theme Config (`tailwind.config.js`)

Port the current CSS variables to Tailwind:

```js
export default {
  theme: {
    extend: {
      colors: {
        primary:      { DEFAULT: '#e74c3c', dark: '#c0392b' },
        dark:         { DEFAULT: '#0a0a14', card: '#12121e', light: '#1a1a2e' },
        border:       'rgba(255,255,255,0.08)',
        'text-dark':  '#e8e8f0',
        'text-body':  '#a0a0b8',
        'text-light': '#6a6a82',
      },
      fontFamily: {
        sans:  ['Poppins', 'sans-serif'],
        cairo: ['Cairo', 'sans-serif'],
      },
      borderRadius: {
        DEFAULT: '12px',
        sm: '8px',
      },
    },
  },
  darkMode: 'class',
}
```

### Tasks

- [ ] **4.1** Set up Tailwind config with project colors/fonts
- [ ] **4.2** Create `app.blade.php` layout with navbar + footer
- [ ] **4.3** Create `guest.blade.php` layout for auth pages
- [ ] **4.4** Build navbar component (responsive hamburger, user dropdown, lang toggle)
- [ ] **4.5** Build footer component with chat widget
- [ ] **4.6** Port welcome splash page
- [ ] **4.7** Port homepage (hero, features, places grid, CTA)
- [ ] **4.8** Port map page (Google Maps integration preserved)
- [ ] **4.9** Port timeline page
- [ ] **4.10** Port favorites page
- [ ] **4.11** Port settings page
- [ ] **4.12** Port about page
- [ ] **4.13** Port admin panel (dashboard, places CRUD, users CRUD)
- [ ] **4.14** Port login/register pages with Petra background
- [ ] **4.15** Implement RTL support (Tailwind RTL plugin or `dir` attribute)
- [ ] **4.16** Port all animations (scroll reveal, hero particles)
- [ ] **4.17** Mobile responsive testing at all breakpoints

### Deliverables
- All pages pixel-close to current design but in Tailwind
- RTL switching works
- Responsive at 480px, 768px, 1024px
- Dark theme throughout

---

## PHASE 5 — FEATURES MIGRATION

**Goal:** Migrate all business logic into Laravel services/controllers.

### Service Classes

| Service | Purpose | Methods |
|---------|---------|---------|
| `PlaceService` | Place business logic | `getAll()`, `search($query, $category)`, `getById($id)`, `create($data)`, `update($id, $data)`, `delete($id)` |
| `FavoriteService` | Favorite management | `getUserFavorites($userId)`, `add($userId, $placeId)`, `remove($userId, $placeId)`, `isFavorited($userId, $placeId)` |
| `GeminiService` | AI chat proxy | `chat($message, $lang): string` |
| `UserService` | User management (admin) | `getAll()`, `updateRole($id, $role)`, `delete($id)` |

### Web Routes (`routes/web.php`)

```php
// Public
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/welcome', [WelcomeController::class, 'index'])->name('welcome');
Route::get('/about', [AboutController::class, 'index'])->name('about');

// Auth (Breeze handles /login, /register, /logout)

// Authenticated
Route::middleware('auth')->group(function () {
    Route::get('/map', [MapController::class, 'index'])->name('map');
    Route::get('/timeline', [TimelineController::class, 'index'])->name('timeline');
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites');
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::put('/settings/profile', [SettingsController::class, 'updateProfile']);
    Route::put('/settings/password', [SettingsController::class, 'updatePassword']);
});

// Admin
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::resource('places', AdminPlaceController::class);
    Route::resource('users', AdminUserController::class)->except(['create', 'store']);
});

// Language switch
Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'ar'])) session(['locale' => $locale]);
    return redirect()->back();
})->name('lang.switch');
```

### Tasks

- [ ] **5.1** Create all Service classes
- [ ] **5.2** Create all Web controllers
- [ ] **5.3** Define web routes
- [ ] **5.4** Implement `HomeController` — load 6 featured places
- [ ] **5.5** Implement `MapController` — pass Google Maps API key to view
- [ ] **5.6** Implement `TimelineController` — all places sorted by year
- [ ] **5.7** Implement `FavoriteController` (web) — user's favorites
- [ ] **5.8** Implement `SettingsController` — profile + password update
- [ ] **5.9** Implement `AdminController` — dashboard stats, places CRUD, users management
- [ ] **5.10** Wire chat widget JS to hit `/api/v1/chat`
- [ ] **5.11** Wire map JS to hit `/api/v1/places`
- [ ] **5.12** Wire favorites JS to hit `/api/v1/favorites`

### Deliverables
- Every page from the old site works identically in Laravel
- All forms have CSRF tokens
- Admin panel fully functional
- Chat, map, favorites all connected via API v1

---

## PHASE 6 — AI CHAT & EXTERNAL SERVICES

**Goal:** Properly integrate Gemini AI and Google Maps as Laravel services.

### GeminiService (`app/Services/GeminiService.php`)

```php
class GeminiService
{
    private string $apiKey;
    private array $models = ['gemini-2.5-flash', 'gemini-2.0-flash'];

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
    }

    public function chat(string $message, string $lang = 'en'): string
    {
        $systemPrompt = $lang === 'ar' ? self::ARABIC_PROMPT : self::ENGLISH_PROMPT;
        // ... HTTP::post to Gemini API, try each model ...
    }
}
```

### Config (`config/services.php`)

```php
'gemini' => [
    'api_key' => env('GEMINI_API_KEY'),
],
'google_maps' => [
    'api_key' => env('GOOGLE_MAPS_API_KEY'),
],
```

### Tasks

- [ ] **6.1** Create `GeminiService` with model fallback chain
- [ ] **6.2** Bind `GeminiService` in `AppServiceProvider` (singleton)
- [ ] **6.3** Add rate limiting on chat endpoint (10/min per user)
- [ ] **6.4** Pass Google Maps key to views via config/view composer
- [ ] **6.5** Port `js/map.js` to use `/api/v1/places` and `/api/v1/favorites`
- [ ] **6.6** Port `js/chat.js` to use `/api/v1/chat`

### Deliverables
- Chat working via proper service class
- API keys in `.env`, never in source code  
- Rate limiting on chat

---

## PHASE 7 — TESTING, DOCS, DEPLOY

**Goal:** Tests, documentation, production deployment.

### Testing

| Type | Tool | What to Test |
|------|------|-------------|
| Unit | PHPUnit | `GeminiService`, `PlaceService`, `FavoriteService` |
| Feature | PHPUnit | Auth flow, API endpoints, admin CRUD, favorites toggle |
| Browser | Laravel Dusk (optional) | Login flow, map interaction, chat widget |

### Documentation

- [ ] **7.1** OpenAPI/Swagger spec for all API v1 endpoints (use `knuckleswtf/scribe`)
- [ ] **7.2** README.md with setup instructions, env vars, architecture overview
- [ ] **7.3** Contributing guide
- [ ] **7.4** PHPDoc on all public methods

### Deployment

- [ ] **7.5** Update `Dockerfile` for Laravel (PHP-FPM + Nginx or Octane)
- [ ] **7.6** Create `docker-compose.yml` for local dev
- [ ] **7.7** Configure Railway deployment (env vars, build command, start command)
- [ ] **7.8** Set up GitHub Actions CI: lint, test, deploy

### Tasks

- [ ] **7.9** Write Feature tests for: auth register/login, places list/show, favorites CRUD, chat, admin CRUD
- [ ] **7.10** Write Unit tests for services
- [ ] **7.11** Generate API documentation
- [ ] **7.12** Update README
- [ ] **7.13** Create production Dockerfile
- [ ] **7.14** Deploy to Railway
- [ ] **7.15** Verify all features in production

### Deliverables
- 80%+ test coverage on API
- Auto-generated API docs at `/docs`
- Docker-based deployment working
- CI pipeline running on push

---

## FILE-BY-FILE MIGRATION MAP

| Old File | → Laravel Equivalent | Notes |
|----------|---------------------|-------|
| `config.php` | `.env` + `config/services.php` + `config/app.php` | Credentials → env vars; helpers → Blade directives & middleware |
| `db.php` | Eloquent Models + Service classes | Each function → model method or service |
| `index.php` | `HomeController@index` → `pages/home.blade.php` | |
| `login.php` | Breeze `LoginController` → `auth/login.blade.php` | |
| `register.php` | Breeze `RegisterController` → `auth/register.blade.php` | |
| `logout.php` | Breeze handles via POST `/logout` | |
| `map.php` | `MapController@index` → `pages/map.blade.php` | |
| `timeline.php` | `TimelineController@index` → `pages/timeline.blade.php` | |
| `about.php` | `AboutController@index` → `pages/about.blade.php` | |
| `favorites.php` | `FavoriteController@index` → `pages/favorites.blade.php` | |
| `settings.php` | `SettingsController` → `pages/settings.blade.php` | |
| `admin.php` | `AdminController` + resource controllers → `admin/*.blade.php` | Split into multiple controllers |
| `welcome.php` | `WelcomeController@index` → `pages/welcome.blade.php` | |
| `api/auth.php` | `Api\V1\AuthController` | Sanctum tokens |
| `api/chat.php` | `Api\V1\ChatController` + `GeminiService` | |
| `api/favorites.php` | `Api\V1\FavoriteController` | |
| `api/places.php` | `Api\V1\PlaceController` | Pagination added |
| `includes/header.php` | `layouts/app.blade.php` + `components/navbar.blade.php` | |
| `includes/footer.php` | `components/footer.blade.php` + `components/chat-widget.blade.php` | |
| `css/style.css` | Tailwind utilities inline + `resources/css/app.css` (custom) | |
| `js/main.js` | `resources/js/app.js` + Alpine.js | |
| `js/chat.js` | `resources/js/chat.js` (ES module) | |
| `js/map.js` | `resources/js/map.js` (ES module) | |
| `setup.sql` | Laravel migrations | |
| `migrate.php` | `php artisan migrate --seed` | |
| `data/places.json` | `database/seeders/data/places.json` | |
| `Dockerfile` | Updated for Laravel (FPM or Octane) | |

---

## DATABASE SCHEMA REFERENCE

### Current Schema (PostgreSQL on Neon)

```sql
-- users
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role VARCHAR(20) DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- places
CREATE TABLE places (
    id VARCHAR(50) PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    name_ar VARCHAR(100),
    city VARCHAR(100),
    city_ar VARCHAR(100),
    category VARCHAR(50),
    category_ar VARCHAR(50),
    lat DOUBLE PRECISION,
    lng DOUBLE PRECISION,
    year_established INTEGER,
    era VARCHAR(100),
    era_ar VARCHAR(100),
    image TEXT,
    description TEXT,
    description_ar TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- favorites
CREATE TABLE favorites (
    id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    place_id VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(user_id, place_id)
);
```

### Laravel Migration Notes
- `users.password_hash` → rename to `password` (Laravel convention)
- Add `updated_at` to all tables (Laravel timestamps)
- Add `email_verified_at` to users (Laravel convention)
- `places.id` stays as string PK (not auto-increment)

---

## API V1 ENDPOINT SPECIFICATION

### Authentication

| Method | Endpoint | Auth | Body/Params | Response |
|--------|----------|------|-------------|----------|
| POST | `/api/v1/auth/register` | No | `{ name, email, password, password_confirmation }` | `{ user, token }` |
| POST | `/api/v1/auth/login` | No | `{ email, password }` | `{ user, token }` |
| POST | `/api/v1/auth/logout` | Bearer | — | `{ message }` |
| GET | `/api/v1/auth/user` | Bearer | — | `{ user }` |

### Places

| Method | Endpoint | Auth | Params | Response |
|--------|----------|------|--------|----------|
| GET | `/api/v1/places` | No | `?search=`, `?category=`, `?page=`, `?per_page=` | `{ data: [PlaceResource], meta: { pagination } }` |
| GET | `/api/v1/places/{id}` | No | — | `{ data: PlaceResource }` |

### Favorites

| Method | Endpoint | Auth | Body | Response |
|--------|----------|------|------|----------|
| GET | `/api/v1/favorites` | Bearer | — | `{ data: [PlaceResource] }` |
| POST | `/api/v1/favorites` | Bearer | `{ place_id }` | `{ message }` (201) |
| DELETE | `/api/v1/favorites/{place_id}` | Bearer | — | `{ message }` (200) |

### Chat

| Method | Endpoint | Auth | Body | Response |
|--------|----------|------|------|----------|
| POST | `/api/v1/chat` | Bearer | `{ message }` | `{ response }` |

### Admin

| Method | Endpoint | Auth | Body/Params | Response |
|--------|----------|------|-------------|----------|
| GET | `/api/v1/admin/stats` | Admin | — | `{ places_count, users_count, favorites_count }` |
| GET | `/api/v1/admin/places` | Admin | `?page=` | Paginated PlaceResource |
| POST | `/api/v1/admin/places` | Admin | Place fields | `{ data: PlaceResource }` (201) |
| PUT | `/api/v1/admin/places/{id}` | Admin | Place fields | `{ data: PlaceResource }` |
| DELETE | `/api/v1/admin/places/{id}` | Admin | — | 204 |
| GET | `/api/v1/admin/users` | Admin | `?page=` | Paginated UserResource |
| PUT | `/api/v1/admin/users/{id}` | Admin | `{ name, email, role, password? }` | `{ data: UserResource }` |
| DELETE | `/api/v1/admin/users/{id}` | Admin | — | 204 |

---

## ENVIRONMENT VARIABLES

```env
APP_NAME=Dalili
APP_ENV=production
APP_URL=http://102.203.200.31:9001

# Database (Neon PostgreSQL)
DB_CONNECTION=pgsql
DB_HOST=ep-calm-lab-alag0l8u-pooler.c-3.eu-central-1.aws.neon.tech
DB_PORT=5432
DB_DATABASE=neondb
DB_USERNAME=neondb_owner
DB_PASSWORD=npg_PrOcKE2Za9Gh
DB_SSLMODE=require

# External APIs
GOOGLE_MAPS_API_KEY=AIzaSyDb9lDX6ULJGKE74MK9iNJJFpdnKUgWThE
GEMINI_API_KEY=AIzaSyBTjZa_s5I6_satso78Mr2ryuxrQqMZhuU

# App
DEFAULT_LOCALE=en
FALLBACK_LOCALE=en
```

---

## EXTERNAL SERVICES & KEYS

| Service | Key | Used For |
|---------|-----|----------|
| **Neon PostgreSQL** | Connection string above | All data storage |
| **Google Maps JS API** | `AIzaSyDb9lDX6ULJGKE74MK9iNJJFpdnKUgWThE` | Interactive map on `/map` |
| **Google Gemini AI** | `AIzaSyBTjZa_s5I6_satso78Mr2ryuxrQqMZhuU` | AI chatbot "Abu Mahmoud" — models: `gemini-2.5-flash`, `gemini-2.0-flash` |
| **Unsplash** | No key (hotlinked image URLs) | Place images |
| **GitHub** | `Alexsan0x/weesam` | Source code repo |
| **Railway** | `weesam-production.up.railway.app` | Production hosting (Docker) |

---

## CURRENT CODEBASE AUDIT SUMMARY

### db.php — All 18 Functions

| # | Function | What It Does |
|---|----------|-------------|
| 1 | `getDB()` | Singleton PDO connection (pgsql, sslmode=require) |
| 2 | `findUserByEmail($email)` | SELECT * FROM users WHERE email = ? |
| 3 | `findUserById($id)` | SELECT * FROM users WHERE id = ? |
| 4 | `createUser($name, $email, $password)` | INSERT INTO users (password_hash via bcrypt) |
| 5 | `updateUser($id, $name, $email)` | UPDATE users SET name, email |
| 6 | `updateUserPassword($id, $newPassword)` | UPDATE users SET password_hash |
| 7 | `getAllUsers()` | SELECT id,name,email,role,created_at FROM users |
| 8 | `updateUserRole($id, $role)` | UPDATE users SET role |
| 9 | `deleteUser($id)` | DELETE FROM users |
| 10 | `getAllPlaces()` | SELECT * FROM places ORDER BY name |
| 11 | `getPlaceById($id)` | SELECT * FROM places WHERE id = ? |
| 12 | `createPlace($data)` | INSERT INTO places (15 columns) |
| 13 | `updatePlace($id, $data)` | UPDATE places SET (14 columns) |
| 14 | `deletePlace($id)` | DELETE FROM places |
| 15 | `searchPlaces($search, $category)` | Dynamic SELECT with LIKE + category filter |
| 16 | `getUserFavorites($userId)` | SELECT place_id FROM favorites (returns flat array) |
| 17 | `addFavorite($userId, $placeId)` | Check exists → INSERT INTO favorites |
| 18 | `removeFavorite($userId, $placeId)` | DELETE FROM favorites |

### config.php — All 6 Helper Functions

| Function | Signature | Maps To (Laravel) |
|----------|-----------|-------------------|
| `isLoggedIn()` | `(): bool` | `auth()->check()` |
| `isAdmin()` | `(): bool` | `auth()->user()?->isAdmin()` or middleware |
| `getCurrentUser()` | `(): ?array` | `auth()->user()` |
| `redirect($url)` | `(string): void` | `redirect($url)` |
| `sanitize($input)` | `(string): string` | Blade `{{ }}` auto-escapes |
| `t($en, $ar)` | `(string, string): string` | `__('messages.key')` or `@lang()` |

### Gemini Chat — System Prompt (preserve this exactly)

The AI persona is **"Abu Mahmoud"** — a friendly, knowledgeable Jordan tourism guide. The system prompt includes:
- Name: Abu Mahmoud (أبو محمود)
- Personality: warm, helpful, loves sharing stories about Jordan
- Knowledge: all Jordan tourist sites, food, culture, customs, travel tips
- Response rules: max 3-4 sentences, use emojis, can speak Arabic if asked
- The chat tries `gemini-2.5-flash` first, falls back to `gemini-2.0-flash`
- Temperature: 0.8, maxOutputTokens: 800

### Places Data — All 13 (Current Image URLs)

| Place | ID | Image Photo ID |
|-------|----|---------------|
| Petra | `petra` | `photo-1575650693902-8ead804c0732` |
| Wadi Rum | `wadi-rum` | `photo-1547234935-80c7145ec969` |
| Dead Sea | `dead-sea` | `photo-1635686900258-b85e14a988d0` |
| Jerash | `jerash` | `photo-1671653250785-8e1c8e8f1148` |
| Amman Citadel | `amman-citadel` | `photo-1563656157432-67560011e209` |
| Aqaba | `aqaba` | `photo-1682687220742-aba13b6e50ba` |
| Ajloun Castle | `ajloun-castle` | `photo-1694617438458-3642fdcb48cb` |
| Madaba | `madaba` | `photo-1604157886053-0c1c0df2bf5a` |
| Mount Nebo | `mount-nebo` | `photo-1562165000-bb1764d2f423` |
| Dana Nature Reserve | `dana-reserve` | `photo-1562164905-61fc1545247e` |
| Karak Castle | `karak-castle` | `photo-1694617260991-c62f58de3321` |
| Roman Theater | `roman-theater` | `photo-1572909391921-09ac8ae3aac0` |
| Umm Qais | `umm-qais` | `photo-1670523551894-b413740d3ff4` |

### CSS Design Tokens (port to Tailwind)

```css
--primary: #e74c3c;
--primary-dark: #c0392b;
--bg-dark: #0a0a14;
--bg-card: #12121e;
--bg-light: #1a1a2e;
--bg-white: #0f0f1a;
--text-dark: #e8e8f0;
--text-body: #a0a0b8;
--text-light: #6a6a82;
--border: rgba(255, 255, 255, 0.08);
--radius: 12px;
--radius-sm: 8px;
--transition: 0.3s ease;
```

---

## ESTIMATED TIMELINE

| Phase | Effort | Dependencies |
|-------|--------|-------------|
| Phase 0 — Setup | 2-3 hours | None |
| Phase 1 — Auth | 3-4 hours | Phase 0 |
| Phase 2 — Database | 2-3 hours | Phase 0 |
| Phase 3 — API v1 | 6-8 hours | Phase 1, 2 |
| Phase 4 — Frontend | 10-14 hours | Phase 1 |
| Phase 5 — Features | 6-8 hours | Phase 3, 4 |
| Phase 6 — AI & Services | 2-3 hours | Phase 3 |
| Phase 7 — Testing & Deploy | 4-6 hours | All |
| **Total** | **~35-50 hours** | |

---

## IMPORTANT NOTES FOR NEXT AGENT

1. **Don't lose the dark theme aesthetic** — the current design is polished and the user likes it. Port it faithfully to Tailwind.
2. **The welcome.php splash page is standalone** — it has its own inline CSS/JS with particle animations. It should remain visually identical.
3. **Abu Mahmoud chat persona must be preserved exactly** — copy the system prompt verbatim from `api/chat.php`.
4. **Bilingual is CORE** — every user-facing string must support EN/AR. Use Laravel's localization system (`lang/en/messages.php`, `lang/ar/messages.php`).
5. **RTL support is critical** — when Arabic is active, the entire layout flips. Tailwind RTL plugin or `[dir="rtl"]` selectors.
6. **The user deploys to Railway (Docker) AND a Linux server at `102.203.200.31:9001`** — the Dockerfile must work on both.
7. **PostgreSQL on Neon requires `sslmode=require`** — configure in `config/database.php`.
8. **The `places.id` is a VARCHAR string like "petra", "wadi-rum"** — NOT an auto-incrementing integer. Eloquent needs `public $incrementing = false; protected $keyType = 'string';`
9. **Admin account: `admin@dalili.jo` / `admin123`** — seed this in `DatabaseSeeder`.
10. **The current hamburger menu has a structural issue** — `btn-lang` is outside `nav-links` causing mobile layout problems. Fix this in the Laravel rebuild.
