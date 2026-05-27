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

**Nu versionați `.env`.** Pe producție, plasați `.env` în rădăcina proiectului sau în afara `public_html` (ex. `/home/aquamari1/.env` — detectat automat).

## Deploy cPanel

Deploy automat via `.cpanel.yml` (rsync, exclude `.env`, uploads, git).

### Fără Composer pe server — încărcare din local (FTP/SFTP/File Manager)

**Composer** nu este „alt fel”: este managerul de dependențe PHP pentru proiect (citește `composer.json`, descarcă pachete în `vendor/`). Pe Mac rulezi local:

```bash
cd /Applications/MAMP/htdocs/aquamarine
/Applications/MAMP/bin/php/php8.3.30/bin/php composer.phar install --no-dev --optimize-autoloader
```

Apoi urci pe hosting **tot proiectul inclusiv folderul `vendor/`** (altfel site-ul nu încarcă `.env`). Nu urca `.env` din dev; pe server pui `/home/aquamari1/.env` cu credențialele DB de producție.

După fiecare cod nou din repo, repetă `composer install --no-dev` dacă s-a schimbat `composer.lock`, apoi reîncarcă fișierele sau doar `vendor/` dacă doar dependențele au fost actualizate.

Pe server, după primul deploy:

1. Creați `/home/aquamari1/.env` (în afara `public_html`) — șablon: variabilele din `.env.example`, cu `DB_HOST=localhost`, `DB_PORT=3306` și credențialele din cPanel → MySQL Databases
2. Dacă **nu** ați încărcat `vendor/` din local (vezi mai sus): `cd ~/public_html && composer install --no-dev`
3. Verificare: `php database/check_setup.php` (exit 0 = OK; exit 2 = lipsește cont admin)
4. Dacă tabelele lipsesc: import `database/schema.sql` în phpMyAdmin
5. Seed doar la instalare nouă (DB goală): `php database/seed_from_json.php`
6. Cont admin: `php database/create_admin.php admin 'ParolaSiguraMin10'`

## Date

- **Runtime:** toate datele dinamice (prețuri, lead-uri, oferte) sunt în MySQL
- **Seed inițial (instalare veche):** `data/preturi.json` — via `seed_from_json.php`
- **Catalog 2026:** `database/seed_prices_2026.sql` — import direct în MySQL (fără JSON)

### Actualizare prețuri 2026 (pe server)

1. Asigurați-vă că există coloanele bilingve: importați `database/migrate_i18n_pricing.sql` (o singură dată, dacă DB e veche)
2. **Backup** recomandat pentru tabelele `price_categories` și `price_items`
3. Import catalog:
   - **phpMyAdmin:** import `database/seed_prices_2026.sql`
   - **CLI:** `php database/seed_prices_2026.php`
4. Verificați site-ul: `/servicii-si-preturi.php` și `/ru/servicii-si-preturi.php`
5. Editări ulterioare din admin: `/admin/preturi.php`

Importul **înlocuiește** catalogul de prețuri existent; lead-urile și ofertele homepage rămân neschimbate.

### Corecții copy RO/RU (fără re-import complet)

Pe un DB deja populat, rulați o dată `database/migrate_copy_feedback_2026.sql` (actualizează titlurile carousel, unități RU `шт.`/`кг`, câteva `service_ru`). Pentru catalog nou, folosiți `seed_prices_2026.sql` deja actualizat.

## Structură

```
includes/       bootstrap, config, DB, i18n, repositories
admin/          panou CRM (lead-uri, prețuri, oferte)
database/       schema SQL + scripturi CLI
lang/ro|ru/     texte pagini
data/           JSON seed + upload-uri contact (blocate HTTP)
```
