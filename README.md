# BTH Catalog — Junior Full-Stack test task

Product catalog with an admin panel built against the spec for a Junior
Full-Stack Developer position (Laravel + Vue.js).

- **Backend:** Laravel 13 (RESTful API), PostgreSQL 17, Sanctum tokens
- **Frontend:** Vue 3 (Composition API, TypeScript) + Inertia, Pinia,
  Tailwind CSS, vue-i18n
- **Extras:** Docker Compose for Postgres, soft deletes, seeders, feature
  tests, composables, delete confirmation modal, debounced search,
  dark mode, RU/EN i18n, URL-synced filters, cached state

---

## Quick start

```bash
# 1. PHP deps
composer install

# 2. JS deps
npm install

# 3. Env
cp .env.example .env
php artisan key:generate

# 4. Postgres in Docker
docker compose up -d

# 5. Schema + realistic demo data (1 admin, 8 categories, 42 products)
php artisan migrate --seed

# 6. Dev (in two terminals)
npm run dev                     # Vite HMR on :5173
php artisan serve --port=8000   # or open http://<folder>.test with Herd
```

Open `http://localhost:8000` (or the Herd domain). Demo admin credentials
are shown on the login screen — `admin@admin.test` / `password`.

Full setup notes, WebStorm config, and convention docs live in
[`SETUP.md`](./SETUP.md).

---

## API

Public:

| Method | Route | Notes |
|--------|-------|-------|
| `GET`  | `/api/categories` | All categories, no pagination |
| `GET`  | `/api/products`   | `?page`, `?per_page` (≤50), `?category_id`, `?search` |
| `GET`  | `/api/products/{id}` | 404 via route model binding |
| `POST` | `/api/login`      | Returns `{ token, user }`. Rate-limited (6/min) |

Protected (`auth:sanctum` + `Authorization: Bearer <token>`):

| Method | Route | Returns |
|--------|-------|---------|
| `POST`  | `/api/logout` | 204 |
| `GET`   | `/api/me`     | Current user |
| `POST`  | `/api/products` | 201 + `ProductResource` |
| `PUT/PATCH` | `/api/products/{id}` | 200 + `ProductResource` |
| `DELETE` | `/api/products/{id}` | 204 (soft delete) |

Validation on `store`/`update`:

- `name` — required string, max 255
- `price` — required numeric, greater than 0
- `category_id` — required integer, must exist in `categories`
- `name_en`, `description`, `description_en`, `image_url` — optional
  (image_url must be a URL if present)

Responses use `App\Http\Resources\ProductResource` /
`CategoryResource`; list endpoints eager-load `category` to avoid N+1.

---

## Frontend pages

| Route | Page | Layout |
|-------|------|--------|
| `/` | `HomePage` — catalog with pagination, category filter, debounced search | `public-layout` |
| `/product/{id}` | `ProductDetailPage` — detail view | `public-layout` |
| `/login` | `LoginPage` — calls `/api/login`, stores token in `localStorage` | — (self-contained) |
| `/admin/products` | `ProductListPage` — table with Edit/Delete/Add | `admin-layout` |
| `/admin/products/create` | `ProductFormPage` — new product (bilingual name/description + image URL) | `admin-layout` |
| `/admin/products/{id}/edit` | `ProductFormPage` — edit existing | `admin-layout` |

Filter / page / search state is mirrored into the URL via
`history.replaceState` so reload and back-navigation keep the view.

---

## Source layout

```
app/
├── Http/
│   ├── Controllers/Api/  (AuthController, ProductController, CategoryController)
│   ├── Middleware/       (HandleInertiaRequests)
│   ├── Requests/Api/     (LoginRequest, StoreProductRequest, UpdateProductRequest)
│   └── Resources/        (ProductResource, CategoryResource)
└── Models/               (User, Category, Product)

database/
├── factories/ ├── migrations/ └── seeders/  (8 categories, 42 curated products)

resources/js/
├── core/     # app entry, axios client, i18n, theme, shared types
├── common/   # dumb shared UI — components / layouts / helpers / composables
├── modules/  # feature-based: auth, categories, products, ...
│   └── <module>/
│       ├── components/  composables/  models/  services/  store/
│       └── index.ts     (module's public API)
└── pages/    # Inertia pages (PascalCase *Page.vue)

routes/
├── api.php   # /api/* endpoints
└── web.php   # Inertia page shells
```

Strict module boundaries: nothing outside a module may reach into its
`services/` or `helpers/` — cross-module imports go through the
module's `index.ts`.

---

## Commands

| Command | Purpose |
|---------|---------|
| `npm run dev` | Vite dev server |
| `npm run build` | Production build |
| `npm run lint` | ESLint with auto-fix |
| `npm run format` | Prettier write |
| `npm run type-check` | `vue-tsc` |
| `./vendor/bin/pint` | PHP code style (Laravel Pint) |
| `php artisan test` | PHPUnit feature tests (17 / 63 assertions) |
| `php artisan migrate:fresh --seed` | Reset DB with demo data |

---

## What's implemented (spec checklist)

Core:
- ✅ Laravel 10+ REST API (using 13)
- ✅ Vue 3 Composition API + Inertia + Tailwind
- ✅ PostgreSQL
- ✅ Models `Product` & `Category` with required columns and `belongsTo` / `hasMany`
- ✅ All five product endpoints + `GET /api/categories`
- ✅ Resource controllers + Form Request validation
- ✅ Sanctum token auth; `GET` routes public, writes require auth
- ✅ Public `/`, `/product/{id}`; category dropdown with AJAX
- ✅ Admin `/login` with token in localStorage
- ✅ Admin `/admin/products` with Edit / Delete / Add buttons
- ✅ Admin form for create/edit with required fields + frontend validation
- ✅ Redirect to admin list after a successful save

Evaluation bonuses:
- ✅ Eloquent with `with('category')` eager loading
- ✅ Clean split: Controller / Model / FormRequest / Resource
- ✅ Accurate HTTP codes (200 / 201 / 204 / 401 / 404 / 422 / 429)
- ✅ Composition API (`ref`, `reactive`, `computed`, `watch`, `watchEffect`)
- ✅ Global state (Pinia) for auth, products cache, categories cache
- ✅ API error handling (422 validation, generic errors, 401 auto-logout)

Optional bonuses the spec listed:
- ✅ Docker & docker-compose (Postgres)
- ✅ Soft Deletes on `Product`
- ✅ Seeders (8 categories, 42 real-world products with images)
- ✅ Feature tests (17 tests / 63 assertions, `php artisan test`)
- ✅ Composables: `useProductsApi`, `useCategories`, `useAuth`,
  `useDebouncedRef`, `useLocalized`
- ✅ Responsive UI (Tailwind, mobile-first, dark/light theme toggle,
  EN/RU toggle)
- ✅ Delete confirmation modal
- ✅ Debounced search (400 ms) with server-side `LIKE` filter
