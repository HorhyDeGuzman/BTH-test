# BTH-Test — Setup Guide

Product catalog with admin panel — Laravel 13 (REST API + Inertia) + Vue 3 (Composition API, TypeScript) + PostgreSQL.

## Prerequisites

| Tool | Version | Install |
|------|---------|---------|
| **Laravel Herd** | latest | https://herd.laravel.com/windows (ships PHP 8.4 + Composer) |
| **Node.js** | 22+ | https://nodejs.org |
| **Docker Desktop** | latest | https://www.docker.com/products/docker-desktop |
| **WebStorm** | 2024.3+ | https://www.jetbrains.com/webstorm/ |

## First-time setup

```powershell
# 1. Install PHP dependencies
composer install

# 2. Install JS dependencies
npm install

# 3. Copy env file (already done if you cloned after first setup)
# cp .env.example .env
# php artisan key:generate

# 4. Start PostgreSQL
docker compose up -d

# 5. Run migrations
php artisan migrate

# 6. Run Vite + PHP dev servers
npm run dev          # in one terminal
# Access via Herd domain (auto-detected): http://bth-test.test
```

> **Note on serving PHP:** Laravel Herd auto-serves any project in its parked directory at `http://<folder-name>.test`. If you want to use `php artisan serve` instead, make sure Herd is not already handling the domain.

## Useful commands

| Command | What |
|---------|------|
| `npm run dev` | Start Vite dev server (HMR) |
| `npm run build` | Production build |
| `npm run lint` | ESLint with `--fix` |
| `npm run format` | Prettier write |
| `npm run type-check` | `vue-tsc` type check |
| `./vendor/bin/pint` | Format PHP (Laravel Pint) |
| `php artisan migrate` | Run DB migrations |
| `php artisan tinker` | REPL |
| `docker compose up -d` | Start PostgreSQL |
| `docker compose down` | Stop PostgreSQL |

## Project structure

```
BTH-Test/
├── app/                    # Laravel backend (Models, Controllers, etc.)
├── database/               # Migrations, seeders, factories
├── docker-compose.yml      # PostgreSQL service
├── resources/
│   ├── css/
│   └── js/                 # Vue 3 frontend (modular)
│       ├── core/           # App entry, axios client, global types
│       │   ├── app.ts
│       │   ├── bootstrap.ts
│       │   ├── api/
│       │   └── types/
│       ├── common/         # Shared UI — reusable in any module
│       │   ├── components/ # Buttons, inputs, modal, etc.
│       │   └── layouts/    # GuestLayout, AuthenticatedLayout
│       ├── modules/        # Feature modules (auth, profile, products, ...)
│       │   └── <name>/
│       │       ├── components/
│       │       ├── composables/
│       │       ├── models/
│       │       ├── services/
│       │       └── index.ts   ← public API
│       └── pages/          # Inertia pages (PascalCase + "Page" suffix)
│           ├── auth/
│           ├── profile/
│           └── *Page.vue
├── routes/                 # web.php, api.php
└── vite.config.js
```

### Import rules

- **Within a module**: import anything directly
- **From another module**: only via its `index.ts` — `import { Foo } from '@/modules/auth';`
- **From `common/`**: direct file import is OK — `import PrimaryButton from '@/common/components/primary-button.vue';`
- **File naming**: `kebab-case` for `.vue` / `.ts` (except `pages/` which is `PascalCase` + `Page` suffix)
- **Folder naming**: `kebab-case`

## WebStorm setup

Format-on-save is already configured via `.idea/prettier.xml` and `.idea/jsLinters/eslint.xml` — it should just work when you open the project. If not:

1. **Prettier:** `Settings → Languages & Frameworks → JavaScript → Prettier`
   - ✅ Automatic Prettier configuration
   - ✅ Run on save
   - ✅ Run on 'Reformat Code' action
2. **ESLint:** `Settings → Languages & Frameworks → JavaScript → Code Quality Tools → ESLint`
   - Choose *Automatic ESLint configuration*
   - ✅ Run eslint --fix on save
3. **PHP (Laravel Pint):** `Settings → PHP → Quality Tools → Laravel Pint`
   - Configuration: from `./vendor/bin/pint` (auto-detected)
   - Enable "On save" under `Tools → Actions on Save`
4. **PHP interpreter:** `Settings → PHP`
   - Add Herd's PHP: `C:\Users\<you>\.config\herd\bin\php84\php.exe`
5. **Database:** `Database → + → PostgreSQL`
   - Host: `localhost`, Port: `5432`, User: `laravel`, Password: `secret`, DB: `bth_test`
