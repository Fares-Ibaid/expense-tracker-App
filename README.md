# Expense Tracker App

<p align="left">
  <img alt="Laravel" src="https://img.shields.io/badge/Laravel-11-FF2D20?logo=laravel&logoColor=white">
  <img alt="Vue" src="https://img.shields.io/badge/Vue-3-4FC08D?logo=vuedotjs&logoColor=white">
  <img alt="Vite" src="https://img.shields.io/badge/Vite-Frontend-646CFF?logo=vite&logoColor=white">
  <img alt="MySQL" src="https://img.shields.io/badge/MySQL-Database-4479A1?logo=mysql&logoColor=white">
  <img alt="License" src="https://img.shields.io/badge/License-MIT-blue.svg">
</p>

A full-stack expense tracking app built with Laravel + Vue 3 to import bank CSV transactions, auto-categorize them with rules, and analyze spending with dashboards and reports.

- Backend: Laravel (API)
- Frontend: Vue 3 + Vite
- Database: MySQL (`expense_tracker`)

---

## ? Table of Contents

- [? What the project does](#-what-the-project-does)
- [? Main Modules](#-main-modules)
- [? Workflow (High Level)](#-workflow-high-level)
- [?? Data Model (Core)](#?-data-model-core)
- [? API Snapshot](#-api-snapshot)
- [?? Screenshots / Demo](#?-screenshots--demo)
- [? Quick Start](#-quick-start)
- [? Important Paths](#-important-paths)
- [?? Roadmap](#?-roadmap)
- [? Contributing](#-contributing)
- [? Notes](#-notes)
- [? License](#-license)

---

## ? What the project does

- Uploads transaction CSV files and parses them safely
- Detects possible duplicate transactions before saving
- Auto-categorizes expenses using rule matching (`contains`, `equals`, `regex`)
- Lets you manage categories and rules from settings
- Provides dashboard filtering and paginated expense browsing
- Generates report summaries by category

---

## ? Main Modules

### ?? Frontend (Vue SPA)

Main entry is `resources/js/components/App.vue`.

- `DashboardSection` - filtering, list, pagination, totals
- `ExpensesSection` - CSV upload + preview + save
- `SettingsPanel` - categories/rules management
- `ReportSection` - summary/report views

Reusable API/state logic lives in:

- `resources/js/composables/useExpenses.js`
- `resources/js/composables/useDashboardApi.js`
- `resources/js/composables/useSettingsPanel.js`

### ?? Backend API (Laravel Controllers)

- `DashboardController`
  - `GET /api/dashboard`
  - Returns filtered, paginated expenses + totals

- `ExpenseUploadController`
  - `POST /api/expenses/upload`
  - `POST /api/expenses/save`
  - `GET /api/expenses/summary-by-category`
  - Handles CSV parsing, normalization, duplicate checks, auto-categorization, persistence

- `CategoryController`
  - Category CRUD
  - Categorized vs uncategorized counts

- `RuleController`
  - Rule CRUD
  - Re-applies new/updated rules to uncategorized expenses

- `ExpenseController`
  - `GET /api/expenses/available-months-years`
  - Supports date filter UI options

---

## ? Workflow (High Level)

1. User uploads CSV in Expenses view
2. Backend parses rows (date/amount/description)
3. Existing rows are checked for duplicates
4. Rules attempt auto-categorization
5. Preview is returned to frontend
6. User confirms save
7. Non-duplicate rows are inserted into DB
8. Dashboard and reports reflect new totals

For full architecture diagram, see `WORKFLOW.md`.

---

## ?? Data Model (Core)

- `Category` has many `Expense`
- `Category` has many `Rule`
- `Expense` belongs to `Category`
- `Expense`, `Category`, `Rule` include `user_id` fields for user scoping

---

## ? API Snapshot

- `GET /api/dashboard`
- `POST /api/expenses/upload`
- `POST /api/expenses/save`
- `GET /api/expenses/summary-by-category`
- `GET /api/expenses/available-months-years`
- `GET|POST|PUT|DELETE /api/categories`
- `GET|POST|PUT|DELETE /api/rules`
- `GET /api/categories/categorized-counts`

---

## ?? Screenshots / Demo

Add screenshots to `docs/images/` and reference them here.

```md
![Dashboard](docs/images/dashboard.png)
![CSV Upload Preview](docs/images/csv-upload-preview.png)
![Settings - Rules](docs/images/settings-rules.png)
![Reports](docs/images/reports.png)
```

Optional demo GIF:

```md
![Demo](docs/images/demo.gif)
```

---

## ? Quick Start

### 1) Install dependencies

```bash
composer install
npm install
```

### 2) Configure environment

```bash
cp .env.example .env
php artisan key:generate
```

Set DB values in `.env` (MySQL):

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=expense_tracker
DB_USERNAME=root
DB_PASSWORD=
```

### 3) Migrate database

```bash
php artisan migrate
```

### 4) Run app

```bash
php artisan serve
npm run dev
```

---

## ? Important Paths

- `app/Http/Controllers/` - API controllers
- `app/Models/` - Eloquent models
- `routes/api.php` - API routes
- `resources/js/components/` - Vue UI modules
- `resources/js/composables/` - Frontend logic
- `database/migrations/` - Schema definitions

---

## ?? Roadmap

- [ ] Add authentication and user-specific dashboards
- [ ] Add recurring expense detection
- [ ] Add export to CSV/PDF for reports
- [ ] Add monthly budget targets and alerts
- [ ] Add automated tests for CSV import and rule matching

---

## ? Contributing

Contributions are welcome.

1. Fork the repo
2. Create a feature branch
3. Commit your changes
4. Open a pull request

Example:

```bash
git checkout -b feature/improve-rule-matching
git commit -m "Improve rule matching performance"
git push origin feature/improve-rule-matching
```

---

## ? Notes

- The project uses a catch-all web route to serve the SPA shell.
- Most app interactions happen through `/api/*` endpoints.
- Rule quality directly improves auto-categorization accuracy over time.

---

## ? License

This project is licensed under the MIT License.
