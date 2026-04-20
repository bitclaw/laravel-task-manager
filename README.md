# Laravel Task Manager

A small Laravel 12 task management application built for local Docker development with MySQL, project-based filtering, and drag-and-drop task reordering.

## Stack

- PHP 8.3
- Laravel 12
- MySQL 8.4
- Nginx + PHP-FPM via Docker Compose
- Vite for frontend assets
- Pest for tests

## Features

- Create, edit, and delete tasks
- Automatic numeric priorities
- Drag-and-drop reordering in the browser
- Projects for grouping tasks
- Project filter to show only tasks for the selected project
- Seeded demo data with multiple tasks per project

## Local setup

### 1. Prepare the environment

```bash
make env.setup
```

If `8000` or `3306` are already in use on your machine, update `.env` before starting the stack:

```env
APP_PORT=8080
FORWARD_DB_PORT=3307
```

### 2. Build and start containers

```bash
make build
make up.d
```

### 3. Install PHP dependencies and generate the app key

If this is your first run, install dependencies inside the running app container and generate the Laravel key:

```bash
make composer.install
make key.generate
```

### 4. Run migrations and seed demo data

```bash
make migrate.fresh
```

This seeds:

- 3 named projects
- multiple tasks per project with sequential priorities
- a few unassigned tasks with their own priority sequence

### 5. Install frontend dependencies

The drag-and-drop reordering UI is powered by the compiled frontend assets, so install Node dependencies once:

```bash
npm install
```

For development, run:

```bash
npm run dev
```

Or build production assets locally with:

```bash
npm run build
```

## Run the app

- App: `http://localhost:8000`
- MySQL: `localhost:3306`

If you changed ports in `.env`, use those values instead.

## Useful commands

```bash
make up.d
make stop
make logs.app
make logs.nginx
make sh
make tinker
make routes
make test
```

## Testing

Run the Pest test suite with:

```bash
make test
```

## Notes

- Reordering is available when the list is scoped to a single project.
- When viewing all tasks across all projects, the UI disables drag-and-drop because priorities are maintained per project, not globally.
