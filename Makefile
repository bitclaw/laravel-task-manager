ifneq (,$(wildcard ./.env))
include .env
export
endif

# ============================================================================
# Laravel Task Manager - Quick Start
# ============================================================================
#
# First-time setup:
#   1. make env.setup      # copies .env.example -> .env
#   2. Edit .env if port 8000 or 3306 conflict locally
#      - APP_PORT=8080
#      - FORWARD_DB_PORT=3307
#   3. make build          # builds the PHP 8.3 FPM app image
#   4. make up.d           # starts Nginx + PHP-FPM + MySQL in the background
#   5. make key.generate   # writes APP_KEY to .env
#   6. make migrate        # runs database migrations
#
# App:      http://localhost:8000
# MySQL:    localhost:3306
#
# Day-to-day:
#   make up.d
#   make logs.app
#   make sh
#   make tinker
#   make artisan cmd="about"
#   make routes
#   make migrate
#   make test
#   make pint
#   make stop
#   make reset
#
# ============================================================================

DC = docker compose
EXEC = $(DC) exec app
APP = $(EXEC) php artisan

env.setup:
	cp .env.example .env

build:
	$(DC) build

up:
	$(DC) up

up.d:
	$(DC) up -d

stop:
	$(DC) down

restart:
	$(DC) down
	$(DC) up -d

logs:
	$(DC) logs -f

logs.app:
	$(DC) logs -f app

logs.nginx:
	$(DC) logs -f nginx

logs.db:
	$(DC) logs -f mysql

ps:
	$(DC) ps

composer.install:
	$(EXEC) composer install

composer.update:
	$(EXEC) composer update

key.generate:
	$(APP) key:generate

# Laravel commands
artisan:
	$(APP) $(cmd)

migrate:
	$(APP) migrate

migrate.fresh:
	$(APP) migrate:fresh --seed

migrate.rollback:
	$(APP) migrate:rollback

migrate.status:
	$(APP) migrate:status

seed:
	$(APP) db:seed

tinker:
	$(APP) tinker

routes:
	$(APP) route:list

routes.api:
	$(APP) route:list --path=api

cache.clear:
	$(APP) cache:clear
	$(APP) config:clear
	$(APP) route:clear
	$(APP) view:clear

# Code quality
test:
	$(APP) test

test.filter:
	$(APP) test --filter=$(filter)

pint:
	$(EXEC) ./vendor/bin/pint

pint.check:
	$(EXEC) ./vendor/bin/pint --test

# Shell access
sh:
	$(EXEC) bash

sh.db:
	$(DC) exec mysql mysql -u$(MYSQL_USER) -p$(MYSQL_PASSWORD) $(MYSQL_DATABASE)

# Useful for exploring the codebase
models:
	$(APP) model:show $(model)

reset:
	$(DC) down -v
