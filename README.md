# Wiwit - A Finance Tracker App

Try it live at [https://demo-wiwit.iqfareez.com](https://demo-wiwit.iqfareez.com).

Email: `demo-user@example.com`\
Password: `12345678`

## Getting Started for Developers

Get your machine ready for Laravel development. You can install [Herd](https://herd.laravel.com/) and setup [Laravel](https://laravel.com/docs/12.x/installation#installing-php).

Clone repository, then run the following command to install dependencies and setup other stuff:

```bash
composer setup
```

Run the database migrations. By default, the database is `mysql`. You can change that in the `.env` file. If you don't have yet mysql setup, you can follow this [tutorial](https://iqfareez.com/blog/setup-docker-mysql-phpmyadmin) to setup one.

```bash
php artisan migrate
```

Run the development server:

```bash
composer dev
```

Create Filament admin user:

```bash
php artisan make:filament-user
```

Then, navigate to `http://127.0.0.1:8000/admin/` in your browser to see the app running.
