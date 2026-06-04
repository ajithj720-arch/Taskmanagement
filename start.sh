#!/bin/bash
set -e

echo ""
echo "╔══════════════════════════════════════════╗"
echo "║    Task Management System — Docker       ║"
echo "╚══════════════════════════════════════════╝"
echo ""

# ── Step 1: Copy env file ──────────────────────
echo "▶ Step 1: Setting up environment..."
if [ ! -f .env ]; then
    cp .env.docker .env
    echo "  ✓ .env created from .env.docker"
else
    echo "  ✓ .env already exists, skipping"
fi

# ── Step 2: Build & start containers ──────────
echo ""
echo "▶ Step 2: Building and starting Docker containers..."
docker-compose down --remove-orphans 2>/dev/null || true
docker-compose up -d --build
echo "  ✓ Containers started"

# ── Step 3: Wait for MySQL ─────────────────────
echo ""
echo "▶ Step 3: Waiting for MySQL to be ready..."
until docker exec taskmanager_db mysql -u taskuser -psecret -e "SELECT 1;" 2>/dev/null; do
    echo "  ⏳ MySQL not ready yet, waiting 3s..."
    sleep 3
done
echo "  ✓ MySQL is ready"

# ── Step 4: Generate App Key ───────────────────
echo ""
echo "▶ Step 4: Generating application key..."
docker exec taskmanager_app php artisan key:generate --force
echo "  ✓ App key generated"

# ── Step 5: Run Migrations ─────────────────────
echo ""
echo "▶ Step 5: Running database migrations..."
docker exec taskmanager_app php artisan migrate --force
echo "  ✓ Migrations complete"

# ── Step 6: Seed Database ──────────────────────
echo ""
echo "▶ Step 6: Seeding database with demo data..."
docker exec taskmanager_app php artisan db:seed --force
echo "  ✓ Database seeded"

# ── Step 7: Fix Permissions ────────────────────
echo ""
echo "▶ Step 7: Setting storage permissions..."
docker exec taskmanager_app chmod -R 775 storage bootstrap/cache
docker exec taskmanager_app chown -R www-data:www-data storage bootstrap/cache
echo "  ✓ Permissions set"

# ── Step 8: Clear Caches ──────────────────────
echo ""
echo "▶ Step 8: Clearing caches..."
docker exec taskmanager_app php artisan config:clear
docker exec taskmanager_app php artisan cache:clear
docker exec taskmanager_app php artisan view:clear
echo "  ✓ Caches cleared"

# ── Step 9: Run Tests ──────────────────────────
echo ""
echo "▶ Step 9: Running tests..."
docker exec taskmanager_app php artisan test
echo "  ✓ All tests passed"

# ── Done ───────────────────────────────────────
echo ""
echo "╔══════════════════════════════════════════╗"
echo "║   ✅  Application is ready!              ║"
echo "╠══════════════════════════════════════════╣"
echo "║  🌐 URL    : http://localhost:8000       ║"
echo "║  👤 Admin  : admin@example.com           ║"
echo "║  🔑 Pass   : password                   ║"
echo "║  👤 User   : user@example.com            ║"
echo "║  🔑 Pass   : password                   ║"
echo "╠══════════════════════════════════════════╣"
echo "║  📋 Other Commands:                      ║"
echo "║  make logs     → live container logs     ║"
echo "║  make queue    → start AI queue worker   ║"
echo "║  make shell    → enter app container     ║"
echo "║  make db       → MySQL shell             ║"
echo "║  make down     → stop all containers     ║"
echo "╚══════════════════════════════════════════╝"
echo ""
