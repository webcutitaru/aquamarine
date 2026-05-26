# Aquamarine — site marketing

Site PHP 8+ pentru curățătorie profesională Aquamarine (RO/RU), cu panou admin MySQL.

## Cerințe

- PHP 8.0+ (extensii: PDO MySQL, finfo, mbstring, json)
- MySQL 8+ / MariaDB 10.3+
- Composer

## Setup local (MAMP)

```bash
composer install
cp .env.example .env
# Editați .env cu credențialele MAMP (port 8889, user root)
```

1. Creați baza `aquamarine` în phpMyAdmin
2. Importați `database/schema.sql`
3. Populați date inițiale:

```bash
php database/seed_from_json.php
php database/create_admin.php admin ParolaVoastraSigura
```

4. Deschideți site-ul: `http://localhost:8888/aquamarine/` (sau path-ul MAMP)
5. Admin: `/admin/login.php`

## Configurare (.env)

| Variabilă | Descriere |
|-----------|-----------|
| `DB_HOST` | Host MySQL |
| `DB_PORT` | Port (MAMP: 8889) |
| `DB_NAME` | Nume bază |
| `DB_USER` / `DB_PASS` | Credențiale |
| `MAIL_ENABLED` | `true` / `false` |
| `CONTACT_RECIPIENT_EMAIL` | Destinatar formular contact |

**Nu versionați `.env`.** Pe producție, plasați `.env` în afara `public_html` (ex. `/home/aquamari1/.env`).

## Deploy cPanel

Deploy automat via `.cpanel.yml` (rsync, exclude `.env`, uploads, git).

Pe server, după primul deploy:

1. Creați `.env` în afara `public_html` cu credențialele DB producție
2. Rulați `composer install --no-dev` pe server (sau includeți `vendor/` în deploy)
3. Importați schema + seed dacă e instalare nouă
4. Creați cont admin: `php database/create_admin.php`

## Date

- **Runtime:** toate datele dinamice (prețuri, lead-uri, oferte) sunt în MySQL
- **Seed:** `data/preturi.json` — folosit o singură dată la instalare via `seed_from_json.php`

## Structură

```
includes/       bootstrap, config, DB, i18n, repositories
admin/          panou CRM (lead-uri, prețuri, oferte)
database/       schema SQL + scripturi CLI
lang/ro|ru/     texte pagini
data/           JSON seed + upload-uri contact (blocate HTTP)
```
