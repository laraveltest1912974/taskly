# Taskly

Taskly je ToDo aplikacija izrađena u Laravelu, kao projekat za učenje. Podržava više tipova korisnika, admin dashboard, CRUD nad zadacima i prijavu preko Googlea i Facebooka. Interfejs je dvojezičan (EN/SR), a aplikacija radi i kao PWA.

**Live:** https://taskly-olux.onrender.com (Render Free: servis se uspava nakon ~15 min neaktivnosti, pa prvi zahtjev traje ~50 s)

## Funkcionalnosti

- Registracija, prijava, reset lozinke i profil (Laravel Breeze)
- Prijava preko Googlea i Facebooka (Socialite); nalog se povezuje po emailu
- CRUD nad zadacima: naslov, opis, rok, status i prioritet; svaki korisnik vidi samo svoje zadatke
- Uloge `user` i `admin`; admin na `/admin/dashboard` vidi statistike i sve korisnike i zadatke
- Dvojezičan interfejs (EN/SR) sa prebacivanjem jezika
- Tamna tema sa sidebar navigacijom, prilagođena mobilnom prikazu
- PWA: manifest, service worker i offline stranica
- Stranica `/tutorial` sa GIF snimcima svih funkcionalnosti
- Javna stranica `/privacy`

## Tehnologije

Laravel 13, PHP 8.5, MySQL, Blade, Tailwind CSS v4, Alpine.js, Vite, PHPUnit. Lokalni razvoj ide kroz Laravel Sail (Docker). Produkcija je na Renderu (Docker) sa TiDB Cloud bazom.

## Lokalno pokretanje

Potrebni su Docker i Composer.

```bash
cp .env.example .env
composer install
vendor/bin/sail up -d
vendor/bin/sail artisan key:generate
vendor/bin/sail artisan migrate --seed
vendor/bin/sail npm install
vendor/bin/sail npm run dev
```

Zadani portovi u `.env.example` mogu biti zauzeti. U razvoju su korišćeni `APP_PORT=8010`, `FORWARD_DB_PORT=3310`, `FORWARD_REDIS_PORT=6310`, `FORWARD_MAILPIT_PORT=1125` i `FORWARD_MAILPIT_DASHBOARD_PORT=8125`.

| Servis | Adresa |
| --- | --- |
| Aplikacija | http://localhost:8010 |
| Mailpit (test mejlovi) | http://localhost:8125 |
| Vite dev server | http://localhost:5173 |

### Test nalozi (seed)

| Email | Lozinka | Uloga |
| --- | --- | --- |
| `test@example.com` | `password` | user |
| `admin@example.com` | `password` | admin |

Ovi nalozi služe samo za lokalni razvoj.

### Social login

Za Google i Facebook prijavu popuni `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`, `FACEBOOK_CLIENT_ID` i `FACEBOOK_CLIENT_SECRET` u `.env`. Callback adrese su `/auth/google/callback` i `/auth/facebook/callback`, a moraju se poklapati sa onima podešenim kod provajdera.

## Testovi i formatiranje

```bash
vendor/bin/sail artisan test --compact
vendor/bin/sail bin pint --format agent
```

## Struktura

- `app/Http/Controllers`: `TaskController`, `Admin/DashboardController`, `Auth/SocialLoginController`
- `app/Policies/TaskPolicy.php`: autorizacija zadataka (admin ima pristup svemu)
- `app/TaskStatus.php`, `app/TaskPriority.php`, `app/UserRole.php`: enumi sa `label()` metodama za prevod
- `lang/`: prevodi (`sr.json` i `lang/sr/*.php`)
- `resources/views`: Blade prikazi i komponente
- `public/sw.js`, `public/manifest.webmanifest`, `public/offline.html`: PWA
- `Dockerfile`, `deploy/`, `render.yaml`: produkcioni deploy na Render

## Deploy (Render + TiDB Cloud)

Render preko `render.yaml` (Blueprint) gradi Docker image iz `Dockerfile` i sam pokreće deploy pri svakom pushu na `main`. Pri startu `deploy/start.sh` pokreće migracije i keširanje konfiguracije. Secreti (`APP_KEY`, podaci za bazu, OAuth i SMTP kredencijali) upisuju se ručno u Render dashboard i ne ulaze u repo.

Napomene za Render Free:
- Baza je spoljna (TiDB Cloud, port 4000, obavezan TLS preko `MYSQL_ATTR_SSL_CA`).
- Nema queue workera ni crona, pa je `QUEUE_CONNECTION=sync`, a sesije i keš su u bazi.
- Mejlovi idu preko Brevo SMTP na portu 2525, jer su 587, 465 i 25 blokirani.
- `APP_URL` mora biti `https://...`, a `trustProxies` je podešen u `bootstrap/app.php`.

## Plan

Faza 8 (Livewire, Inertia, REST API sa Sanctum, Filament varijante) je odložena. Sljedeći prioritet su AWS postavka za pravu aplikaciju i mobilna aplikacija.

Detaljne bilješke o razvoju i odlukama su u `.claude/laravel-todolist-session.md`.
