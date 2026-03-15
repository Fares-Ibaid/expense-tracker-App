# Expense Tracker App — Architecture & Workflow

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                          BROWSER  (Vue 3 SPA)                               │
│                                                                             │
│  ┌──────────────────────────────────────────────────────────────────────┐   │
│  │  App.vue  ─── single-page shell, controls active view via           │   │
│  │               currentView ref + <Navbar> tab switching              │   │
│  └────────────────────────┬─────────────────────────────────────────────┘   │
│                           │ renders one section at a time                   │
│          ┌────────────────┼────────────────┬──────────────────┐             │
│          ▼                ▼                ▼                  ▼             │
│  ┌───────────────┐ ┌─────────────┐ ┌────────────────┐ ┌────────────────┐   │
│  │DashboardSection│ │ExpensesSect.│ │SettingsPanel   │ │ReportSection   │   │
│  │               │ │             │ │                │ │                │   │
│  │ - Filter bar  │ │ - CsvUpload │ │ - CategoryMgr  │ │ - Chart/graphs │   │
│  │ - Expenses    │ │   .vue      │ │   (CRUD cats)  │ │ - Summary by   │   │
│  │   table       │ │ - Expenses  │ │ - Rule Manager │ │   category     │   │
│  │ - Pagination  │ │   list      │ │   (CRUD rules) │ │                │   │
│  │ - Total amt   │ │             │ │                │ │                │   │
│  └───────┬───────┘ └──────┬──────┘ └───────┬────────┘ └───────┬────────┘   │
│          │                │                │                  │             │
│   useExpenses.js / useDashboardApi.js / useSettingsPanel.js                 │
│          │   (Vue composables - wrap all axios calls)          │             │
└──────────┼────────────────┼────────────────┼──────────────────┼─────────────┘
           │  axios/HTTP    │                │                  │
           ▼                ▼                ▼                  ▼
═══════════════════════════ Laravel API (api.php) ══════════════════════════════

  GET  /api/dashboard                  POST /api/expenses/upload
  GET  /api/expenses/available-months-years
  GET  /api/expenses/summary-by-category
  POST /api/expenses/save
  GET|POST|PUT|DELETE /api/categories
  GET|POST|PUT|DELETE /api/rules
  GET  /api/categories/categorized-counts

═══════════════════════════════════════════════════════════════════════════════

┌──────────────────────────────────────────────────────────────────────────────┐
│                          BACKEND  (Laravel 11)                               │
│                                                                              │
│  ┌──────────────────────────────────────────────────────────────────────┐    │
│  │  DashboardController  GET /api/dashboard                            │    │
│  │                                                                      │    │
│  │  1. Receives optional filters (category, date range, month/year,    │    │
│  │     minAmount/maxAmount, per_page)                                   │    │
│  │  2. Builds Expense query with dynamic WHERE clauses                  │    │
│  │  3. Eager-loads Category relationship                                │    │
│  │  4. Paginates results + calculates SUM(ABS(amount))                  │    │
│  │  5. Returns JSON { expenses, total, count, totalAmount }             │    │
│  └──────────────────────────────────────────────────────────────────────┘    │
│                                                                              │
│  ┌──────────────────────────────────────────────────────────────────────┐    │
│  │  ExpenseUploadController                                             │    │
│  │                                                                      │    │
│  │  POST /api/expenses/upload  ------------------------------------->  │    │
│  │                                                                      │    │
│  │   ┌─────────────────────────────────────────────────────────────┐   │    │
│  │   │  1. Validate: file must be csv/txt                          │   │    │
│  │   │  2. Check if user has Categories & Rules                    │   │    │
│  │   │     └- if none -> seedDefaultCategoriesAndRules()           │   │    │
│  │   │  3. Load existing expenses into memory map                  │   │    │
│  │   │     (date + amount + description) for dedup check           │   │    │
│  │   │  4. processCsv()                                            │   │    │
│  │   │     ├- Read file, convert ISO-8859-1 -> UTF-8               │   │    │
│  │   │     ├- Parse with League\Csv (delimiter=";", header row 0)  │   │    │
│  │   │     └- Returns TabularDataReader of rows                    │   │    │
│  │   │  5. Per row:                                                │   │    │
│  │   │     ├- Extract: Buchungstag, Beguenstigter, Betrag          │   │    │
│  │   │     ├- Fix 2-digit year (e.g. "25" -> "2025")               │   │    │
│  │   │     ├- parseAmount() - European "1.234,56" -> 1234.56       │   │    │
│  │   │     ├- autoCategorize() - match rules (contains/equals/     │   │    │
│  │   │     │   regex) against description -> category name          │   │    │
│  │   │     └- Flag as duplicated if already in DB map              │   │    │
│  │   │  6. Return JSON preview array (not saved yet)               │   │    │
│  │   └─────────────────────────────────────────────────────────────┘   │    │
│  │                                                                      │    │
│  │  POST /api/expenses/save  --------------------------------------->  │    │
│  │                                                                      │    │
│  │   ┌─────────────────────────────────────────────────────────────┐   │    │
│  │   │  1. Validate each expense row                               │   │    │
│  │   │  2. Filter out rows flagged as duplicated                   │   │    │
│  │   │  3. For each non-duplicate:                                 │   │    │
│  │   │     ├- autoCategorize() -> look up Category.id by name      │   │    │
│  │   │     └- Expense::create(...)                                 │   │    │
│  │   │  4. Return 201 success                                      │   │    │
│  │   └─────────────────────────────────────────────────────────────┘   │    │
│  │                                                                      │    │
│  │  GET /api/expenses/summary-by-category                              │    │
│  │     └- JOIN expenses + categories, GROUP BY category name,          │    │
│  │        SUM(ABS(amount)), COUNT(id) -> used for Reports/charts       │    │
│  └──────────────────────────────────────────────────────────────────────┘    │
│                                                                              │
│  ┌──────────────────────────────────────────────────────────────────────┐    │
│  │  CategoryController  CRUD /api/categories                            │    │
│  │  ├- index()   -> Category::with('rules')                             │    │
│  │  ├- store()   -> create new category (unique name)                   │    │
│  │  ├- update()  -> rename category                                     │    │
│  │  ├- destroy() -> delete (blocked if rules exist)                     │    │
│  │  └- getCategorizedCounts() -> categorized vs uncategorized           │    │
│  └──────────────────────────────────────────────────────────────────────┘    │
│                                                                              │
│  ┌──────────────────────────────────────────────────────────────────────┐    │
│  │  RuleController  CRUD /api/rules                                     │    │
│  │  ├- index()   -> Rule::with('category')                               │    │
│  │  ├- store()   -> create rule, then apply to uncategorized expenses    │    │
│  │  ├- update()  -> update rule, re-apply to uncategorized expenses      │    │
│  │  └- destroy() -> delete rule                                          │    │
│  │                                                                      │    │
│  │  matchesRule(): contains | equals | regex                            │    │
│  └──────────────────────────────────────────────────────────────────────┘    │
│                                                                              │
│  ┌──────────────────────────────────────────────────────────────────────┐    │
│  │  ExpenseController                                                   │    │
│  │  └- getAvailableMonthsAndYears() -> distinct YEAR/MONTH pairs       │    │
│  │     used to populate date filter dropdowns                           │    │
│  └──────────────────────────────────────────────────────────────────────┘    │
└──────────────────────────────────────────────────────────────────────────────┘

┌──────────────────────────────────────────────────────────────────────────────┐
│                     DATABASE  (MySQL - expense_tracker)                      │
│                                                                              │
│   users ----------------------------------------------------------------┐    │
│                                                                         │    │
│   categories  <--- has many --- rules                                  │    │
│       │                            │                                   │    │
│       │ has many                   │ belongs to                        │    │
│       ▼                            ▼                                   │    │
│   expenses ------ belongs to --- categories                            │    │
│       │                                                                │    │
│       └--- user_id --------------------------------------------------- ┘    │
└──────────────────────────────────────────────────────────────────────────────┘

══════════════════════════ CSV IMPORT FLOW (end-to-end) ═══════════════════════

  User picks .csv file
        │
        ▼
  CsvUpload.vue  --POST /api/expenses/upload-->  ExpenseUploadController
                                                        │
                                                   processCsv()
                                                        │
                                              ┌─────────▼──────────┐
                                              │  For each row:      │
                                              │  ├- parse date      │
                                              │  ├- parse amount    │
                                              │  ├- autoCategorize  │
                                              │  └- flag duplicate  │
                                              └─────────┬──────────┘
                                                        │
                                              Return preview JSON
                                                        │
        <-----------------------------------------------┘
        │
  User reviews table (new vs duplicate)
        │
        ▼
  User clicks "Save"
        │
  POST /api/expenses/save --> Skip duplicated rows
                              autoCategorize -> Category.id
                              Expense::create() x N
                              Return 201
```

