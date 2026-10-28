# Hoppy Coin API

Laravel backend for the [Hoppy Coin](https://github.com/sudoist/hoppy-coin) experimental Phaser game. The current API supports score submission and leaderboard retrieval.

## Stack

PHP 8.2+ and Laravel 11 (see `composer.json` for exact dependency constraints). This is a separate application from the browser game.

## Local setup

```sh
composer install
cp .env.example .env
php artisan key:generate
```

Configure a local database and the Hoppy API key in your environment/configuration, then run:

```sh
php artisan migrate
php artisan serve
```

Run tests with `php artisan test`. Existing tests have not been verified as passing during this documentation update.

## API (current implementation)

The routes in `routes/api.php` expose `/api/v1/scores` behind the `with_hoppy_api_key` middleware.

- `GET /api/v1/scores?level=Ranked1` — leaderboard for a level; `level` is required.
- `POST /api/v1/scores` — JSON body with `name` (string, up to 3 characters), `score` (numeric), and `level` (string).

Send `x-api-key` in the request headers. **Important:** a key embedded in the public browser game is not a secret and is not sufficient protection against automated score submissions. Score verification, rate limiting, and response consistency require review. Other resource routes may exist but are not documented as supported operations.

The [API docs repository](https://github.com/sudoist/hoppy-coin-api-docs) contains the Swagger specification.

## Workflow

This is a free passion project, not a commercial service. Use small branches and PRs targeting `master`; no permanent `develop` branch. Keep security checks proportional to risk, especially for score submissions and public endpoints. See [AGENTS.md](AGENTS.md).

## Licensing

The licensing status of original Hoppy Coin API code has not been independently established in this documentation update. Laravel and its dependencies retain their respective licenses; a framework license is not automatically the license of this application.
