# Task Management System

A production-ready, AI-assisted Task Management System built with **Laravel 11** and **Blade**, following clean architecture with the Repository Pattern, Service Layer, and AI integration.

---

## Architecture Overview

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── TaskController.php          # Web CRUD — no direct Eloquent, delegates to TaskService
│   │   ├── DashboardController.php
│   │   └── Api/
│   │       └── TaskApiController.php   # REST API endpoints
│   ├── Requests/
│   │   ├── StoreTaskRequest.php        # Validation + policy check
│   │   └── UpdateTaskRequest.php
│   └── Resources/
│       └── TaskResource.php            # API response transformer
├── Models/
│   ├── Task.php                        # Casts enums, defines scopeFilter
│   └── User.php
├── Repositories/
│   ├── Contracts/
│   │   └── TaskRepositoryInterface.php # Contract — controllers depend on this
│   └── Eloquent/
│       └── TaskRepository.php          # Eloquent implementation + caching
├── Services/
│   ├── TaskService.php                 # Business logic, transactions, job dispatch
│   └── AIService.php                   # AI prompt building, API call, mock fallback
├── Jobs/
│   └── GenerateAISummary.php           # Queued job: calls AIService, updates task
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

## AI Integration

**Class:** `App\Services\AIService`

The `AIService` is exclusively called by `TaskService` (never from a controller). It:

1. Builds a structured prompt containing the task title, description, current priority, status, and due date
2. Calls the OpenAI Chat Completions API (`gpt-3.5-turbo`)
3. Parses the JSON response for `summary` and `priority` fields
4. Falls back to a mock response if `AI_MOCK=true` or if the API call fails

**Prompt template:**
```
Analyze this task and provide a brief summary and suggested priority.

Task Title: {title}
Description: {description}
Current Priority: {priority}
Status: {status}
Due Date: {due_date}

Respond with JSON: {"summary": "2-3 sentence summary", "priority": "low|medium|high"}
```

**Queue flow:** When a task is created, `TaskService::store()` dispatches `GenerateAISummary` job to the database queue. The job calls `AIService::generateSummary()` and updates the task record with `ai_summary` and `ai_priority`.

---

## Setup

```bash
# 1. Clone and install
composer install
cp .env.example .env
php artisan key:generate

# 2. Configure MySQL in .env
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=task_management
# DB_USERNAME=root
# DB_PASSWORD=

# 3. Create the database
mysql -u root -e "CREATE DATABASE task_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 4. Run migrations + seed demo data
php artisan migrate --seed

# 5. Start the server (no npm/build step needed — uses Tailwind CDN)
php artisan serve

# 6. (Optional) Start queue worker for AI background processing
php artisan queue:work
```

**Demo credentials:**
- Admin: `admin@example.com` / `password`
- User: `user@example.com` / `password`

---

## API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/tasks` | Paginated task list with filters |
| POST | `/api/tasks` | Create a new task |
| PATCH | `/api/tasks/{id}/status` | Update task status |
| GET | `/api/tasks/{id}/ai-summary` | Refresh and return AI summary |

All API routes require `auth:sanctum` authentication.

---

## Roles & Policies

| Action | Admin | Owner | Assignee | Other |
|--------|-------|-------|----------|-------|
| View any task list | ✅ | ✅ | ✅ | ✅ |
| View specific task | ✅ | ✅ | ✅ | ❌ |
| Create task | ✅ | ✅ | ✅ | ✅ |
| Edit task | ✅ | ✅ | ❌ | ❌ |
| Delete task | ✅ | ✅ | ❌ | ❌ |
| Update status | ✅ | ✅ | ✅ | ❌ |

---

## Tech Stack

- **Backend:** Laravel 11, PHP 8.2+
- **Frontend:** Blade, Tailwind CSS, Alpine.js, Chart.js
- **Database:** SQLite (dev) / MySQL (production)
- **Queue:** Laravel database queue
- **AI:** OpenAI GPT-3.5 (with mock fallback)
- **Auth:** Laravel Breeze

---

## Running Tests

```bash
php artisan test
# or
./vendor/bin/phpunit tests/Feature/TaskTest.php
```
