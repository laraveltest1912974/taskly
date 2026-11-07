# Vue obuka, Korak 2: Ruta, Blade stranica i stavka "Board" u meniju

Osnovni fajl: [vue-obuka.md](vue-obuka.md) · Prethodni korak: [vue-obuka-korak-1.md](vue-obuka-korak-1.md)

**Status:** ⬜ u toku (čeka rezultate korisnika)

**Šta učimo:** kako se Vue ugrađuje u Laravel aplikaciju sa Blade stranicama. Laravel isporučuje običnu stranicu sa praznim `<div id="board-app">`, a Vue preuzima taj element i u njemu iscrtava komponentu (tzv. mount point). Ovo nije SPA, jer ostali delovi aplikacije ostaju Blade.

**Činjenice o projektu (provereno 2026-11-07):**
- Rute su u `routes/web.php`; stranica Tutorial je primer jednostavne rute unutar `auth` grupe.
- Meni je u `resources/views/layouts/partials/sidebar-nav.blade.php`, a stavke koriste `<x-sidebar-link>`.
- Layout `layouts/app.blade.php` već učitava `app.css` i `app.js` preko `@vite`. Layout nema `@stack`, pa se Vue skripta učitava direktno iz view-a.
- Prevodi su u `lang/sr.json` (ključ je engleski tekst). Testovi nasleđuju stil iz `tests/Feature/TutorialTest.php`.
- Vue ide u poseban ulaz `resources/js/board.js`, a ne u `app.js`, da se Vue (oko 50 kB) ne učitava na svakoj stranici.

## 2.1 Ruta

U `routes/web.php`, unutar grupe `Route::middleware('auth')`, odmah posle rute `tutorial`:

```php
Route::view('/board', 'board.index')->name('board');
```

## 2.2 Stavka u meniju

U `resources/views/layouts/partials/sidebar-nav.blade.php`, odmah posle stavke Tasks, pre Tutorial:

```blade
<x-sidebar-link :href="route('board')" :active="request()->routeIs('board')">
    <svg class="w-[15px] h-[15px] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 4.5v15m6-15v15M4.5 4.5h15a1.5 1.5 0 011.5 1.5v12a1.5 1.5 0 01-1.5 1.5h-15A1.5 1.5 0 013 18V6a1.5 1.5 0 011.5-1.5z" />
    </svg>
    {{ __('Board') }}
</x-sidebar-link>
```

## 2.3 Prevod

U `lang/sr.json`, ispod ključa `"Tutorial"` (voditi računa o zarezu):

```json
"Board": "Tabla",
```

## 2.4 Blade stranica

Nov fajl `resources/views/board/index.blade.php`:

```blade
<x-app-layout>
    <x-slot name="header">
        <h2 class="text-[13px] font-medium text-zinc-200">
            {{ __('Board') }}
        </h2>
    </x-slot>

    <div id="board-app"></div>

    @vite('resources/js/board.js')
</x-app-layout>
```

## 2.5 Vite ulaz

U `vite.config.js` dodati u `input`:

```js
input: ['resources/css/app.css', 'resources/js/app.js', 'resources/js/board.js'],
```

## 2.6 JavaScript ulaz

Nov fajl `resources/js/board.js`:

```js
import { createApp } from 'vue';
import BoardApp from './board/BoardApp.vue';

createApp(BoardApp).mount('#board-app');
```

## 2.7 Prva Vue komponenta

Nov fajl `resources/js/board/BoardApp.vue`, za sada samo šablon:

```vue
<template>
    <div class="rounded-xl bg-zinc-950/40 ring-1 ring-white/[0.08] p-6 text-[13px] text-zinc-200">
        Vue radi!
    </div>
</template>
```

## 2.8 Test

Komanda, pa popuniti fajl:

```bash
vendor/bin/sail artisan make:test --phpunit BoardTest --no-interaction
```

```php
<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class BoardTest extends TestCase
{
    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('board'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_the_board_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('board'));

        $response->assertOk();
        $response->assertSee('id="board-app"', false);
    }
}
```

## 2.9 Provera

```bash
vendor/bin/sail npm run build
vendor/bin/sail artisan test --compact --filter=BoardTest
vendor/bin/sail bin pint --dirty --format agent
```

Build ide pre testa, jer novi ulaz `board.js` mora da uđe u `public/build/manifest.json`, inače test javlja `Unable to locate file in Vite manifest`. Zatim u pregledaču otvoriti http://localhost:8010/board (uz pokrenut `sail npm run dev`): treba da se vidi kartica sa tekstom "Vue radi!", a stavka "Board" u meniju treba da bude označena.

## Rezultat

_(upisati kad korak bude završen)_
