<p align="center">
  <img src="public/favicon.svg" width="128" height="128" alt="Wiwit logo">
</p>

# Wiwit - A Finance Tracker App

Try it live at [https://demo-wiwit.iqfareez.com](https://demo-wiwit.iqfareez.com).

Email: `demo-user@example.com`\
Password: `12345678`

## Getting Started

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

Then, navigate to `http://127.0.0.1:8000/dashboard/` in your browser to see the app running.

### Generate OpenAPI Docs

Swagger docs is generated using [Scribe](https://scribe.knuckles.wtf/) package. Update the `APP_URL` in the environment, this will be used to populate the server URL in the swagger doc and do [response calls](https://scribe.knuckles.wtf/nodejs/documenting/responses#response-calls).

To generate docs, run:

```shell
php artisan scribe:generate
```

See also `Scribe::beforeResponseCall` hook in [AppServiceProvider.php](./app/Providers/AppServiceProvider.php).

Scribe will use `scribe.sqlite` to store temporary data when generating docs. This sqlite only contains database schema (no data). To regenerate this file, delete the existing `scribe.sqlite` and run:

```shell
DB_CONNECTION=sqlite DB_DATABASE=database/scribe.sqlite php artisan migrate
```

## Attributions

- App logo/favicons from https://solar-icons.vercel.app/icons?search=wallet&icon=wallet
