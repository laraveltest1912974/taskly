# Laravel ToDo List — izvod sesije (2026-09-15)

Cilj: učenje Laravela kroz izradu ToDo List aplikacije na razne načine (Blade, Livewire, Inertia, API...), sa više tipova korisnika, admin dashboard-om i CRUD-om nad dnevnim zadacima. Sve na lokalu, u Docker-u.

> **Status: PAUZIRANO nakon Faze 7 + redizajna frontend-a.** Faze 0–7 su odrađene i testirane. Faza 8 (Livewire/Inertia/API/Filament varijante) nije počela i **ODLAŽE SE dok aplikacija ne bude live na internetu** (odluka 2026-09-18). **Trenutni prioritet: postavka na AWS + mobilna aplikacija** (vidi sekciju "Sledeći korak" na dnu). Ova beleška služi kao referenca za pitanja o dosad urađenom — sekcije ispod prate hronologiju rada, "Brzi pregled" ispod je sažetak za brzo pretraživanje.

## Linkovi, URL-ovi i servisi (referenca, ažurirano 2026-09-18)

**Aplikacija**
- Live: https://taskly-olux.onrender.com (health: `/up`, privacy: `/privacy`, login: `/login`)
- Lokalno (Sail): http://localhost:8010 · Mailpit http://localhost:8125 · Vite dev server http://localhost:5173 · MySQL `127.0.0.1:3310` · Redis `6310`
- Lokalni OAuth callback-ovi: `http://localhost:8010/auth/google/callback`, `http://localhost:8010/auth/facebook/callback` (Facebook ih ne traži u listi — localhost je automatski dozvoljen u development režimu)
- Produkcioni OAuth callback-ovi: `https://taskly-olux.onrender.com/auth/google/callback`, `https://taskly-olux.onrender.com/auth/facebook/callback`

**Kod / repo (GitHub, nalog `laraveltest1912974`)**
- Repo (javan): https://github.com/laraveltest1912974/taskly
- Ključni commit-ovi ove faze: `027180f` social login, `d185301` priprema za Render, `f2a0d85` Render region Frankfurt, `2adc4f3` beleške o deployu

**Render** (workspace *My Workspace*; nalog je vezan za Google nalog *Laravel Test*, avatar "L")
- Dashboard: https://dashboard.render.com/
- Servis `taskly` (`srv-dampi13m8hqs73absqb0`): pregled https://dashboard.render.com/web/srv-dampi13m8hqs73absqb0 · Deploys `.../deploys` · Logs `.../logs?t=app&r=1h` · Environment `.../env` (tu se menjaju secreti i `APP_URL`; izmena okida redeploy)
- Blueprint `taskly` (`exs-dampe34ri2ms73bb783g`): https://dashboard.render.com/blueprint/exs-dampe34ri2ms73bb783g/sync/exe-dampe34ri2ms73bb786g
- Novi blueprint iz javnog repoa: https://dashboard.render.com/select-repo?type=blueprint

**TiDB Cloud** (org *Nikola's Org*, `orgId=1372813089209366969`)
- Lista resursa: https://tidbcloud.com/tidbs?orgId=1372813089209366969
- Instanca `taskly` (`10174430657315079062`): overview https://tidbcloud.com/tidbs/10174430657315079062/overview?orgId=1372813089209366969 · SQL Editor `.../sqleditor?orgId=1372813089209366969` · *Connect* dijalog na overview-u (lozinka se generiše tamo, prikazuje samo jednom)
- Baza `taskly`, host `gateway01.eu-central-1.prod.aws.tidbcloud.com`, port 4000

**Google Cloud / Google Auth Platform** (projekat `taskly-509018`; nalog `xsaero@gmail.com`; stari projekat `php-tutorial-a5397` se ne koristi za Taskly)
- 2SV (obavezan za Cloud konzolu): https://myaccount.google.com/signinoptions/twosv · Security: https://myaccount.google.com/security
- Klijenti (redirect URI-ji): https://console.cloud.google.com/auth/clients?project=taskly-509018 (klijent `Taskly local`, Client ID počinje sa `474847234137-erv7…`)
- Audience / test korisnici: https://console.cloud.google.com/auth/audience?project=taskly-509018
- Branding: https://console.cloud.google.com/auth/branding?project=taskly-509018 · Overview: https://console.cloud.google.com/auth/overview?project=taskly-509018

**Meta for Developers** (app *Taskly*, App ID `1712588247381610`, Development režim)
- Moje aplikacije: https://developers.facebook.com/apps/
- Dashboard: https://developers.facebook.com/apps/1712588247381610/dashboard/
- Use cases → Facebook Login → Settings (Valid OAuth Redirect URIs): https://developers.facebook.com/apps/1712588247381610/use_cases/customize/settings/?use_case_enum=FB_LOGIN&selected_tab=settings&product_route=fb-login
- Use cases → dozvole (`email`, `public_profile`): https://developers.facebook.com/apps/1712588247381610/use_cases/customize/?use_case_enum=FB_LOGIN&selected_tab=permissions&product_route=use_cases
- App settings → Basic (App ID, App secret — *Show* traži Facebook lozinku): https://developers.facebook.com/apps/1712588247381610/settings/basic/

**Ostali korišćeni linkovi**
- Dizajn inspiracija (samo obrasci, ne kod): https://linear.app · https://todoist.com
- Bunny Fonts (Inter): https://fonts.bunny.net
- GitHub device login (korišćen za `gh auth`): https://github.com/login/device
- Docker Hub slike u `Dockerfile`: `php:8.5-fpm-alpine`, `node:24-alpine`, `composer:2`, `mlocati/php-extension-installer`

**Lokalne komande (sa Windows strane, kroz WSL):** `wsl -d Ubuntu --cd /home/nikola/projects/todolist -- vendor/bin/sail ...` (složeni upiti sa navodnicima: napisati `.sh` u scratchpad i pokrenuti `wsl -d Ubuntu -- bash /mnt/c/.../skripta.sh`).

## Login preko Googlea i Facebooka (2026-09-18)

Dodat social login (Laravel Socialite) uz postojeći email/lozinka login. **Pravi OAuth tok nije probran u browseru** — nemamo Google/Facebook kredencijale; testovi mockuju Socialite.

- **Zavisnost:** `laravel/socialite` v5.31. Instalacija je prvo pala jer najnoviji Socialite vuče `league/oauth1-client` koji ne podržava Guzzle 8 (projekat je imao `guzzlehttp/guzzle` 8.2.0). Uz odobrenje korisnika pokrenuto `composer require laravel/socialite -W`, što je **spustilo Guzzle (i promises/psr7) na 7.x** — Laravel 13 i Boost dozvoljavaju `^7.8 || ^8.0`, pa je bezbedno. Kad `oauth1-client` podrži Guzzle 8, može se vratiti gore.
- **Baza:** tabela `social_accounts` (`user_id` FK cascade, `provider`, `provider_user_id`, unique `[provider, provider_user_id]`) — bolje od kolona na `users` jer jedan korisnik može imati više provajdera. Model `SocialAccount` (`#[Fillable]`, `user()`), `SocialAccountFactory`, `User::socialAccounts()`.
- **Kontroler:** `App\Http\Controllers\Auth\SocialLoginController` (`redirect`, `callback`). Rute u `routes/auth.php` (guest grupa): `/auth/{provider}/redirect` (`social.redirect`) i `/auth/{provider}/callback` (`social.callback`), provajder ograničen sa `whereIn('provider', ['google', 'facebook'])` — sve ostalo 404.
- **Logika callback-a:** (1) postojeći `social_accounts` red → prijavi tog korisnika; (2) inače nalog sa istim emailom → **poveži provajdera sa postojećim nalogom** (odluka korisnika, ne odbijaj); (3) inače novi `User` sa `Str::password()` lozinkom. Novi/povezani nalozi dobijaju `email_verified_at`. Izuzetak od provajdera ili prazan email → redirect na `/login` sa `email` greškom (prevedeno).
- **⚠️ Sigurnosna napomena:** povezivanje po emailu je sigurno za Google (email verifikovan), ali slabije za Facebook — neko sa FB nalogom na tuđ email može preuzeti postojeći nalog. Prihvatljivo za lični/learning projekat; za produkciju tražiti potvrdu lozinke pre povezivanja.
- **UI:** Blade komponenta `resources/views/components/social-login-buttons.blade.php` (razdelnik "or" + dugmad "Continue with Google/Facebook" sa inline SVG logoima, dark stil), uključena u `auth/login` i `auth/register`. Prevodi u `lang/sr.json`.
- **Konfiguracija:** `config/services.php` (`google`, `facebook` sa `client_id`/`client_secret`/`redirect` — relativni redirect `/auth/{provider}/callback` Socialite sam pretvara u apsolutni URL), `.env.example` dobio `GOOGLE_*` i `FACEBOOK_*` promenljive.
- **Testovi:** `tests/Feature/Auth/SocialLoginTest.php` (8 testova: dugmad na login/register, redirect ka provajderu, nepodržan provajder 404, novi korisnik, linkovanje po emailu bez duplikata, povratni korisnik, greška provajdera, provajder bez emaila). Ukupno **77 testova** prolazi, Pint čist.

### Podešavanje provajdera i pravi test (2026-09-18)

**Google** (Google Cloud → Google Auth Platform):
- Google Cloud je prvo blokirao pristup dok se ne uključi **2-Step Verification** na Google nalogu (korisnik uključio ručno; uz to "Skip password when possible" ne računa se kao 2SV).
- Napravljen novi projekat **Taskly** (`taskly-509018`; stari `php-tutorial-a5397` je za drugi tutorijal i nije korišćen).
- Consent screen: app `Taskly`, **External**, status **Testing**, kontakt/support `xsaero@gmail.com`. Dodat **test korisnik** `xsaero@gmail.com` — bez toga Google odbija prijavu (`access_denied`); u Testing režimu maksimum 100 test korisnika.
- OAuth client **Taskly local** (Web application), redirect URI `http://localhost:8010/auth/google/callback` (mora tačno da se poklapa, uključujući port 8010).
- Client secret se prikazuje samo pri kreiranju; ako se izgubi, dodaje se novi u Clients → Taskly local.

**Facebook** (Meta for Developers):
- Nova aplikacija **Taskly** (App ID `1712588247381610`, Unpublished/development režim), use case **Authenticate and request data from users with Facebook Login**, bez business portfolija. Kontakt email aplikacije je `nikolaraf@hotmail.com` (Meta ga popuni iz naloga).
- U use case-u dodata dozvola **`email`** uz `public_profile` — obavezno, jer `SocialLoginController` odbija prijavu bez emaila.
- **Redirect URI se NE dodaje** za lokalni rad: Meta kaže da su `http://localhost` preusmeravanja automatski dozvoljena u development režimu, a `http` URI unos odbija kao nevalidan. Za produkciju treba `https://...` URI u "Valid OAuth Redirect URIs" i prebacivanje aplikacije u **Live** (uz privacy policy URL, moguć App Review).
- App secret je maskiran; "Show" traži Facebook lozinku (korisnik ručno).
- U development režimu prijaviti se mogu samo nalozi sa ulogom u aplikaciji (Admin/Developer/Tester); FB nalog bez emaila (samo telefon) neće moći da se prijavi.

**`.env`** (gitignored, ne ide u repo): `GOOGLE_CLIENT_ID/SECRET` i `FACEBOOK_CLIENT_ID/SECRET` popunjeni; posle izmene `vendor/bin/sail artisan config:clear`. Oba secreta su nalepljena u chat pa ih **zameniti pre produkcije** (Google: novi secret u Clients; Meta: Reset App Secret).

**Rezultat testa u browseru (Chrome, oba provajdera uspešna):**
- **Google** → nov korisnik `id=4` (`xsaero@gmail.com`, uloga `user`, email verifikovan) + `social_accounts` red `google`.
- **Facebook** → FB je vratio `nikolaraf@hotmail.com`, koji je već pripadao korisniku `id=3` (registrovan 2026-09-16, 1 task) → nalog je **povezan, ne dupliran** (`social_accounts` red `facebook`), a `email_verified_at` je postavljen. Završeno na `/dashboard`.
- Google i FB emailovi su različiti, pa su to **dva odvojena korisnika** — očekivano ponašanje (poredi se samo email).
- Za logout iz lokalne app koristiti UI dropdown (POST `/logout`); `/login` je dostupan samo gostima.
- Napomena: Chrome autofill je popunio email/lozinku na login formi tokom testa — nije stvar aplikacije.

## Podešavanje sesije (2026-09-18)

- Ovaj fajl je uvezen u `CLAUDE.md` (`@.claude/laravel-todolist-session.md`, sekcija "Session navigation" ispod Boost bloka) da se automatski učitava na početku svake sesije. Ako je fajl velik, uvećava kontekst svake sesije. Boost može prepisati `CLAUDE.md` pri `boost:update` — import držati van `<laravel-boost-guidelines>` bloka.
- Sa Windows strane Sail se pokreće preko `wsl -d Ubuntu --cd /home/nikola/projects/todolist -- vendor/bin/sail ...`. Kontejneri: `sail up -d`, pa `sail npm run dev` (Vite na 5173). Napomena: Vite ispisuje `APP_URL: http://localhost:8000`, a app je na **8010** (`.env.example` i dalje ima 8000).

## Lightbox za Tutorial GIF-ove (2026-09-16)

Korisnik je pitao "Postoji li opcija da se klikom na gifove oni uvelicaju?" — dodat click-to-enlarge lightbox u `resources/views/tutorial/index.blade.php`.

- Svaka GIF kartica je sad `<button>` sa hover overlay-om ("Enlarge" / "Uvećaj" natpis) koji na klik postavlja Alpine `open = '<filename>'`.
- Lightbox je fullscreen `fixed inset-0 z-50` overlay sa `x-show="open"`, zatvara se klikom van slike ili na Escape (`@keydown.escape.window`), prikazuje uvećan GIF + naslov + opis preko `current` Alpine getter-a (`videos.find(v => v.file === open)`).
- `videos` niz se prosleđuje iz PHP-a u Alpine preko `@js($videos)` Blade direktive (bezbedno serijalizuje PHP array u JS).
- Korisnik je zatim pitao da li je to "definitivna velicina" i tražio da se GIF-ovi uvećaju duplo na ekranu kad se klikne — lightbox kontejner promenjen sa `max-w-4xl` (896px) na `max-w-[112rem]` (1792px, tačno duplo), blizu native rezolucije GIF-ova (1568×698px), pa se sada prikazuju skoro u punoj veličini.
- Verifikovano u browseru: klik na GIF karticu otvara lightbox primetno veći nego pre; svih 7 GIF-ova (uključujući "Create a Task" koji je jednom izgledao prazan na screenshot-u — ispostavilo se da je to bio samo privremeni render/paint-timing artefakt, JS provera `img.complete/naturalWidth/naturalHeight` je pokazala da je slika ispravno učitana) prikazuje se ispravno.
- Testovi (69) i dalje prolaze, Pint čist. Commit `10c8737`, pushovan na GitHub.

## 🐛 Bag #2 — Log Out (dropdown meni) nije radio (2026-09-16, posledica fix-a bag-a #1)

Odmah posle fix-a sidebar bag-a, korisnik je prijavio da "Log Out" ne radi. Uzrok: `<main>` element je imao `relative z-10` — **isti z-index kao `<header>`** koji sadrži dropdown meni. Pošto `main` dolazi POSLE `header`-a u DOM-u, a oba imaju eksplicitan (jednak) z-index, `main`-ov sadržaj (npr. ljubičasta "Welcome back" kartica na dashboard-u) je pobeđivao u stacking redosledu i **fizički prekrivao donji deo otvorenog dropdown menija** (uključujući "Log Out" link) — potvrđeno screenshot-om, dropdown se video samo delimično, "Log Out" je bio ispod banner kartice.

**Fix:** Uklonjen `relative z-10` sa `<main>` u `resources/views/layouts/app.blade.php` — nije ni bio potreban (main-ov sadržaj se svakako DOM-redom prirodno renderuje iznad blob pozadine bez eksplicitnog z-indeksa). Sad header (i njegov dropdown) definitivno pobeđuje nad main sadržajem. Potvrđeno u browseru: dropdown se u potpunosti vidi, klik na "Log Out" ispravno odjavljuje i vraća na `/login`.

**Pouka:** Kad dodaješ `z-index` nekom elementu da rešiš jedan stacking problem, obavezno proveriti SVE susedne/nadređene elemente sa istim ili sličnim z-indeksom — lako se napravi novi konflikt na drugom mestu. Ovde su i `header` i `main` imali isti `z-10` "za svaki slučaj" bez stvarne potrebe (nijedan od njih se prirodno ne sudara sa blob pozadinom bez toga).

## 🐛 Bag #1 — sidebar nav nije primao klikove (2026-09-16, nakon dark redizajna)

Korisnik je prijavio: "Ne mogu da klikćem po navigaciji i drugim delovima sajta." Ovo je bio pravi bag u produkcionom kodu (ne artefakt automatizacije), prisutan otkad je dark tema uvedena.

**Uzrok:** U `layouts/app.blade.php`, desktop `<aside>` sidebar je `lg:fixed lg:inset-y-0` ali BEZ eksplicitnog `z-index`. Glavni content wrapper (`<div class="relative flex-1 ... lg:pl-60 ...">`) je `position: relative` (dodato radi pozicioniranja blob SVG-a) i dolazi POSLE aside-a u DOM-u. Pošto je `<aside>` `position: fixed`, izlazi iz flex flow-a, pa content-div (jedini preostali flex item) prirodno zauzima **CELU širinu viewport-a** (0 do 1920px), a `lg:pl-60` samo vizuelno pomera sadržaj (padding), ne i stvarnu širinu/hitbox diva. Kako su i `<aside>` (fixed) i content-div (relative) "positioned" elementi sa `z-index: auto`, CSS stacking pravilo kaže: **kasniji u DOM redosledu pobeđuje** kada je z-index auto na oba — pa je content-div (providan u toj zoni, pa se sidebar VIDI ispod) ipak HVATAO sve klikove u levih 240px, iako se sidebar linkovi vizuelno tu nalaze.

**Dijagnoza:** Potvrđeno preko `document.elementFromPoint(x, y)` na koordinatama "Tasks" linka — vraćao je content-div, ne `<a>` link (`isSameAsLink: false`).

**Fix:** Dodat `lg:left-0 lg:z-20` na `<aside>` (`resources/views/layouts/app.blade.php`) — sada eksplicitan z-index sidebar-a definitivno pobeđuje nezavisno od DOM redosleda. Potvrđeno: `elementFromPoint` sada pogađa pravi link, i svi nav linkovi (Dashboard/Tasks/Tutorial/Admin) + user dropdown rade na **prvi klik**.

**Retroaktivno objašnjenje:** Ovo objašnjava zašto su tokom CELE prethodne sesije (redizajn, snimanje GIF-ova) klikovi na sidebar linkove često "promašivali" i trebalo je kliknuti dvaput ili koristiti direktnu `navigate()` umesto klika — nije bio artefakt automatizacije, bio je pravi bag koji je automatizacija (nasumično) povremeno "preživljavala" zavisno od tačne pozicije klika u okviru te 240px zone.

**Pouka za ubuduće:** Kad se `position: relative`/`absolute` doda na kontejner SAMO radi pozicioniranja dekorativnog elementa (npr. blob pozadina), obavezno proveriti da li taj kontejner sad "krade" stacking prioritet od susednih positioned elemenata (fixed sidebar, modali, dropdown-ovi) — dodati eksplicitan `z-index` na oba da se izbegne oslanjanje na DOM-redosled tie-breaking.

## Tutorial stranica sa GIF-ovima (2026-09-16)

Korisnik je pitao da li mogu da snimam GIF-ove dok klikćem po sajtu (koristeći `mcp__claude-in-chrome__gif_creator`), pa je tražio da se doda stavka "Tutorial" u meni sa GIF-ovima za svaku funkcionalnost.

- Snimljeno 7 GIF-ova kroz `gif_creator` (start_recording → akcije → stop_recording → export sa `download: true`): login, pregled taskova, kreiranje, izmena, brisanje, promena jezika, admin dashboard. Fajlovi se preuzimaju u Windows Downloads folder (`$env:USERPROFILE\Downloads`), otuda kopirani u `public/tutorial-clips/` preko PowerShell-a (i očišćeni `:Zone.Identifier` ADS fajlovi koje Windows dodaje preuzetim fajlovima).
- **Nativni `confirm()` dijalog kod brisanja taska** (`onsubmit="return confirm(...)"`) ne sme da se okine kroz browser automatizaciju (blokira ekstenziju) — zaobiđeno privremenim `window.confirm = () => true` preko `javascript_exec` pre snimanja te akcije, umesto klikanja kroz pravi dijalog.
- Prirodni HTML `<select>` dropdown (Status/Priority) se ne otvara/bira klikom pouzdano u CDP automatizaciji (browser-native popup, van DOM-a koji Chrome DevTools Protocol može da klikne) — koristi se tastatura (`Down`/`Return`) umesto klika na `<option>`.
- **⚠️ Bag koji je otkriven i ispravljen**: ruta `/tutorial` se u početku sudarala sa `public/tutorial/` direktorijumom — nginx u Sail kontejneru je pokušavao da posluži folder direktno (`try_files $uri $uri/ ...`) pre nego što zahtev stigne do Laravel-a, vraćajući sirovi "404 Not Found" (ne Laravel-ovu 404 stranicu). Rešeno preimenovanjem foldera u `public/tutorial-clips/`. **Ubuduće: nikad ne nazivati javni asset folder istim imenom kao neku rutu.**
- Ruta `GET /tutorial` (auth-gated) u `routes/web.php`, priprema niz `$videos` (file/title/description, sve kroz `__()`) i prosleđuje `tutorial.index` view-u
- View `resources/views/tutorial/index.blade.php` — grid kartica (dark stil, isti kao ostatak app-a), svaka sa GIF-om, naslovom i kratkim opisom
- Nav link "Tutorial" dodat u sidebar (ikonica play-dugme) između "Tasks" i "Admin"
- Svi novi stringovi prevedeni u `lang/sr.json`
- Test: `tests/Feature/TutorialTest.php` (guest redirect, autentifikovan korisnik vidi sadržaj) — svih **69 testova** prolazi
- `public/tutorial-clips/` dodaje ~12MB binarnih GIF fajlova u repo — prihvatljivo za ličan/learning projekat, ali vredi imati na umu ako repo naraste

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
vendor/bin/sail artisan test --compact              # 77 testova
vendor/bin/sail bin pint --format agent             # formatiranje
vendor/bin/sail npm run build                       # build frontend assets (Tailwind v4)
vendor/bin/sail npm run dev                         # dev watch (za rad na frontend-u)
```

**Stack:** Laravel 13.32.0, PHP 8.5, MySQL (Sail), Breeze + Blade, Tailwind CSS v4 (CSS-first config), Alpine.js. Autorizacija: `role` kolona (`UserRole` enum) + `TaskPolicy`. App naziv: **Taskly**.

**Šta postoji funkcionalno:**
- Registracija/login/reset lozinke/profil (Breeze)
- Login preko Googlea i Facebooka (Socialite; čeka kredencijale u `.env`)
- CRUD nad zadacima (`/tasks`) sa poljima title/description/due_date/status/priority, vlasništvo po korisniku
- Admin dashboard (`/admin/dashboard`) — statistike + pregled svih korisnika/taskova
- Tamna, Linear/Todoist-inspirisana tema — sidebar, grid+glow+blob pozadina, indigo/violet brand ("Taskly")
- Dvojezičan interfejs (EN/SR) sa switcher-om, realni bilingual seed taskovi
- Tutorial stranica (`/tutorial`) sa GIF snimcima svake funkcionalnosti

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
8. Iste funkcionalnosti na razne načine: Livewire, Inertia+Vue/React, REST API+Sanctum, Filament — **nije počelo; ODLOŽENO dok app ne bude live** (učenje tehnika je za kasnije)
9. **Postavka app live na internet** — **sada najvažnije**: proba na **Render** (besplatno), AWS za pravu app kasnije
10. **Mobilna aplikacija** — **sada najvažnije** (pristup nije izabran, vidi dole)

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

## Sledeći korak: live na AWS + mobilna app (odluka 2026-09-18)

**Faza 8 je odložena** (Livewire, Inertia+Vue/React, REST API+Sanctum, Filament). Učenje tehnika ostaje za posle live postavke. Predlog redosleda kad se vratimo: API + Sanctum → Livewire → Filament → Inertia. Svaka varijanta traži novu zavisnost (potvrda korisnika).

**Sada najvažnije:** proći ceo proces postavke app na internet i napraviti mobilnu aplikaciju. Cilj je da korisnik vidi kompletan roadmap za pravu aplikaciju koju planira uskoro. **Odluka (2026-09-18): proba ide na Render (besplatan plan); AWS (Lightsail/EC2) i Google Play ostaju za pravu aplikaciju.** GitHub Pages ne dolazi u obzir (samo statični fajlovi, nema PHP/MySQL); GitHub Actions se može koristiti za deploy.

### Plan: Render (besplatan plan) — proba live postavke

**Ograničenja besplatnog plana koja menjaju app** (proveriti aktuelne uslove na Renderu, menjaju se):
- Nema PHP runtime-a → potreban **Docker** (`Dockerfile`: multi-stage, Node za `npm run build`, pa PHP 8.5 + nginx).
- Servis se **uspava** posle ~15 min neaktivnosti (prvi zahtev ~30–60 s).
- **Nema trajnog diska** → nema SQLite/fajl-sesija; `SESSION_DRIVER=database`, keš u bazi.
- Nema besplatnih background workera ni crona → `QUEUE_CONNECTION=sync`, bez schedulera; pre-deploy komanda je plaćena → `migrate --force` ide u start skriptu kontejnera.
- Nema besplatne MySQL baze (besplatan Postgres ističe posle ~30 dana) → **spoljna besplatna MySQL-kompatibilna baza** (predlog: TiDB Cloud Serverless ili Aiven).
- TLS završava Render → **`->trustProxies(at: '*')`** u `bootstrap/app.php`, inače Socialite generiše `http://` redirect i OAuth puca. `APP_URL` mora biti `https://...`.

**Koraci:**
1. **Repo (Claude):** `Dockerfile` + start skripta (migrate, `config/route/view:cache`, pokretanje nginx+php-fpm) + nginx konfiguracija (predlog: u jednom novom folderu `deploy/` — traži odobrenje jer pravila projekta zabranjuju nove osnovne foldere bez saglasnosti), opciono `render.yaml` (Blueprint), `trustProxies`, `.env.production.example`, privacy policy stranica (treba za Meta Live).
2. **Spoljna baza (korisnik):** napraviti nalog i besplatnu MySQL-kompatibilnu bazu (nalog ne pravi Claude).
3. **Render (korisnik):** nalog → New Web Service → GitHub repo `taskly` → Docker → Free plan. **Env varijable** (`APP_KEY`, DB podaci, Google/Facebook ID i secret, `APP_URL`, `APP_ENV=production`, `APP_DEBUG=false`) upisuje korisnik u Render dashboard — Claude ne unosi secrete u web forme.
4. **OAuth i provera:** besplatan HTTPS na `*.onrender.com`. Google: dodati `https://<ime>.onrender.com/auth/google/callback` u klijent (app može ostati **Testing**); Facebook: isti https URI u *Valid OAuth Redirect URIs* (app ostaje **Development**). Za probu nije potrebna objava. Zatim test prijave u browseru.
5. **Mobilno:** PWA (manifest + service worker) i vizuelna provera mobilnog prikaza sidebar-a.

**Odluke (korisnik, 2026-09-18):** dozvoljeno dodavanje `Dockerfile`/`deploy/`/`render.yaml`; baza = **TiDB Cloud Serverless**.

**Korak 1 završen (2026-09-18) — repo je spreman za Render:**
- `Dockerfile` (3 faze: `composer:2` za vendor, `node:24-alpine` za `npm run build`, `php:8.5-fpm-alpine` + nginx + supervisor za runtime; PHP ekstenzije `pdo_mysql intl zip`), `.dockerignore` (isključuje `.env*`, `vendor`, `node_modules`, `public/build|hot`, `bootstrap/cache/*.php` da dev provajderi ne uđu u image).
- `deploy/`: `nginx.conf` (sluša `$PORT`, Render podrazumevano 10000), `supervisord.conf` (nginx + php-fpm), `php.ini` (opcache), `php-fpm.conf` (`clear_env = no`, `catch_workers_output`), `start.sh` (proverava `APP_KEY`, `migrate --force`, `config/route/view:cache`, pokreće supervisor).
- `render.yaml` (Blueprint; `sync: false` promenljive Render traži pri kreiranju — tu korisnik upisuje secrete), `.env.production.example`.
- `bootstrap/app.php`: `$middleware->trustProxies(at: '*')`. Novo: javna stranica `/privacy` (`privacy.blade.php`, prevedena u `sr.json`, link u `guest` layoutu), `tests/Feature/ProductionReadinessTest.php` (5 testova: privacy javna/prevedena/link, https URL-ovi iza proksija, `/up`). Ukupno **82 testa** prolazi, Pint čist.
- **Lokalno provereno:** Docker build prošao; kontejner protiv Sail MySQL-a startuje (`Nothing to migrate`, cache-ovi, nginx+php-fpm RUNNING); `/up`, `/login`, `/privacy` = 200, Vite asseti se serviraju, `X-Forwarded-Proto: https` daje https URL-ove, tutorial GIF-ovi se serviraju.
- **TiDB napomena:** TLS je obavezan → `MYSQL_ATTR_SSL_CA=/etc/ssl/certs/ca-certificates.crt` (već u `render.yaml`), port **4000**, korisničko ime kod TiDB Serverless ima prefiks. Proveriti tačne vrednosti u TiDB konzoli ("Connect").

### Rezultat: app je LIVE na Renderu (2026-09-18)

**Javni URL: https://taskly-olux.onrender.com** (Render web service `taskly`, `srv-dampi13m8hqs73absqb0`, Docker, Free, Frankfurt). Baza: TiDB Cloud Starter instanca `taskly` (Frankfurt, spending limit 0, bez kartice), baza `taskly`, host `gateway01.eu-central-1.prod.aws.tidbcloud.com`, port 4000, TLS preko `MYSQL_ATTR_SSL_CA`.

**Kako je urađeno (redosled koji je radio):**
1. TiDB Cloud: nalog → *Create Resource* → plan **Starter** (podrazumevano je bio izabran *Essential* od ~480 USD/mes koji traži karticu — obavezno prebaciti na Starter) → SQL Editor `CREATE DATABASE taskly;` → *Connect* daje host/port/username (lozinku generiše korisnik, prikazuje se samo jednom; dijalog za AI/Chat2Query zatvoriti bez pristanka).
2. Render: nalog → *New → Blueprint* → **Public Git Repository** URL `https://github.com/laraveltest1912974/taskly` (GitHub nalog NIJE povezan sa Renderom preko GitHub aplikacije, ali servis je Blueprint-managed i **Render ipak sam pokreće deploy pri svakom pusha na `main`** — trigger *Auto-Deploy*, potvrđeno za commit `2adc4f3`, ~50 s; *Manual Deploy* nije potreban). Blueprint traži `sync: false` varijable: `APP_KEY` (`sail artisan key:generate --show`, stavljen u clipboard bez ispisa u chat), `APP_URL`, `DB_HOST/DATABASE/USERNAME/PASSWORD`, Google/Facebook ID i secret. `region: frankfurt` je dodat u `render.yaml` (commit `f2a0d85`) da baza i servis budu u istom regionu.
3. Prvi deploy ~2 min (build), migracije su prošle na TiDB-u; posle promene `APP_URL` na pravi URL redeploy ~45 s (keširani slojevi).
4. **OAuth:** Google klijent `Taskly local` dobio dodatni URI `https://taskly-olux.onrender.com/auth/google/callback`; Facebook *Valid OAuth Redirect URIs* dobio `https://taskly-olux.onrender.com/auth/facebook/callback`. Google app ostaje **Testing** (samo test korisnik `xsaero@gmail.com`), Facebook app **Development** (samo nalozi sa ulogom u aplikaciji).

**Ručni test na javnom URL-u (Chrome) — sve prošlo:** Google i Facebook prijava; odjava; kreiranje (validacija + poruka "Task created."), izmena (status/prioritet, dashboard statistika prati), brisanje ("Zadatak je obrisan."); EN/SR prebacivanje sa pamćenjem u sesiji; svih 7 tutorial GIF-ova se učitava; profil se otvara i čuva ("Sačuvano"); `/admin/dashboard` = 403 za običnog korisnika; kao gost zaštićene rute preusmeravaju na login, a `/login`, `/register`, `/forgot-password`, `/privacy`, `/up` = 200; https linkovi za socijalnu prijavu (trustProxies radi).

**Nije testirano:** admin dashboard kao admin (u novoj bazi nema admina; može `UPDATE users SET role='admin' WHERE email=...` u TiDB SQL Editoru), prijava/registracija/reset lozinkom (ne unosimo lozinke kroz automatizaciju), brisanje naloga (destruktivno), mobilni prikaz.

**Zapažanja / poznata ograničenja:**
- Besplatan Render se **uspava** posle ~15 min neaktivnosti; prvi zahtev ~50 s. Nema crona ni queue workera (`QUEUE_CONNECTION=sync`), sesije/keš u bazi, mail preko `log` drajvera.
- Posle prijave `redirect()->intended()` vraća na poslednju zaštićenu stranicu koju je gost tražio — ako je to `/admin/dashboard`, običan korisnik vidi 403 (laravel ponašanje; može se lepše rešiti).
- Chrome ekstenzija je na `facebook.com` kratko vratila "Permission denied for this action on this domain" za screenshot, ali je klik prošao — ako se ponovi, proveriti stanje kroz `get_page_text`.
- `render.yaml` ima `sync: false` za secrete — **secreti se nikad ne upisuju u repo**, samo u Render dashboard.

**Bezbednost pre prave produkcije:** Google secret, Facebook secret i TiDB/Render vrednosti su u nekom trenutku nalepljeni u chat → zameniti (Google: novi secret u Clients; Meta: Reset App Secret; TiDB: novi password). Za pravu app: privacy policy URL i Live režim za Meta, *In production* za Google, svoj domen.

**Sledeće:** 5) PWA (manifest + service worker) i vizuelna provera mobilnog prikaza sidebar-a; zatim odluka o mobilnoj aplikaciji (Capacitor ili nativna uz API); Faza 8 ostaje odložena.

### Postavka na AWS — za pravu aplikaciju kasnije (okvirni koraci; prvo izabrati servis)
1. **Servis:** Lightsail (najjednostavnije, fiksna cena) ili EC2 (+ RDS za MySQL) za učenje; ECS/Fargate je za kasnije. Sail `compose.yaml` je za razvoj — za produkciju treba nginx + php-fpm (ili produkcioni Dockerfile).
2. **Domen + DNS** (Route 53 ili spoljni registrar) i **HTTPS** (Let's Encrypt/certbot ili ALB + ACM).
3. **Produkcioni `.env`:** `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://...`, nov `APP_KEY`, produkciona baza, session/cache/queue drajveri, **mail preko SES** (Mailpit je samo za razvoj).
4. **Deploy:** `git pull`, `composer install --no-dev`, `npm run build`, `artisan migrate --force`, `config:cache route:cache view:cache`; queue worker (supervisor) i cron za scheduler; dozvole nad `storage`/`bootstrap/cache`.
5. **Sigurnost/rad:** security group-e, backup baze, monitoring/logovi.
6. **OAuth za produkciju:** dodati `https://<domen>/auth/{google,facebook}/callback` u Google client i Meta app, prebaciti Google app u *In production* i Meta app u *Live* (privacy policy URL, moguć App Review), **zameniti secrete** koji su bili nalepljeni u chat.
7. Napomena: `public/tutorial-clips/` je ~12MB binarnih GIF-ova u repou.

### Mobilna aplikacija — opcije (nije izabrano)
- **Responsive web / PWA:** najjeftinije, "Add to Home Screen"; mobilni prikaz sidebar-a još nije vizuelno proveren (poznato ograničenje).
- **Capacitor omotač** oko postojeće web app: brzo, ali ima ograničenja u prodavnicama (Apple često odbija "tanke" web omotače) i Google login ne sme u ugrađenom WebView-u (`disallowed_useragent`) — traži sistemski browser/nativni plugin.
- **Nativna app (React Native/Flutter):** traži **REST API + Sanctum** (deo odložene Faze 8 — tu se javlja veza sa odlaganjem).
- Prodavnice: Google Play jednokratno ~25 USD, Apple Developer ~99 USD godišnje.
