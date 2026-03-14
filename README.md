# Source Watcher Board

Web UI for the [Source Watcher](https://github.com/TheCocoTeam/source-watcher) project. It provides a login screen and a **transformations** canvas where you design ETL pipelines by dragging and dropping steps (extractors, transformers, loaders) and connecting them. The board talks to [Source Watcher API](../source-watcher-api) for authentication and will use it for saving/loading pipelines when that is implemented.

## Requirements

- A web server (e.g. Apache, nginx, or PHP built-in server) to serve the files under `html/`.
- [Source Watcher API](../source-watcher-api) running and reachable (e.g. at `http://localhost:8181/api/`). The board’s JavaScript is configured to use that base URL for login, JWT validation, and refresh.

## Running the board

### Option 1: PHP built-in server (development)

From the **board** project directory:

```bash
php -S localhost:8080 -t html
```

Then open http://localhost:8080/ in your browser. You will be redirected to the login page. Ensure the API is running (e.g. on port 8181) and that the API base URL in the board’s JavaScript matches your setup (see below).

### Option 2: Apache / nginx

Point the document root at the `html/` directory of this project. The entry point is `index.php`, which redirects to `login.php`.

### Docker

A `Dockerfile` builds a PHP 8.4 Apache image (aligned with Core/API PHP 8.4). The board is served on **port 8080** so the API can use 8181. It does not include the API or Core; run those separately and configure the board’s API base URL (see below).

From the **board** directory:

```bash
docker compose up -d --build web-server
```

Board: http://localhost:8080/  
For the full command list and context, see the [dev-env README](../README.md) (section “Running the board”).

## API URL configuration

The board’s scripts call the API at fixed URLs, for example:

- `http://localhost:8181/api/v1/credentials` (login)
- `http://localhost:8181/api/v1/jwt` (validate token)
- `http://localhost:8181/api/v1/refresh-token` (refresh tokens)

If your API runs on a different host or port, update these URLs in:

- `html/assets/js/views/login.js`
- `html/assets/js/views/transformations.js`

Cookies for `access_token` and `refresh_token` are set by the API for `localhost`. If the board is served from a different origin (e.g. different port or host), cookie sharing and CORS may need to be configured on the API side.

## Project layout

- `html/` — Web root: PHP entry points and static assets.
  - `index.php` — Redirects to `login.php`.
  - `login.php` — Login form; POSTs to the API credentials endpoint.
  - `transformations.php` — Canvas UI (jsPlumb) for building pipelines.
- `html/assets/` — CSS and JavaScript (jQuery, jQuery UI, jsPlumb, app and view scripts).
- `Dockerfile` — Docker image for serving the board (PHP 8.4 Apache).

## Current limitations

- Designed pipelines are not yet persisted to the API; they exist only in the browser session.
- The board does not “contain” the API or Core; it is a separate front-end that consumes the API.
