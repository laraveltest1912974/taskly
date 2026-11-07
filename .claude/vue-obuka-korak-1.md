# Vue obuka, Korak 1: Priprema (instalacija Vue-a)

Osnovni fajl: [vue-obuka.md](vue-obuka.md) · Sledeći korak: [vue-obuka-korak-2.md](vue-obuka-korak-2.md)

**Status:** ✅ završen 2026-11-07

**Šta učimo:** šta je potrebno da Vite zna da obrađuje `.vue` fajlove (Single File Components).

**Činjenice o projektu (provereno 2026-11-07):**
- Vite je `^8.0.0`, a `@vitejs/plugin-vue` 6.0.10 podržava Vite 5 do 8, pa nema konflikta.
- Vue u momentu pisanja ima verziju 3.5.x.
- Postojeći paketi su u `devDependencies` (npr. `alpinejs`), pa i ovde koristimo `-D`. Vue se ionako ugrađuje u build, tako da ne postoji razlika u produkciji.
- `vite.config.js` ima plugine `laravel()` i `tailwindcss()`. `resources/js/app.js` pokreće Alpine i registruje service worker, a to ostaje kako jeste.

## 1.1 Instalacija paketa

```bash
cd ~/projects/todolist
vendor/bin/sail npm install -D vue @vitejs/plugin-vue
```

- `vue` je sam framework.
- `@vitejs/plugin-vue` uči Vite da kompajlira `.vue` fajlove (`<template>`, `<script>`, `<style>`).

## 1.2 Uključivanje plugina u `vite.config.js`

Dodaj uvoz na vrh i `vue()` u listu plugina:

```js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
        vue(),
    ],
});
```

## 1.3 Provera

```bash
vendor/bin/sail npm run build
```

Očekivano: build prolazi bez greške. Ako javi problem sa peer zavisnostima, treba poslati tačnu poruku.

Zatim restartovati dev server (Ctrl+C pa ponovo) da pokupi novi plugin:

```bash
vendor/bin/sail npm run dev
```

## Zašto smo pre instalacije pregledali verzije paketa

**Tri sastojka:**
- **Vue** je sam framework, biblioteka koja se koristi u kodu.
- **Vite** je alat koji pokreće razvojni server i pravi build. U Tasklyju njime se obrađuju Tailwind CSS i JavaScript.
- **`@vitejs/plugin-vue`** je dodatak za Vite. Vite sam ne zna šta je `.vue` fajl sa `<template>`, `<script>` i `<style>`, a plugin ga prevodi u običan JavaScript. Bez njega Vue komponente ne bi radile.

**Zašto je moglo doći do konflikta:** svaki plugin u svom opisu navodi sa kojim verzijama Vitea radi (`peerDependencies`). Projekat koristi Vite 8, što je veoma nova verzija. Da je plugin podržavao samo starije verzije (5, 6 ili 7), `npm install` bi javio grešku. Rešenja bi bila da se instalacija iznudi (`--force`) ili da se Vite spusti na staru verziju, a oba mogu da naprave probleme na drugom mestu.

**Šta je utvrđeno:** komandom `vendor/bin/sail npm view @vitejs/plugin-vue version peerDependencies` (samo čitanje podataka sa npm registra, ništa se ne instalira) vidi se:
- plugin 6.0.10 podržava Vite `^5 || ^6 || ^7 || ^8`, pa je Vite 8 među podržanim verzijama;
- najnovija verzija Vuea je 3.5.x, a plugin traži Vue `^3.2.25`, što se slaže.

Zaključak: konflikta nema i obična komanda iz Koraka 1 je dovoljna. Ako se ipak pojavi greška oko `peer` zavisnosti, treba sačuvati tačan ispis.

**Pouka:** pre dodavanja novog paketa vredi pogledati koje verzije drugih paketa on podržava (`npm view <paket> peerDependencies`, a za PHP `composer show <paket>` ili `composer why-not`).

## Primer iz ovog projekta: Guzzle i Socialite

Isti problem već se desio kod Laravel Socialitea (prijava preko Googlea i Facebooka). Najnoviji Socialite vuče paket `league/oauth1-client`, koji ne podržava Guzzle 8. Projekat je imao `guzzlehttp/guzzle` 8.2.0, pa instalacija nije prolazila. Uz odobrenje je pokrenuto `composer require laravel/socialite -W`, što je spustilo Guzzle (i `promises`/`psr7`) na 7.x. Laravel 13 i Boost dozvoljavaju `^7.8 || ^8.0`, pa je to bezbedno. Kad `oauth1-client` podrži Guzzle 8, može se vratiti na novije izdanje.

**Čemu služi Guzzle:**
- Guzzle je PHP biblioteka za slanje HTTP zahteva, tj. način da aplikacija sama pozove neki drugi server (GET, POST i slično, sa zaglavljima, telom, tokenima i obradom odgovora).
- Laravel ga koristi u pozadini: fasada `Http` (`Http::get(...)`, `Http::post(...)`) je omotač oko Guzzlea.
- Socialite ga koristi da pozove Google i Facebook. Posle prijave korisnika šalje autorizacioni kod njihovom serveru, dobija pristupni token i preuzima podatke o korisniku (ime, email). Zato njegova verzija utiče na prijavu preko društvenih mreža.
- Slično važi i za slanje pošte preko nekih servisa, i za bilo koji poziv spoljnog API-ja iz Laravela.
- Razlika u odnosu na ono što radimo u Vueu: Guzzle šalje zahteve **sa servera** (PHP), a `fetch` u Vueu šalje zahteve **iz pregledača** ka našem Laravelu.

## Rezultat (2026-11-07)

✅ Instalirani `vue` 3.5.45 i `@vitejs/plugin-vue` 6.0.10 (oba u `devDependencies`), `vue()` dodat u `vite.config.js`, `npm run build` prolazi (Vite 8.3.0).

**Napomena o `npm audit`:** posle instalacije audit prijavljuje 3 ranjivosti (1 high, 2 critical). Nisu nastale zbog Vuea: ranjivi su `vite` 8.3.0 do 8.3.2 (propusti razvojnog servera) i `shell-quote` preko `concurrently`, a njihove verzije se instalacijom nisu menjale. Oba paketa su samo razvojni alati i ne ulaze u produkcioni image. Ispravka (`vendor/bin/sail npm audit fix`, pa `npm run build`) ostaje kao odvojen posao posle obuke ili između koraka, sa posebnim commitom.
