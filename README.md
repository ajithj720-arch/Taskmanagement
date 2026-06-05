# AI-Assisted Task Management System

## About Project

This is a premium, production-ready AI-Assisted Task Management System built using **Laravel 11**, **Blade**, **Tailwind CSS**, and **MySQL**. It demonstrates advanced web application concepts, clean architecture, and modern AI integration — following the Repository Pattern, Service Layer, and strict separation of concerns.

### Core Architecture

This project is built utilizing robust software design patterns to ensure maintainability, testability, and decoupled logic:

- **Service Layer & Repository Pattern:** Business logic is abstracted into dedicated Services (`TaskService`, `AIService`), while all data access is handled exclusively through Repositories (`TaskRepositoryInterface`, `Eloquent/TaskRepository`). Controllers only handle HTTP request validation and response formatting — no direct Eloquent calls.

- **Form Request Validation Architecture:** Extensive use of strict, modular Form Requests (`StoreTaskRequest`, `UpdateTaskRequest`) that completely separate validation rules from Controller logic, keeping controllers clean and focused.

- **Repository Caching:** Implements intelligent caching with graceful degradation. `TaskRepository` caches list (`all()`) and single fetch (`find()`) queries. Write operations (`create`, `update`, `delete`) automatically invalidate the cache to ensure fresh data delivery without performance penalties.

- **Fine-grained RBAC Policy System:** Resources are protected dynamically using Laravel Policies (`TaskPolicy`). Admins retain full CRUD privileges, while regular Users are restricted to viewing and updating statuses on assigned tasks only.

- **Sanctum-Protected REST APIs:** Cross-platform integration ready via `/api/tasks` endpoints, secured with `auth:sanctum` middleware and returning standardised API Resources.

---

### AI Integration (OpenAI GPT-3.5)

The system leverages the **OpenAI API** for automated intelligent task insights:

- **Asynchronous Queue Workers:** The `GenerateAISummary` job is dispatched onto a database queue the moment a task is created. This keeps the HTTP response instant — AI processing happens entirely in the background.

- **Graceful Retries & Fallbacks:** The job is configured with `$tries = 3` and `$backoff = 30` seconds. If the OpenAI API is unreachable or returns an error, the `AIService` automatically falls back to a meaningful mock response, preventing any UI breakage.

- **Mock Mode:** Set `AI_MOCK=true` in your `.env` to run the full AI flow without consuming OpenAI credits — ideal for development and testing.

- **Insight Extraction:** The `AIService` constructs a structured prompt from the task title, description, current priority, status, and due date — instructing GPT-3.5-turbo to return a strict JSON payload containing a concise `summary` and an AI-recommended `priority` level.

**AI Prompt Template:**
```
Analyze this task and provide a brief summary and suggested priority.

Task Title: {title}
Description: {description}
Current Priority: {priority}
Status: {status}
Due Date: {due_date}

Respond with JSON: {"summary": "2-3 sentence summary", "priority": "low|medium|high"}
```

> ⚠️ AI is **never** called directly from a controller. The call chain is strictly:
> `TaskController` → `TaskService::store()` → `dispatch(GenerateAISummary)` → `AIService::generateSummary()`

---

### Folder Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── TaskController.php          # Web CRUD — delegates to TaskService
│   │   ├── DashboardController.php     # Analytics & stats
│   │   └── Api/
│   │       └── TaskApiController.php   # REST API endpoints
│   ├── Requests/
│   │   ├── StoreTaskRequest.php        # Validation + authorization check
│   │   └── UpdateTaskRequest.php
│   └── Resources/
│       └── TaskResource.php            # API response transformer
├── Models/
│   ├── Task.php                        # Enum casts, scopeFilter
│   └── User.php
├── Repositories/
│   ├── Contracts/
│   │   └── TaskRepositoryInterface.php # Contract — controllers depend on this
│   └── Eloquent/
│       └── TaskRepository.php          # Eloquent implementation + caching
├── Services/
│   ├── TaskService.php                 # Business logic, transactions, job dispatch
│   └── AIService.php                   # Prompt building, API call, mock fallback
├── Jobs/
│   └── GenerateAISummary.php           # Queued: calls AIService, updates task
├── Policies/
│   └── TaskPolicy.php                  # Gate checks: view, create, update, delete
├── Enums/
│   ├── TaskPriority.php                # low | medium | high
│   └── TaskStatus.php                  # pending | in_progress | completed
└── Providers/
    ├── AppServiceProvider.php          # Registers TaskPolicy
    └── RepositoryServiceProvider.php   # Binds interface → implementation
```

---

## Setup Instructions

### Requirements

- **Docker Environment (Recommended):** Docker Desktop (includes Docker Compose)
- **Local Environment:** PHP >= 8.2, Composer, Node.js >= 18, NPM, MySQL >= 8.0

---

### Installation & Environment Setup

#### Option 1: Using Docker (Recommended)

**Step 1 — Clone the Repository**
```bash
git clone https://github.com/ajithj720-arch/Taskmanagement.git
cd Taskmanagement
```

**Step 2 — Setup Environment File**
```bash
cp .env.docker .env
```
> This pre-configures `DB_HOST=db`, `DB_USERNAME=taskuser`, `DB_PASSWORD=secret` to match the MySQL container, and `APP_ENV=production` so the app serves pre-built assets without requiring a Vite dev server.

**Step 3 — Build Docker Images**
```bash
docker compose build
```

**Step 4 — Start All Containers**
```bash
docker compose up -d
```

This starts 3 containers:

| Container | Role | Port |
|---|---|---|
| `taskmanager_app` | PHP 8.3-FPM | 9000 (internal) |
| `taskmanager_nginx` | Nginx Web Server | **8000 → 80** |
| `taskmanager_db` | MySQL 8.0 | 3306 |

**Step 5 — Wait for Boot (~15–20 seconds)**

The app container automatically:
- ⏳ Waits for MySQL to be ready
- 🔑 Generates the application key
- 🗄️ Runs database migrations
- 🌱 Seeds demo data
- 🧹 Clears all caches
- ✅ Starts PHP-FPM

Watch progress:
```bash
docker compose logs -f app
```
Wait until you see:
```
==> Starting PHP-FPM...
[...] NOTICE: ready to handle connections
```

**Step 6 — Access the Application**

Navigate to **http://localhost:8000**

---

#### Option 2: Running Locally (Without Docker)

**Step 1 — Setup Environment File**
```bash
cp .env.example .env
```

**Step 2 — Install Dependencies**
```bash
composer install
npm install
```

**Step 3 — Generate Application Key**
```bash
php artisan key:generate
```

**Step 4 — Database Configuration**

Configure your database credentials in the `.env` file:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=task_management
DB_USERNAME=root
DB_PASSWORD=your_password
```

**Step 5 — Run Migrations & Seeders**
```bash
php artisan migrate:fresh --seed
```

**Step 6 — Compile Frontend Assets**
```bash
npm run build
```

---

### Running the Application

**Start the Development Server:**
```bash
# Docker
docker compose up -d

# Local
php artisan serve
```

**Start the Queue Worker (mandatory for AI Summary processing):**
```bash
# Docker
docker compose exec app php artisan queue:work --sleep=3 --tries=3

# Local
php artisan queue:work
```

> ⚠️ The queue worker **must** be running for AI summaries to be generated. Without it, tasks will show "Processing in background queue..." indefinitely.

**Access the Application:**

Navigate to [http://localhost:8000](http://localhost:8000)

| Role | Email | Password |
|---|---|---|
| Admin | `admin@example.com` | `password` |
| Regular User | `user@example.com` | `password` |

---

### Running Tests

To verify all feature and authorization flows:
```bash
# Docker
docker compose exec app php artisan test

# Local
php artisan test

# Specific test file
./vendor/bin/phpunit tests/Feature/TaskTest.php
```

---

## API Endpoints

All API routes require `auth:sanctum` authentication.

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/tasks` | Paginated task list with filters |
| POST | `/api/tasks` | Create a new task |
| PATCH | `/api/tasks/{id}/status` | Update task status |
| GET | `/api/tasks/{id}/ai-summary` | Refresh and return AI summary |

---

## Roles & Permissions

| Action | Admin | Task Creator | Assignee | Other |
|--------|-------|-------------|----------|-------|
| View task list | ✅ | ✅ | ✅ | ✅ |
| View specific task | ✅ | ✅ | ✅ | ❌ |
| Create task | ✅ | ✅ | ✅ | ✅ |
| Edit task | ✅ | ✅ | ❌ | ❌ |
| Delete task | ✅ | ✅ | ❌ | ❌ |
| Update status | ✅ | ✅ | ✅ | ❌ |
| Manage users | ✅ | ❌ | ❌ | ❌ |

---

## Tech Stack

- **Backend:** Laravel 11, PHP 8.3
- **Frontend:** Blade, Tailwind CSS, Chart.js
- **Database:** MySQL 8.0
- **Queue:** Laravel database queue
- **AI:** OpenAI GPT-3.5-turbo (with mock fallback via `AI_MOCK=true`)
- **Auth:** Laravel Breeze (session-based)
- **API Auth:** Laravel Sanctum
- **Containerisation:** Docker (PHP-FPM + Nginx + MySQL)

---

## Common Fixes

### 1. Database Connection Refused
Ensure the MySQL container is running:
```bash
docker compose ps
docker compose up -d db
```
If running locally, verify port `3306` and credentials inside `.env`.

### 2. Queue Jobs Not Processing (AI summaries stuck)
Make sure `queue:work` is actively running:
```bash
docker compose exec app php artisan queue:work
```
Check failed jobs:
```bash
docker compose exec app php artisan queue:failed
```

### 3. Asset Styles Not Displaying
Compile the assets to ensure Tailwind utility styling is fully built:
```bash
npm run build
```

### 4. 502 Bad Gateway (Docker)
The app container is still booting. Wait for PHP-FPM to be ready:
```bash
docker compose logs -f app
```

### 5. Rebuild Everything from Scratch
```bash
docker compose down -v
docker compose build --no-cache
docker compose up -d
```
