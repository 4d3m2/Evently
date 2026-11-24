# Evently

Event discovery and reservation platform built with Symfony 7.4 LTS, PostgreSQL and Bootstrap 5.

## Requirements

- Docker with Compose v2
- Node.js 22+ (only for Prettier and Git hooks)
- Git

## Quick start

```bash
export USER_ID=$(id -u) GROUP_ID=$(id -g)
docker compose up -d --build
docker compose exec php composer install
docker compose exec php php bin/console doctrine:database:create --if-not-exists
docker compose exec php php bin/console doctrine:database:create --env=test --if-not-exists
npm install
```

The application is then available at http://localhost:8080.

## Everyday commands

| Command                                    | Purpose                                 |
| ------------------------------------------ | --------------------------------------- |
| `docker compose exec php composer test`    | Run PHPUnit against the test database   |
| `docker compose exec php composer quality` | Lint, code style check, static analysis |
| `docker compose exec php composer fix`     | Auto-fix PHP code style                 |
| `npm run format`                           | Auto-fix frontend file formatting       |
| `docker compose logs -f php`               | Follow application logs                 |

## Git workflow

- `main` is always stable and deployable; changes reach it only through pull requests with passing CI.
- Work happens on `feature/*` and `fix/*` branches.
- Commit messages follow Conventional Commits (`feat:`, `fix:`, `test:`, `chore:`, `ci:`, `docs:`, `security:`), enforced by a commit-msg hook.
- Git hooks (Lefthook) run quick checks locally; GitHub Actions repeats every check and is the real quality gate.
