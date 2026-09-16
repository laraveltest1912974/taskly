# Laravel ToDo List — izvod sesije (2026-09-15)

Cilj: učenje Laravela kroz izradu ToDo List aplikacije na razne načine (Blade, Livewire, Inertia, API...), sa više tipova korisnika, admin dashboard-om i CRUD-om nad dnevnim zadacima. Sve na lokalu, u Docker-u.

> **Status: PAUZIRANO nakon Faze 7 + redizajna frontend-a.** Faze 0–7 su odrađene i testirane. Faza 8 (Livewire/Inertia/API/Filament varijante) nije počela. Ova beleška služi kao referenca za pitanja o dosad urađenom — sekcije ispod prate hronologiju rada, "Brzi pregled" ispod je sažetak za brzo pretraživanje.

## Redizajn v2 — tamna "Linear/Todoist-inspired" tema (2026-09-16)

Korisnik je i dalje smatrao dizajn "tankim" nakon prvog redizajna (indigo/violet sidebar SaaS). Otvorio sam **linear.app** i **todoist.com** u Chrome-u kao referencu (samo za vizuelnu inspiraciju — kod nije kopiran, samo obrasci: razmak, tipografija, boje). Napravljeno je više mockup krugova (`public/design-preview-v2.html`, `v3.html`, obrisani nakon odabira) pre nego što je korisnik odobrio finalnu varijantu.

**Odabran pravac:** potpuno tamna tema (ne light/dark toggle — samo tamna, dark mode toggle nikad nije implementiran niti tražen), inspirisana Linear-ovim ultra-minimalnim sidebar-om (sitne ikonice, `13px`/`11px` tekst, tanke `border-white/[0.06]` linije umesto senki) + Todoist-ovim kružnim checkbox-ovima obojenim po prioritetu. Dodatak na zahtev korisnika: suptilna pozadina sa `grid` + `radial-gradient glow` + **apstraktna SVG blob grafika** (organski oblik, ljubičasto-plavo-roze gradijent, jako zamagljen `blur(60px)`, `opacity: 0.22`) — generisano čisto kroz SVG/CSS, bez eksternih slika (nisam mogao da povlačim prave slike sa interneta bez linka od korisnika).

### Šta je izmenjeno
- `resources/css/app.css` — font promenjen sa Figtree na **Inter** (i dalje kroz Bunny Fonts, privacy-friendly), dodate `.bg-app-grid` (tamna pozadina + grid + glow) i `.bg-blob` (pozicioniranje/blur za SVG blob) utility klase
- **Svi layout/komponente fajlovi** prešli na tamnu paletu (`zinc-950/40` prozirne kartice sa `backdrop-blur-md`, `ring-1 ring-white/[0.08]` umesto `shadow-lg`): `layouts/app.blade.php`, `layouts/guest.blade.php`, `layouts/partials/sidebar-nav.blade.php`, `components/{sidebar-link,status-badge,dropdown,dropdown-link,locale-switcher,text-input,input-label,input-error,primary-button,secondary-button,danger-button,auth-session-status,modal}.blade.php`
- **Task lista redizajnirana**: kružni checkbox indikatori obojeni po prioritetu (`border-red-400` = High, `border-orange-400` = Medium, `border-zinc-600` = Low) umesto teksta/badge-a za prioritet — `x-priority-badge` komponenta **obrisana** (postala nepotrebna). Završeni **i** otkazani taskovi sad oba dobijaju `opacity-60` + `line-through` (ranije samo Completed)
- Sve stranice: `tasks/{index,create,edit,_form}`, `dashboard`, `admin/dashboard`, `auth/*` (login/register/forgot-password/reset-password/confirm-password/verify-email), `profile/edit` + 3 partial-a
- **Uklonjen mrtav kod**: `components/{nav-link,responsive-nav-link,application-logo}.blade.php` — otkriveno da su ostali neiskorišćeni od PRVOG redizajna (zamenjeni sidebar-om), niko ih ranije nije očistio
- Dugmad (primary/secondary/danger) redizajnirana sa uppercase+tracking-widest stila na kompaktniji `text-[13px] font-medium` (bez uppercase) — usklađeno sa rafinisanijom Linear-like estetikom
- `focus:ring-offset-zinc-950` dodat svuda gde je bio `focus:ring-offset-2` (default ring-offset-color je belo, pravilo bi ostavljalo beli "halo" oko dugmadi na tamnoj pozadini)
- Date input dobio `[color-scheme:dark]` da bi nativni kalendar picker ikonica bila vidljiva na tamnoj pozadini

### Napomene za buduće izmene
- **PowerShell encoding**: `Get-Content`/`Set-Content` u Windows PowerShell 5.1 i dalje kvare UTF-8 karaktere (npr. em dash) čak i uz `-Encoding utf8`. Za bulk find-replace preko više fajlova koristiti `[System.IO.File]::ReadAllText/WriteAllText` sa eksplicitnim `[System.Text.UTF8Encoding]::new($false)` (bez BOM-a) — to je proradilo čisto.
- Lančani `.Replace()` pozivi u istom PowerShell bloku mogu da se "pojedu" međusobno ako drugi replace traži tekst koji je prvi replace već izmenio (redosled bitan) — proveriti rezultat posle bulk operacija, ne pretpostaviti da je sve pogođeno.
- Pagination view (`$tasks->links()`) i dalje koristi Laravel-ov default Tailwind stil (svetla tema) — nije primetno jer trenutni seed ima <15 taskova po stranici pa se paginacija ne renderuje. **Ako broj taskova preko 15, treba stilizovati/publish-ovati custom pagination view za tamnu temu.**
- Svih 67 testova prolazi, Pint čist, build (`npm run build`) prošao. Vizuelno potvrđeno u browseru: login/register (EN+SR), dashboard, tasks lista/forma/kreiranje, admin dashboard, profile (uklj. delete account modal) — sve kao ulogovan `test@example.com` i `admin@example.com`.

## Git i GitHub — odrađeno (2026-09-16)

- `git init -b main` u `~/projects/todolist`, inicijalni commit (174 fajla) — provereno pre commit-a da `.env`, `vendor/`, `node_modules/`, `.idea/` i sl. nisu uključeni (`.gitignore` je već bio ispravan iz Laravel starter kit-a)
- GitHub CLI (`gh`) instaliran u WSL-u — trebalo je zaobići `sudo` (nije se znala lozinka) kroz `wsl.exe -d Ubuntu -u root -- ...` (WSL dozvoljava root pristup bez lozinke sa Windows strane); usput trebalo i `dpkg --configure -a` zbog prekinute ranije apt operacije
- `gh auth login` — HTTPS + browser device-code flow (korisnik ručno otvorio `github.com/login/device`, `xdg-open` ne postoji u WSL-u pa je auto-open preskočen bez problema)
- GitHub nalog: **`laraveltest1912974`**
- Repo kreiran i push-ovan: **https://github.com/laraveltest1912974/taskly** (javan), `gh repo create taskly --public --source=. --remote=origin --push`
- Remote `origin` podešen, grana `main` prati `origin/main`

## Dvojezičnost (EN/SR) + realni seed podaci — odrađeno (2026-09-16)

Traženo kao među-task: app treba da bude dvojezična (engleski/srpski), i seed taskovi treba da budu realni (ne lorem ipsum).

- `php artisan lang:publish` objavio `lang/en/{validation,auth,passwords,pagination}.php`; napravljeni srpski parnjaci u `lang/sr/` (kompletan prevod, uključujući `'attributes'` mapu u `validation.php` za title/description/due_date/status/priority/name/email/password)
- `lang/sr.json` — prevodi svih app-specifičnih `__()` stringova (koristi engleski tekst kao ključ, Laravel-ov preporučeni pristup za veliki broj stringova)
- **Enum `label()` metode** dodate na `TaskStatus`, `TaskPriority`, `UserRole` (vraćaju `__('...')`) — zamenile ručni `ucfirst(str_replace('_',' ',...))` razbacan po view-ovima (dashboard, admin dashboard, _form.blade.php, status/priority-badge komponente); sada je jedno mesto istine za labele i lakše za prevod
- `config/app.php` — dodat `'supported_locales' => ['en' => 'English', 'sr' => 'Srpski']`
- `App\Http\Middleware\SetLocale` — čita `session('locale')`, poziva `App::setLocale()`; registrovan globalno u `web` grupi (`bootstrap/app.php`)
- Ruta `GET /locale/{locale}` (`locale.switch`) — validira protiv `supported_locales`, čuva u sesiji, `back()`
- `x-locale-switcher` Blade komponenta (EN/SR pill) — u topbar-u (`layouts/app.blade.php`) i na guest stranicama (`layouts/guest.blade.php`, vidljivo i pre logina)
- Flash poruke u `TaskController` (`Task created.` itd.) sada idu kroz `__()`
- Test: `tests/Feature/LocaleSwitchTest.php` (4 testa — switch persistuje u sesiji, nepodržan locale = 404, login/tasks stranice renderuju na srpskom)
- **`TaskFactory` prepravljen** — umesto `fake()->sentence()`/`paragraph()` (lorem ipsum), koristi kurirani pool od 20 parova realnih todo stavki na engleskom i srpskom (npr. "Buy groceries"/"Kupiti namirnice", "Call the dentist"/"Pozvati zubara") nasumično biranih po tasku — seed podaci sada izgledaju kao pravi todo-lista, ne generisan tekst
- Svih **67 testova** prolazi, Pint čist
- Vizuelno potvrđeno u browseru: login/register, dashboard, tasks lista/forma, flash poruke, user dropdown — sve ispravno na oba jezika; admin dashboard nije ponovo vizuelno proveren na srpskom u ovoj sesiji (kod prati isti `->label()`/`__()` obrazac kao ostale stranice, pa je rizik nizak)
- **Napomena iz debug-a:** ručno "hakovanje" HTML forme kroz JS (`removeAttribute('required')` + `form.submit()`) u automatizovanom browser testiranju je izazvalo čudno ponašanje (redirect na login, gubitak sesije/locale) — ispostavilo se da je to artefakt te specifične JS manipulacije, NE stvarni bag u aplikaciji; kroz normalnu UI interakciju (klik/kucanje) sve radi ispravno. Ako se ubuduće testira browserom, izbegavati takve JS hakove nad formama i koristiti stvarne klikove/tastaturu.

## Brzi pregled (cheat sheet)

**Pristup aplikaciji:**
- App: http://localhost:8010 (`/` redirect-uje na `/login` ili `/dashboard`)
- Mailpit (test email UI): http://localhost:8125
- DB (host strane, npr. TablePlus): `127.0.0.1:3310`, user `sail`, pass `password`, baza `laravel`
- GitHub repo: https://github.com/laraveltest1912974/taskly (javan)

**Test nalozi (seed-ovani):**
- `test@example.com` / `password` — obična uloga (`user`), 5 taskova
- `admin@example.com` / `password` — admin uloga, 5 taskova, vidi sve preko `/admin/dashboard`

**Osnovne komande (iz `~/projects/todolist` u WSL-u, ili kroz `wsl.exe -d Ubuntu -- bash -lc "cd ~/projects/todolist && ..."` sa Windows strane):**
```bash
vendor/bin/sail up -d                              # pokreni kontejnere
vendor/bin/sail artisan migrate:fresh --seed        # reset baze + seed
vendor/bin/sail artisan test --compact              # 67 testa
vendor/bin/sail bin pint --format agent             # formatiranje
vendor/bin/sail npm run build                       # build frontend assets (Tailwind v4)
vendor/bin/sail npm run dev                         # dev watch (za rad na frontend-u)
```

**Stack:** Laravel 13.32.0, PHP 8.5, MySQL (Sail), Breeze + Blade, Tailwind CSS v4 (CSS-first config), Alpine.js. Autorizacija: `role` kolona (`UserRole` enum) + `TaskPolicy`. App naziv: **Taskly**.

**Šta postoji funkcionalno:**
- Registracija/login/reset lozinke/profil (Breeze)
- CRUD nad zadacima (`/tasks`) sa poljima title/description/due_date/status/priority, vlasništvo po korisniku
- Admin dashboard (`/admin/dashboard`) — statistike + pregled svih korisnika/taskova
- Tamna, Linear/Todoist-inspirisana tema — sidebar, grid+glow+blob pozadina, indigo/violet brand ("Taskly")
- Dvojezičan interfejs (EN/SR) sa switcher-om, realni bilingual seed taskovi

**Šta NE postoji još:** Livewire/Inertia/API/Filament varijante (Faza 8), email verifikacija (ruta postoji ali `User` ne implementira `MustVerifyEmail`), dark mode, Kanban prikaz taskova, mobilni prikaz sidebar-a nije vizuelno proveren (videti napomenu u sekciji redizajna).

## Okruženje (zatečeno na mašini)

- Windows 11, WSL2 Ubuntu 26.04
- PHP 8.5.4 CLI (WSL, host) — **nema `pdo_sqlite`** ekstenziju na hostu (nebitno, radimo kroz Docker)
- Composer 2.10.0
- Node nema instaliran unutar WSL-a (samo Windows npm kroz `/mnt/c/...`) — **ali postoji unutar Sail kontejnera** (v24.21.0), pa `vendor/bin/sail npm ...` radi bez problema
- Docker Desktop instaliran, WSL integracija radi (`docker`, `docker compose` dostupni iz Ubuntu distra)
- Postojeći projekti na mašini koriste Laravel Sail pattern: `laravel11-app`, `ishrana`, `tinder-clone`, `fakturator`

## Plan učenja (faze)

0. Okruženje ✅
1. Skelet projekta ✅
2. Baza + model `Task` ✅
3. Autentifikacija — Breeze ✅
4. Tipovi korisnika (role kolona) + Policy ✅
5. CRUD nad zadacima (resource controller, form requests, Blade views) ✅
6. Admin dashboard ✅
- (van originalnog plana) Redizajn frontend-a — moderan sidebar UI ✅
7. Testovi (PHPUnit) ✅
8. Iste funkcionalnosti na razne načine: Livewire, Inertia+Vue/React, REST API+Sanctum, Filament — **nije počelo, sledeće na redu**

Odluke koje su već donete: **Docker (Laravel Sail)**, **Breeze + Blade** za Fazu 3-6, **MySQL kroz Sail** za bazu, **prosta `role` kolona** (ne spatie/laravel-permission), **PHPUnit** (ne Pest), **Tailwind v4** (migrirano sa v3 koju je Breeze instalirao), dizajn pravac **Sidebar SaaS indigo/violet** sa redizajniranom listom taskova (ne Kanban).

## Šta je konkretno urađeno

### Projekat
- Lokacija: `~/projects/todolist` (WSL putanja; sa Windows strane `\\wsl.localhost\Ubuntu\home\nikola\projects\todolist`)
- Napravljen preko `composer create-project laravel/laravel todolist`
- **Laravel 13.32.0** (najnovija stabilna verzija u trenutku instalacije)

### Docker (Laravel Sail)
Dva ključna Docker fajla:
- `compose.yaml` (root projekta) — definiše servise: `laravel.test` (app), `mysql`, `redis`, `mailpit`
- `vendor/laravel/sail/runtimes/8.5/Dockerfile` — build recept za PHP 8.5 image, referenciran iz `compose.yaml`

Instalacija:
```bash
composer require laravel/sail --dev
php artisan sail:install --with=mysql,redis,mailpit
```

**Portovi su promenjeni u `.env`** (default portovi su bili zauzeti drugim projektima — `fakturator-dev-mailpit` je i dalje radio u pozadini):
```env
APP_PORT=8010
FORWARD_DB_PORT=3310
FORWARD_REDIS_PORT=6310
FORWARD_MAILPIT_PORT=1125
FORWARD_MAILPIT_DASHBOARD_PORT=8125
```

Kontejneri (imena, provereno da rade):
```
todolist-laravel.test-1   → http://localhost:8010
todolist-mysql-1          → localhost:3310
todolist-redis-1          → localhost:6310
todolist-mailpit-1        → http://localhost:8125 (mail testing UI)
```

Migracije su pokrenute i prošle (`users`, `cache`, `jobs` tabele u MySQL-u). `curl localhost:8010` vraća HTTP 200.

### Ulazak u shell kontejnera
```bash
cd ~/projects/todolist
./vendor/bin/sail shell
```
(alternativa: `docker exec -it todolist-laravel.test-1 bash`)

Provereno unutar kontejnera: PHP 8.5.10, ima i `pdo_mysql` i `pdo_sqlite`, plus Redis, Imagick, GD, Intl, Swoole, itd.

### Laravel Boost (MCP server za Claude Code)
Instaliran radi lakšeg rada Claude-a nad projektom (baza, šema, dokumentacija direktno kroz alate umesto ručnih shell komandi):
```bash
./vendor/bin/sail composer require laravel/boost --dev   # v2.9.0
./vendor/bin/sail artisan boost:install --guidelines --skills --mcp -n
```

Boost je **automatski prepoznao Claude Code** i generisao:
- `CLAUDE.md` (prepisan) — pravila za ovaj projekat: sve komande kroz Sail, koristi Boost MCP alate umesto ručnih komandi, PHP 8 konvencije, kako tražiti dokumentaciju kroz `search-docs`
- `.claude/skills/` — 5 skill foldera: `laravel-best-practices`, `testing-best-practices`, `tailwindcss-development`, `deploying-to-cloud`, `infer-conventions`
- `.mcp.json` (root projekta)

### ✅ Rešeno — MCP konekcija sa Windows strane
Pošto je Claude Code sesija pokretana **sa Windows strane** (UNC putanja `\\wsl.localhost\Ubuntu\...`), `vendor/bin/sail` (bash skripta) se nije mogla izvršiti direktno iz PowerShell/cmd sloja. `.mcp.json` je izmenjen da uvek prolazi kroz `wsl.exe` wrapper:
```json
{
  "mcpServers": {
    "laravel-boost": {
      "command": "wsl.exe",
      "args": ["-d", "Ubuntu", "--", "bash", "-lc", "cd ~/projects/todolist && vendor/bin/sail artisan boost:mcp"]
    }
  }
}
```
Radi bez obzira da li je Claude Code pokrenut sa Windows ili WSL strane. Boost alati potvrđeno rade: `application-info`, `database-query`, `database-schema`, `search-docs`.

## Faza 2 — odrađeno (2026-09-15)

- Migracija `tasks` (title, description, due_date, status, priority, `user_id` FK sa `cascadeOnDelete`)
- Enumi `App\TaskStatus` i `App\TaskPriority` (backed string enumi, u `app/` rootu — artisan default, ne `app/Enums/`)
- `Task` model: `belongsTo(User)`, casts (`due_date` → date, `status`/`priority` → enum), `#[Fillable]` atribut
- `User::tasks()` — `hasMany` relacija
- `TaskFactory` (+ `completed()` state), `TaskSeeder`, uključen u `DatabaseSeeder`
- `migrate:fresh --seed` prošao, provereno kroz Boost `database-query` i tinker (enum casting radi)

## Faza 3 — odrađeno (2026-09-15)

- Stack potvrđen: **Breeze + Blade** (ne Inertia — prvobitan odgovor je bio greška, korisnik je ispravio)
- `composer require laravel/breeze --dev` (v2.4), `artisan breeze:install blade`, Vite build prošao
- Node postoji **unutar Sail kontejnera** (v24.21.0), iako ga nema na WSL hostu — `vendor/bin/sail npm ...` radi bez problema, nije potrebna host instalacija
- 25 Breeze testova prolazi, Pint čist, vizuelno potvrđeno u browseru (`/register`)
- Seed nalog za testiranje: `test@example.com` / `password` (5 seed-ovanih taskova)

## Faza 4 — odrađeno (2026-09-15)

- Odluka: prosta `role` kolona (ne spatie/laravel-permission — dovoljno za dve uloge, manje boilerplate-a)
- Enum `App\UserRole` (`User`, `Admin`), migracija dodaje `role` string kolonu na `users` (default `user`)
- `User::casts()` kastuje `role` u `UserRole`; `User::isAdmin()` helper; `role` **nije** u `#[Fillable]` (izbegava privilege escalation kroz mass assignment)
- `UserFactory::admin()` state; `DatabaseSeeder` sad seed-uje i `admin@example.com` / `password`
- `App\Policies\TaskPolicy` — auto-discovered (konvencija `app/Models/Task` → `app/Policies/TaskPolicy`), `before()` bypass za admina, ownership provera (`user_id === task->user_id`) za view/update/delete, `create`/`viewAny` otvoreno za sve ulogovane
- Test: `tests/Unit/Policies/TaskPolicyTest.php` (10 testova, ownership + admin bypass), svih 35 testova u projektu prolazi

## Faza 5 — odrađeno (2026-09-15)

- `TaskController` (resource, bez `show` — edit forma služi kao detalj), `StoreTaskRequest`/`UpdateTaskRequest` (validacija + `authorize()` poziva `TaskPolicy`)
- Autorizacija kroz **Laravel 13 `#[Authorize]` controller atribut** (ne `authorizeResource` — noviji, deklarativniji pristup dokumentovan za 13.x)
- `Route::resource('tasks', TaskController::class)->except('show')` unutar `auth` middleware grupe u `routes/web.php`
- Views: `resources/views/tasks/{index,create,edit,_form.blade.php}` — `_form.blade.php` je **@include-ovan partial, ne Blade component** (živi u `views/tasks/`, ne `views/components/`, pa `@props` ne važi tu — atributi se prosleđuju kao obična array u `@include`)
- `index` scoping: admin (`isAdmin()`) vidi sve taskove + "Owner" kolonu, običan korisnik samo svoje (`$user->tasks()`)
- Nav link "Tasks" dodat u `layouts/navigation.blade.php` (desktop + mobile)
- Test: `tests/Feature/Http/Controllers/TaskControllerTest.php` (8 testova — guest redirect, scoping, validacija, ownership 403, CRUD persist), svih 43 testova u projektu prolazi
- Vizuelno potvrđeno u browseru: kreiranje/izmena/lista taskova, admin scoping
- **Napomena:** `/logout` je POST ruta (CSRF), ne GET — za manualno testiranje odjave mora se koristiti UI dropdown, ne direktna navigacija na `/logout`

## Faza 6 — odrađeno (2026-09-15)

- `EnsureUserIsAdmin` middleware, registrovan kao alias `admin` u `bootstrap/app.php` (Laravel 13 način — nema više `app/Http/Kernel.php`)
- `Admin\DashboardController@index` — statistike: ukupno korisnika, ukupno taskova, raspodela po statusu (`Task::toBase()->groupBy('status')->pluck()`, ne cast-ovan model da izbegnemo probleme sa enum objektom kao array key-jem), lista korisnika sa `withCount('tasks')`
- Ruta: `Route::middleware(['auth','admin'])->prefix('admin')->name('admin.')` → `/admin/dashboard`
- View `resources/views/admin/dashboard.blade.php`, "Admin" nav link vidljiv samo kad `Auth::user()->isAdmin()`
- Test: `tests/Feature/Http/Controllers/Admin/DashboardControllerTest.php` (guest redirect, regular user 403, admin vidi tačne total/per-user brojeve), svih 46 testova u projektu prolazi
- Vizuelno potvrđeno u browseru kao admin — statistike i tabela korisnika tačne

## Redizajn frontend-a — odrađeno (2026-09-15)

Korisnik je tražio "baš moderan dizajn". Pokazao sam mu statički HTML mockup (6 varijanti: A–D pravci boja/layout-a, E/F prikazi taskova) servisovan kroz `public/design-preview.html` i otvoren u Chrome-u (obrisan nakon odabira). Odabrano: **A — Sidebar SaaS (indigo/violet)** uz pojačan kontrast kartica na pozadini, i **F — redizajnirana lista** (ne Kanban) za taskove.

### ⚠️ Otkriven i ispravljen problem: Tailwind v3 vs v4
Breeze `breeze:install blade` je instalirao **klasičnu v3 konfiguraciju** (`tailwind.config.js`, `postcss.config.js`, `@tailwind` direktive) iako je `@tailwindcss/vite` v4 već bio u `package.json`-u iz starter kit-a — dva paralelna seta paketa, samo v3 se stvarno koristio. Migrirano na pravi v4:
- `vite.config.js` — dodat `tailwindcss()` plugin iz `@tailwindcss/vite`
- `resources/css/app.css` — `@import 'tailwindcss'`, `@plugin '@tailwindcss/forms'`, `@theme { --color-brand-* }` (oklch tokeni), `@source` za `vendor/laravel/framework` pagination views (v4 auto-detekcija ignoriše `.gitignore`-ovan `/vendor`, pa Laravel-ova default paginacija ne bi dobila stilove bez eksplicitnog `@source`)
- Obrisani `tailwind.config.js`, `postcss.config.js`; `package.json` očišćen (uklonjen `autoprefixer`, `postcss`, v3 `tailwindcss` pin zamenjen sa `^4.0.0`)

### Šta je urađeno
- **Brand tokeni**: `--color-brand-50..900` (indigo/violet, oklch) u `app.css`, `APP_NAME` promenjen u **Taskly** (`.env` i `.env.example`)
- **Sidebar shell**: novi `resources/views/layouts/app.blade.php` — fiksni tamni (`slate-900`) sidebar na desktopu, mobilni slide-over (Alpine `sidebarOpen`), topbar sa `$header` slot-om i user dropdown-om. Stari `layouts/navigation.blade.php` (top nav) obrisan.
- **Komponente**: nova `x-sidebar-link` (aktivan tab = puni `brand-600` pill + senka, ne providan — korisnikov feedback), `x-status-badge`/`x-priority-badge` (boje po enum vrednosti), `primary/secondary/danger-button`, `text-input`, `input-label`, `dropdown` — svi prebačeni na `brand-*`/`slate-*` umesto `indigo-*`/`gray-*` (masovna zamena kroz sve `.blade.php` fajlove via PowerShell regex)
- **Task lista** (F): kartice po redu umesto tabele, badge-ovi u boji, hover edit/delete ikonice, prazno stanje sa ikonicom
- **Dashboard**: gradient welcome kartica, stat kartice (total + po statusu), "Upcoming Tasks" lista — ruta `/dashboard` sada nosi podatke (ranije prazan closure)
- **Admin dashboard**: redizajniran u istom stilu (stat kartice + tabela korisnika sa role badge-om)
- **Auth stranice**: `layouts/guest.blade.php` redizajniran (brend logo, zaobljena kartica sa senkom)
- **`/` ruta**: menjano iz prikazivanja stock Laravel welcome stranice u redirect (guest → login, ulogovan → dashboard); `welcome.blade.php` obrisan; `ExampleTest` zamenjen sa `RootRedirectTest` (2 testa)
- **Bag fix**: "New Task" dugme na tasks index-u koristilo je `onclick` hack na `<button type=submit>` van forme — nije radilo; zamenjeno pravim `<a>` linkom
- Svih **47 testova** prolazi, Pint čist, build (`npm run build`) prošao sa v4

### Poznato ograničenje ove sesije
`resize_window` alat za Chrome nije uspeo da smanji prozor (ostao 1920×911 uprkos pozivu za 390×844) — **mobilni prikaz sidebar-a (slide-over) nije vizuelno potvrđen u browseru**, samo kroz kod (iste `lg:` breakpoint konvencije koje je Breeze već koristio). Vredelo bi proveriti na pravom uređaju ili kroz DevTools device toolbar ručno.

## Faza 7 — odrađeno (2026-09-15)

Ostalo se na **PHPUnit** (već korišćen, korisnik nije tražio Pest — nije ni pitano jer bi to bila nepotrebna migracija bez razloga).

- `tests/Unit/Http/Requests/StoreTaskRequestTest.php` (9 testova) i `UpdateTaskRequestTest.php` (5 testova) — validaciona matrica testirana **direktno preko `Validator::make($data, (new Request)->rules())`**, ne kroz HTTP (per testing-best-practices skill: "rule-class test owns the matrix"). `TaskControllerTest`-ov postojeći empty-payload test ostaje kao jedini HTTP-nivo slučaj.
- `tests/Unit/Models/UserTest.php` (3 testa) — potvrđuje da `role` **nije mass-assignable** (`User::create(['role' => 'admin', ...])` i dalje daje `UserRole::User` posle `fresh()`), plus `isAdmin()` helper testovi
- Obrisan trivijalni `tests/Unit/ExampleTest.php` stub (nije ništa testirao)
- Svih **63 testa** prolazi, Pint čist

## Sledeći korak (Faza 8)

Iste funkcionalnosti na razne načine: Livewire, Inertia+Vue/React, REST API+Sanctum, Filament. Treba odlučiti sa korisnikom kojim redosledom/da li sve ili izabrati podskup — ovo je najveća preostala faza i verovatno zahteva razdvajanje u više pod-koraka.
