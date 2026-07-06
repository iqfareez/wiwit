# Using Docker Compose

## Quick Start

Create a `compose.yml` file with the content from [`compose.prod.yml`](../compose.prod.yml).

The production compose file connects the app to an external Docker network named `proxy`. Create it once if it does not already exist:

```bash
docker network create proxy
```

Create a `stack.env` file in the same directory as `compose.yml`, with content from [`.env.example`](../.env.example). At least, update these values:

```env
APP_NAME=Wiwit
APP_ENV=production
APP_KEY=base64:replace-with-your-generated-key
APP_DEBUG=false
APP_URL=https://wiwit.example.com

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=wiwit
DB_USERNAME=wiwit
DB_PASSWORD=replace-with-secure-password
```

Generate `APP_KEY` with:

```bash
php artisan key:generate --show
```

Use the same database name, username, and password in the `db` service environment in `compose.yml`:

```yaml
environment:
    MYSQL_DATABASE: wiwit
    MYSQL_USER: wiwit
    MYSQL_PASSWORD: replace-with-secure-password
    MYSQL_ROOT_PASSWORD: replace-with-secure-root-password
```

Then start the services:

```bash
docker compose -f compose.yml up -d
```

## First Setup

Access the container shell (eg: `docker exec -it wiwit-app-1 sh`) and run the following commands to set up the app.

Run the migrations:

```bash
app php artisan migrate --force
```

Create the Filament admin user:

```bash
app php artisan make:filament-user
```

The app is ready at `https://wiwit.example.com`. The admin panel is at `/admin`.

## Environment Variables

| Variable      | Description                                                                 |
| ------------- | --------------------------------------------------------------------------- |
| `APP_NAME`    | Application name shown by Laravel.                                          |
| `APP_ENV`     | Use `production` for production deployments.                                |
| `APP_KEY`     | Laravel encryption key. Generate it with `php artisan key:generate --show`. |
| `APP_DEBUG`   | Keep this `false` in production.                                            |
| `APP_URL`     | Public URL of the application.                                              |
| `DB_HOST`     | Database service host. Use `db` for the included MySQL service.             |
| `DB_DATABASE` | MySQL database name. Must match `MYSQL_DATABASE`.                           |
| `DB_USERNAME` | MySQL username. Must match `MYSQL_USER`.                                    |
| `DB_PASSWORD` | MySQL password. Must match `MYSQL_PASSWORD`.                                |

Additional variables supported by the image are documented here: https://serversideup.net/open-source/docker-php/docs/reference/environment-variable-specification.

## Reverse Proxy

`compose.prod.yml` assumes the app is behind a reverse proxy on the external `proxy` network. The app listens on port `8080` inside the container.

The compose file already sets:

```env
ENABLE_TRUSTED_PROXY_CONFIG=true
TRUSTED_PROXIES=*
```

Example Caddyfile:

```caddyfile
wiwit.example.com {
    reverse_proxy wiwit-app-1:8080 {
        header_up X-Forwarded-Proto {scheme}
        header_up X-Forwarded-Host {host}
        header_up X-Forwarded-For {remote_host}
        header_up X-Real-IP {remote_host}
    }
}
```

You can verify proxy headers at:

```text
https://wiwit.example.com/_debug/proxy-headers
```

## Optional phpMyAdmin

`compose.prod.yml` includes a commented phpMyAdmin service. Uncomment it only when needed, then protect it behind your own network or proxy rules before exposing it publicly.

## References

- ServerSideUp PHP image defaults: https://serversideup.net/open-source/docker-php/docs/getting-started/default-configurations
- MySQL Docker environment variables: https://hub.docker.com/_/mysql#environment-variables
