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

## Production deployment

These steps assume a fresh Ubuntu server or VPS with a public domain pointed at the box. This project was built for local Docker development, but the application itself can be deployed in a standard Laravel stack with `nginx`, `php-fpm`, and MySQL.

### 1. Install system packages

Install the runtime dependencies:

```bash
sudo apt update
sudo apt install -y nginx mysql-server unzip git curl \
    php8.3 php8.3-cli php8.3-fpm php8.3-mysql php8.3-mbstring \
    php8.3-xml php8.3-curl php8.3-zip php8.3-bcmath
```

Install Composer:

```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

Install Node.js 22 so Vite assets can be built:

```bash
curl -fsSL https://deb.nodesource.com/setup_22.x | sudo -E bash -
sudo apt install -y nodejs
```

### 2. Create the database

Log into MySQL and create a dedicated database and user:

```sql
CREATE DATABASE task_manager CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'task_manager'@'localhost' IDENTIFIED BY 'change-this-password';
GRANT ALL PRIVILEGES ON task_manager.* TO 'task_manager'@'localhost';
FLUSH PRIVILEGES;
```

### 3. Deploy the application code

Clone the repository into a deployment directory, for example:

```bash
cd /var/www
sudo git clone <your-repository-url> laravel-task-manager
sudo chown -R $USER:$USER /var/www/laravel-task-manager
cd /var/www/laravel-task-manager
```

Prepare the environment:

```bash
cp .env.example .env
composer install --no-dev --optimize-autoloader
npm install
npm run build
php artisan key:generate
```

Update `.env` for production:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=task_manager
DB_USERNAME=task_manager
DB_PASSWORD=change-this-password
```

Run the database setup:

```bash
php artisan migrate --force
php artisan db:seed --force
```

Set writable permissions:

```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### 4. Configure PHP-FPM and nginx

Point `nginx` to Laravel's `public/` directory with a server block like this:

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/laravel-task-manager/public;

    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Enable the site and reload services:

```bash
sudo ln -s /etc/nginx/sites-available/laravel-task-manager /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
sudo systemctl restart php8.3-fpm
```

### 5. Enable HTTPS

If the domain is public, install a TLS certificate with Certbot:

```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d your-domain.com
```

### 6. Post-deploy checks

Run these after the site is live:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan test
```

Open the application in the browser and confirm:

- seeded projects and tasks are visible
- the project filter narrows the task list correctly
- drag-and-drop works after the production asset build

## Notes

- Reordering is available when the list is scoped to a single project.
- When viewing all tasks across all projects, the UI disables drag-and-drop because priorities are maintained per project, not globally.
