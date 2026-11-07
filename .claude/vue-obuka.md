# Vue.js obuka: Kanban "Board" za Taskly (osnovni fajl)

Cilj: naučiti Vue.js 3 gradeći novu funkcionalnost u Tasklyju, Kanban tablu ("Board") ispod taba "Zadaci" u meniju. Tabla koristi podatke koji već postoje u bazi (`status`, `priority`, `due_date`). Mockup je u `C:\Users\Nikola\Documents\Claude\taskly-kanban-mockup.jpg`.

## Fajlovi obuke

Sve je u `.claude/`. Ovaj fajl je osnovni: sadrži način rada, plan i dnevnik. Svaki korak ima svoj fajl sa komandama, kodom, objašnjenjima i rezultatom.

- [vue-obuka-korak-1.md](vue-obuka-korak-1.md): Priprema (instalacija Vue-a)
- [vue-obuka-korak-2.md](vue-obuka-korak-2.md): Ruta, Blade stranica i stavka "Board" u meniju

Novi korak se dodaje kao `vue-obuka-korak-N.md`, uz red u spisku iznad i u planu ispod.

## Način rada

- Korisnik sam kuca komande i piše kod. Claude objašnjava šta se i zašto radi, daje komande i kod za prepisivanje, i vodi dokumentaciju obuke.
- Claude ne instalira pakete i ne menja projektne fajlove za potrebe obuke, osim fajlova obuke.
- Komande idu kroz Sail iz `~/projects/todolist` u WSL-u (`vendor/bin/sail ...`). Vite dev server mora raditi: `vendor/bin/sail npm run dev`.
- Svaki korak ima: šta učimo, komande, kod, proveru i rezultat. Završeni koraci se označavaju sa ✅.

## Plan koraka

1. ✅ [Priprema: instalacija Vue-a i Vite plugina, provera da build radi](vue-obuka-korak-1.md)
2. ⬜ [Ruta, Blade stranica i stavka "Board" u meniju (tačka montiranja za Vue)](vue-obuka-korak-2.md)
3. ⬜ Prva Vue komponenta: `ref`, `v-for`, `v-if` sa statičkim podacima
4. ⬜ JSON endpoint za zadatke (kontroler, resource, test)
5. ⬜ Dohvat podataka iz komponente (`onMounted`, `fetch`) i kolone po statusu (`computed`)
6. ⬜ Razdvajanje na komponente `BoardColumn` i `TaskCard` (`props`, `emits`)
7. ⬜ Prevlačenje kartica i promena statusa (`PATCH` ruta, optimistično ažuriranje)
8. ⬜ Filter po prioritetu, pretraga, završna dorada izgleda i testovi

## Dnevnik

| Datum | Šta je urađeno |
| --- | --- |
| 2026-11-07 | Napisan plan i Korak 1 |
| 2026-11-07 | Dodato objašnjenje o proveri verzija (Vite 8 i plugin) i o Guzzleu |
| 2026-11-07 | Korak 1 završen: instalacija, `vite.config.js`, build prolazi; zabeležen rezultat `npm audit` |
| 2026-11-07 | Napisan Korak 2 (ruta, Blade stranica, meni, prva komponenta, test) |
| 2026-11-07 | Dokumentacija podeljena na osnovni fajl i po jedan fajl za svaki korak |
