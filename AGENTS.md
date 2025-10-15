# Repository Guidelines

## Project Structure & Module Organization
Core bundle code lives in `src/`, with controllers under `src/Controller`, health checks in `src/Service`, helpers in `src/Utils`, and shared data objects in `src/Objects`. Bundle wiring sits in `src/ZuHealthCheckBundle.php` and the lightweight test kernel in `src/Kernel.php`. Public HTTP fixtures belong in `public/`, configuration samples land in `config/` and `docs/`, and executable utilities live in `bin/` and `tools/`. Automated tests mirror the production namespace inside `tests/` (`tests/Controller`, `tests/Service`, etc.)—follow that layout when adding code.

## Build, Test, and Development Commands
Run `composer install` once to pull dependencies. Use `make phpstan` for static analysis and `make phpcs` to apply the Symfony coding standard. Execute `make test` for full PHPUnit coverage; HTML reports are written to `tools/php-unit/coverage`. For focused runs you can call `./vendor/bin/phpunit tests/Service` or similar.

## Coding Style & Naming Conventions
Target PHP 8.3+ syntax with strict types, promoted properties, and enums where practical. Follow the Symfony coding style: four-space indentation, PascalCase class names, camelCase methods, and snake_case configuration keys. One class per file with PSR-4 alignment under the `Zu\HealthCheckBundle` namespace. Let `tools/php-cs-fixer/.php-cs-fixer.php` format changes, and keep service IDs predictable (`zu_health_check.*`) when declaring configuration.

## Testing Guidelines
All behavior changes require PHPUnit coverage via the `symfony/phpunit-bridge`. Place new test cases beside the code they exercise and suffix classes with `Test`. When extending health checks, cover both `/ping` and `/health-check` responses with success and failure scenarios. Aim to maintain or raise current coverage; confirm `make test` passes before opening a pull request.

## Commit & Pull Request Guidelines
Commits follow Conventional Commit prefixes (`fix:`, `feat:`, `chore:`) and imperative summaries, as seen in the Git history. Group related changes and avoid mixing formatting with logic. Pull requests should describe the change, link related issues, list manual verification steps, and include sample responses or screenshots when altering endpoints. Call out configuration impacts (e.g., updates under `config/routes.yaml`) so reviewers can re-test quickly.

## Bundle Integration Notes
When dogfooding in a host Symfony app, register the bundle in `config/bundles.php` and import routes from `@ZuHealthCheckBundle/config/routes.yaml`. Mount this repository as a local Composer path repository for rapid iteration, ensure health-check services remain public or properly tagged, and refresh the host cache after each change (`bin/console cache:clear`).
