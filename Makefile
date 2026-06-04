# Task Management System — Docker Commands

## ─────────────────────────────────────────
##  SETUP
## ─────────────────────────────────────────

setup: ## Full first-time setup: build, start, configure
	cp .env.docker .env
	docker-compose up -d --build
	@echo "Waiting for containers to fully start..."
	sleep 10
	docker exec taskmanager_app php artisan key:generate --force
	docker exec taskmanager_app php artisan migrate --force
	docker exec taskmanager_app php artisan db:seed --force
	@echo ""
	@echo "✅ App is ready at http://localhost:8000"
	@echo "   Admin: admin@example.com / password"
	@echo "   User:  user@example.com  / password"

## ─────────────────────────────────────────
##  DOCKER
## ─────────────────────────────────────────

build: ## Build Docker images
	docker-compose build

up: ## Start all containers
	docker-compose up -d

down: ## Stop all containers
	docker-compose down

restart: ## Restart all containers
	docker-compose restart

rebuild: ## Rebuild and restart everything
	docker-compose down
	docker-compose up -d --build

logs: ## Show container logs (live)
	docker-compose logs -f

logs-app: ## Show app container logs only
	docker-compose logs -f app

ps: ## Show running containers
	docker-compose ps

## ─────────────────────────────────────────
##  LARAVEL
## ─────────────────────────────────────────

migrate: ## Run database migrations
	docker exec taskmanager_app php artisan migrate --force

migrate-fresh: ## Fresh migrate + seed
	docker exec taskmanager_app php artisan migrate:fresh --seed --force

seed: ## Run database seeders
	docker exec taskmanager_app php artisan db:seed --force

key: ## Generate app key
	docker exec taskmanager_app php artisan key:generate --force

cache-clear: ## Clear all caches
	docker exec taskmanager_app php artisan config:clear
	docker exec taskmanager_app php artisan cache:clear
	docker exec taskmanager_app php artisan view:clear
	docker exec taskmanager_app php artisan route:clear

queue: ## Start queue worker (for AI jobs)
	docker exec taskmanager_app php artisan queue:work --sleep=3 --tries=3

## ─────────────────────────────────────────
##  TESTING
## ─────────────────────────────────────────

test: ## Run all feature tests
	docker exec taskmanager_app php artisan test

test-task: ## Run only TaskTest
	docker exec taskmanager_app php artisan test tests/Feature/TaskTest.php

## ─────────────────────────────────────────
##  DATABASE
## ─────────────────────────────────────────

db: ## Access MySQL shell
	docker exec -it taskmanager_db mysql -u taskuser -psecret task_management

db-root: ## Access MySQL as root
	docker exec -it taskmanager_db mysql -u root -psecret

## ─────────────────────────────────────────
##  SHELL ACCESS
## ─────────────────────────────────────────

shell: ## Enter app container shell
	docker exec -it taskmanager_app sh

shell-nginx: ## Enter nginx container shell
	docker exec -it taskmanager_nginx sh

tinker: ## Open Laravel Tinker
	docker exec -it taskmanager_app php artisan tinker

## ─────────────────────────────────────────
##  CLEANUP
## ─────────────────────────────────────────

clean: ## Stop containers and remove volumes
	docker-compose down -v

clean-all: ## Remove everything including images
	docker-compose down -v --rmi all

help: ## Show this help
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | \
		awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-18s\033[0m %s\n", $$1, $$2}'

.DEFAULT_GOAL := help
