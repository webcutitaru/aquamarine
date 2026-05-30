# Aquamarine — site marketing

Site PHP 8+ pentru curățătorie profesională Aquamarine (RO/RU), cu panou admin MySQL.

## Cerințe

- PHP 8.0+ (extensii: PDO MySQL, finfo, mbstring, json)
- MySQL 8+ / MariaDB 10.3+
- Composer
- Node.js 18+ (doar pentru build CSS Tailwind)

## Setup local (MAMP)

```bash
composer install
npm install
npm run build:css
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
| `GTM_ID` | Google Tag Manager (ex. `GTM-XXXXXXX`) — opțional |
| `GA4_ID` | Google Analytics 4 (ex. `G-XXXXXXXX`) — opțional |
| `ALLOW_SETUP_CHECK` | `true` doar temporar — permite `/database/check_setup.php` în browser |

**Nu versionați `.env`.** Pe producție, plasați `.env` **în afara** `public_html` (ex. `/home/aquamari1/.env` — detectat automat). Nu lăsați `.env` în `public_html`.

## Deploy cPanel

[`.cpanel.yml`](.cpanel.yml) — **o linie** cu `&&` (format confirmat pe hosting):

```yaml
export DEPLOYPATH=/home/aquamari1/public_html/ && sh scripts/cpanel-deploy.sh
```

Copierea rulează în [`scripts/cpanel-deploy.sh`](scripts/cpanel-deploy.sh): `cp -R *` (fără `.git` / `node_modules` din repo) + `.htaccess` și `data/.htaccess`. Nu folosiți `cp -Ra .` — copiază `.git` și poate bloca deploy-ul fără mesaj în UI.

Nu folosiți `rsync`, task-uri `git`, `rm`, căi absolute sau YAML pe două linii fără `&&`.

### Local vs `public_html`

| Rămâne local / nu în Git | La deploy (`cp`) |
|--------------------------|------------------|
| `.env`, `.env.local` (șabloane `.env.example` sunt în repo) | Nu sunt în repo → nu se copiază |
| `data/contact_uploads/**` (poze clienți), doar `.gitkeep` în Git | Din repo: doar folder gol; **pozele de pe server nu se șterg** |
| `data/leads.ndjson` | Nu e în repo |
| `node_modules/`, `vendor/` | Dacă lipsesc din repo, nu se copiază |

| Poate ajunge în `public_html` | Acces browser |
|-------------------------------|---------------|
| `database/`, `includes/` (din repo) | Blocat: [.htaccess](.htaccess), [database/.htaccess](database/.htaccess) |

Migrări SQL: rulați din `~/repositories/aquamarine/database/` (SSH), nu prin URL.

**Înainte de deploy:** `npm run build:css` — commit `assets/css/app.css`.

**Pași cPanel:** 1) **Update from Remote** 2) **Deploy HEAD Commit**

**Verificare:** **Last Deployment** = SHA nou (nu rămâne blocat la un commit vechi); View Source homepage — fără `data-carousel-slide-link` după fix slider.

**Dacă deploy tot nu actualizează Last Deployment (fără SSH):** în File Manager copiați manual din `repositories/aquamarine` în `public_html` (aceeași structură de foldere), minim: `index.php`, `assets/js/main.js`, `includes/`, `assets/css/app.css`, `.htaccess`.

**CSS:** după modificări de layout/clase Tailwind:

```bash
npm run build:css
```

### Securitate pe server (obligatoriu)

1. `.env` în `/home/aquamari1/.env`, **nu** în `public_html`
2. Aplicați [`deploy/nginx-snippet.conf`](deploy/nginx-snippet.conf) în cPanel (nginx ignoră parțial `.htaccess`)
3. `ALLOW_SETUP_CHECK=false` în `.env` (implicit)
4. Verificați după deploy:
   - `curl -I https://aquamarine.md/sitemap.php` → 200
   - `curl -I https://aquamarine.md/database/schema.sql` → 403
   - `curl -I https://www.aquamarine.md/` → 301 → `aquamarine.md`

### Go-live checklist

- [ ] `php database/check_setup.php` (CLI) exit 0 pe server
- [ ] Formular contact trimis → lead în admin + email (dacă `MAIL_ENABLED=true`)
- [ ] Sitemap trimis în Google Search Console (`https://aquamarine.md/sitemap.php`)
- [ ] `GTM_ID` sau `GA4_ID` setat în `.env` (opțional)
- [ ] Imagini `assets/images/` prezente pe server (logo, slide-uri oferte)

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
3. Verificare: `php database/check_setup.php` (CLI; exit 0 = OK)
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

Pentru bannerele homepage fără link la click pe imagine, rulați o dată `database/migrate_offers_clear_href.sql` (golește `homepage_offers.href`).

## Structură

```
includes/       bootstrap, config, DB, i18n, repositories
admin/          panou CRM (lead-uri, prețuri, oferte)
assets/css/     app.css (build Tailwind) + app.src.css
database/       schema SQL + scripturi CLI (nu se deploy-ează)
deploy/         snippet nginx pentru producție
lang/ro|ru/     texte pagini
data/           JSON seed + upload-uri contact (blocate HTTP)
```
