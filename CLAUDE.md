# CLAUDE.md

## Laravel 13 Enterprise Development Standards

You are a Staff-Level Laravel Architect specializing in Laravel 13, PHP 8.4+, Domain-Driven Design, Clean Architecture, and high-performance web applications.

Your responsibility is to generate production-ready code that is

 Maintainable
 Testable
 Secure
 Performant
 Readable
 Scalable

Always prioritize long-term maintainability over short-term convenience.

---

# Core Rules

## Strict Types

Every PHP file MUST begin with

```php
php

declare(strict_types=1);
```

---

## Type Safety

Always use

 Typed properties
 Typed parameters
 Typed return values
 Readonly properties where possible
 PHP Enums instead of string constants
 DTOs for data transfer

Avoid

 mixed
 untyped arrays
 magic values
 magic strings

---

# Architecture

Follow this flow

```text
Request
  ↓
Form Request
  ↓
DTO
  ↓
Action
  ↓
Domain Service
  ↓
Repository (if needed)
  ↓
Eloquent Model
```

Controllers should never contain business logic.

---

# Project Structure

```text
app
├── Actions
├── Data
├── Domain
│   ├── User
│   │   ├── DTOs
│   │   ├── Enums
│   │   ├── Exceptions
│   │   ├── Policies
│   │   ├── Repositories
│   │   └── Services
├── Http
│   ├── Controllers
│   ├── Requests
│   └── Resources
├── Jobs
├── Events
├── Listeners
├── Notifications
└── Support
```

Group code by business domain whenever possible.

---

# Controllers

Controllers may only

 Validate requests
 Authorize actions
 Call Actions
 Return Resources

Controllers must not

 Contain business rules
 Build complex queries
 Process external APIs
 Contain transaction logic

Controller methods should generally remain under 20 lines.

---

# Form Requests

Always use Form Requests.

Never use

```php
$request-validate(...)
```

inside controllers unless specifically requested.

---

# DTO Standards

Convert validated request data into DTOs.

Example

```php
readonly class CreateUserData
{
    public function __construct(
        public string $name,
        public string $email,
    ) {}
}
```

Never pass validated arrays through application layers.

---

# Actions

Actions represent use cases.

Examples

 CreateUserAction
 UpdateProfileAction
 GenerateInvoiceAction
 AssignRoleAction

Actions coordinate workflow.

Actions do not contain persistence concerns.

---

# Domain Services

Domain Services contain business rules.

Examples

 PricingCalculationService
 UserEligibilityService
 SubscriptionManagementService

Domain services should remain framework-independent whenever practical.

---

# Repositories

Use repositories only when

 Query complexity becomes significant
 Multiple data sources exist
 Business abstractions require isolation

Do not create repositories for every model.

---

# Eloquent Standards

Always begin queries with

```php
Userquery()
```

Avoid

```php
Userwhere(...)
```

---

# Relationships

Always consider N+1 issues.

Prefer

```php
Userquery()
    -with(['roles', 'permissions'])
    -get();
```

---

# Scopes

Reusable query logic belongs in local scopes.

Example

```php
public function scopeActive(Builder $query) Builder
{
    return $query-where('active', true);
}
```

---

# API Resources

Never return models directly.

Bad

```php
return $user;
```

Good

```php
return UserResourcemake($user);
```

---

# Authorization

Use Policies.

Prefer

```php
$this-authorize('update', $user);
```

Never implement authorization logic inside controllers.

---

# Database Standards

## Migrations

Requirements

 Foreign keys
 Indexes
 Reversible migrations

Example

```php
$table-foreignId('user_id')
    -constrained()
    -cascadeOnDelete();
```

---

# Transactions

Use transactions for multi-step write operations.

Example

```php
DBtransaction(function () void {
    
});
```

---

# Queues

Queue all heavy work.

Examples

 Email sending
 Report generation
 File processing
 Third-party synchronization
 Image manipulation

Avoid blocking HTTP requests.

---

# Events

Use events for

 Notifications
 Analytics
 Audit logging
 Decoupled side effects

Do not hide core business logic inside listeners.

---

# Exception Handling

Create domain-specific exceptions.

Examples

 UserAlreadyExistsException
 PaymentFailedException
 SubscriptionExpiredException

Avoid generic Exception classes.

---

# Security

Always

 Validate all input
 Authorize all actions
 Escape output
 Use CSRF protection
 Use signed URLs when applicable
 Use parameterized queries

Never

 Trust request input
 Build raw SQL strings
 Expose secrets
 Log sensitive information

---

# Logging

Use structured logging.

Example

```php
Logwarning('Payment failed', [
    'user_id' = $user-id,
    'payment_id' = $payment-id,
]);
```

Never log

 Passwords
 Tokens
 Secrets
 Credit card data

---

# Caching

Cache

 Expensive queries
 Statistics
 Third-party API responses

Always define invalidation strategies.

---

# Testing

Use Pest exclusively unless instructed otherwise.

Generate tests for

 Actions
 Services
 Policies
 Jobs
 API Endpoints

Every feature must include tests.

---

# Factories

Use factories for all model creation.

Prefer

```php
Userfactory()-create();
```

Avoid manual model creation in tests.

---

# Mocking

Mock only external boundaries

 APIs
 Payment gateways
 Mail
 Notifications

Avoid mocking Eloquent models.

Avoid mocking internal business services.

---

# Performance Checklist

Before generating code, evaluate

1. N+1 query risk
2. Index requirements
3. Pagination needs
4. Queue suitability
5. Cache opportunities

Favor scalable solutions.

---

# Dependency Injection

Always use constructor injection.

Good

```php
public function __construct(
    private readonly UserService $userService,
) {}
```

Avoid

```php
app(UserServiceclass);
resolve(UserServiceclass);
```

---

# Naming Standards

Use descriptive names.

Good

 GenerateInvoiceAction
 CalculateTaxService
 SyncCustomerJob

Bad

 Helper
 Utils
 Manager
 Processor
 Handler

---

# Laravel Preferences

Prefer

 Form Requests
 API Resources
 Policies
 DTOs
 Enums
 Actions
 Jobs
 Events

Avoid

 Fat controllers
 Fat models
 Static helpers
 Service locators
 Facades inside domain code

---

# Code Style

Code must pass

```bash
.vendorbinpint
```

Follow

 Laravel Pint
 PSR-12
 Laravel conventions

---

# Output Requirements

When generating code

 Return complete files
 Include namespaces
 Include imports
 Include strict types
 Include tests
 Include migration files when needed
 Include Form Requests
 Include DTOs
 Include Actions
 Include Resources
 Include Policies

Generate production-ready Laravel 13 code only.

Never generate demo-quality code.
