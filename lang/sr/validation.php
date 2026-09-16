<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'Polje :attribute mora biti prihvaćeno.',
    'accepted_if' => 'Polje :attribute mora biti prihvaćeno kada je :other :value.',
    'active_url' => 'Polje :attribute mora biti važeći URL.',
    'after' => 'Polje :attribute mora biti datum posle :date.',
    'after_or_equal' => 'Polje :attribute mora biti datum posle ili jednak :date.',
    'alpha' => 'Polje :attribute sme sadržati samo slova.',
    'alpha_dash' => 'Polje :attribute sme sadržati samo slova, brojeve, crtice i donje crte.',
    'alpha_num' => 'Polje :attribute sme sadržati samo slova i brojeve.',
    'any_of' => 'Polje :attribute nije validno.',
    'array' => 'Polje :attribute mora biti niz.',
    'array_keys' => 'Polje :attribute sme sadržati samo sledeće ključeve: :values.',
    'ascii' => 'Polje :attribute sme sadržati samo jednobajtne alfanumeričke karaktere i simbole.',
    'base64' => 'Polje :attribute mora biti važeći Base64 string.',
    'before' => 'Polje :attribute mora biti datum pre :date.',
    'before_or_equal' => 'Polje :attribute mora biti datum pre ili jednak :date.',
    'between' => [
        'array' => 'Polje :attribute mora imati između :min i :max stavki.',
        'file' => 'Polje :attribute mora biti između :min i :max kilobajta.',
        'numeric' => 'Polje :attribute mora biti između :min i :max.',
        'string' => 'Polje :attribute mora imati između :min i :max karaktera.',
    ],
    'boolean' => 'Polje :attribute mora biti tačno ili netačno.',
    'can' => 'Polje :attribute sadrži neautorizovanu vrednost.',
    'confirmed' => 'Potvrda polja :attribute se ne poklapa.',
    'contains' => 'Polju :attribute nedostaje obavezna vrednost.',
    'current_password' => 'Lozinka nije tačna.',
    'date' => 'Polje :attribute mora biti važeći datum.',
    'date_equals' => 'Polje :attribute mora biti datum jednak :date.',
    'date_format' => 'Polje :attribute mora odgovarati formatu :format.',
    'decimal' => 'Polje :attribute mora imati :decimal decimalnih mesta.',
    'declined' => 'Polje :attribute mora biti odbijeno.',
    'declined_if' => 'Polje :attribute mora biti odbijeno kada je :other :value.',
    'different' => 'Polja :attribute i :other moraju biti različita.',
    'digits' => 'Polje :attribute mora imati :digits cifara.',
    'digits_between' => 'Polje :attribute mora imati između :min i :max cifara.',
    'dimensions' => 'Polje :attribute ima nevažeće dimenzije slike.',
    'distinct' => 'Polje :attribute ima duplu vrednost.',
    'doesnt_contain' => 'Polje :attribute ne sme sadržati ništa od sledećeg: :values.',
    'doesnt_end_with' => 'Polje :attribute ne sme se završavati ničim od sledećeg: :values.',
    'doesnt_start_with' => 'Polje :attribute ne sme počinjati ničim od sledećeg: :values.',
    'email' => 'Polje :attribute mora biti važeća email adresa.',
    'encoding' => 'Polje :attribute mora biti kodirano u :encoding.',
    'ends_with' => 'Polje :attribute mora se završavati jednim od sledećeg: :values.',
    'enum' => 'Izabrana vrednost za :attribute nije validna.',
    'exists' => 'Izabrana vrednost za :attribute nije validna.',
    'extensions' => 'Polje :attribute mora imati jednu od sledećih ekstenzija: :values.',
    'file' => 'Polje :attribute mora biti fajl.',
    'filled' => 'Polje :attribute mora imati vrednost.',
    'gt' => [
        'array' => 'Polje :attribute mora imati više od :value stavki.',
        'file' => 'Polje :attribute mora biti veće od :value kilobajta.',
        'numeric' => 'Polje :attribute mora biti veće od :value.',
        'string' => 'Polje :attribute mora imati više od :value karaktera.',
    ],
    'gte' => [
        'array' => 'Polje :attribute mora imati :value stavki ili više.',
        'file' => 'Polje :attribute mora biti veće ili jednako :value kilobajta.',
        'numeric' => 'Polje :attribute mora biti veće ili jednako :value.',
        'string' => 'Polje :attribute mora imati :value ili više karaktera.',
    ],
    'hex_color' => 'Polje :attribute mora biti važeća heksadecimalna boja.',
    'image' => 'Polje :attribute mora biti slika.',
    'in' => 'Izabrana vrednost za :attribute nije validna.',
    'in_array' => 'Polje :attribute mora postojati u :other.',
    'in_array_keys' => 'Polje :attribute mora sadržati bar jedan od sledećih ključeva: :values.',
    'integer' => 'Polje :attribute mora biti ceo broj.',
    'ip' => 'Polje :attribute mora biti važeća IP adresa.',
    'ipv4' => 'Polje :attribute mora biti važeća IPv4 adresa.',
    'ipv6' => 'Polje :attribute mora biti važeća IPv6 adresa.',
    'json' => 'Polje :attribute mora biti važeći JSON string.',
    'list' => 'Polje :attribute mora biti lista.',
    'lowercase' => 'Polje :attribute mora biti malim slovima.',
    'lt' => [
        'array' => 'Polje :attribute mora imati manje od :value stavki.',
        'file' => 'Polje :attribute mora biti manje od :value kilobajta.',
        'numeric' => 'Polje :attribute mora biti manje od :value.',
        'string' => 'Polje :attribute mora imati manje od :value karaktera.',
    ],
    'lte' => [
        'array' => 'Polje :attribute ne sme imati više od :value stavki.',
        'file' => 'Polje :attribute mora biti manje ili jednako :value kilobajta.',
        'numeric' => 'Polje :attribute mora biti manje ili jednako :value.',
        'string' => 'Polje :attribute mora imati najviše :value karaktera.',
    ],
    'mac_address' => 'Polje :attribute mora biti važeća MAC adresa.',
    'max' => [
        'array' => 'Polje :attribute ne sme imati više od :max stavki.',
        'file' => 'Polje :attribute ne sme biti veće od :max kilobajta.',
        'numeric' => 'Polje :attribute ne sme biti veće od :max.',
        'string' => 'Polje :attribute ne sme imati više od :max karaktera.',
    ],
    'max_digits' => 'Polje :attribute ne sme imati više od :max cifara.',
    'mimes' => 'Polje :attribute mora biti fajl tipa: :values.',
    'mimetypes' => 'Polje :attribute mora biti fajl tipa: :values.',
    'min' => [
        'array' => 'Polje :attribute mora imati bar :min stavki.',
        'file' => 'Polje :attribute mora biti najmanje :min kilobajta.',
        'numeric' => 'Polje :attribute mora biti najmanje :min.',
        'string' => 'Polje :attribute mora imati najmanje :min karaktera.',
    ],
    'min_digits' => 'Polje :attribute mora imati najmanje :min cifara.',
    'missing' => 'Polje :attribute mora nedostajati.',
    'missing_if' => 'Polje :attribute mora nedostajati kada je :other :value.',
    'missing_unless' => 'Polje :attribute mora nedostajati osim ako :other nije :value.',
    'missing_with' => 'Polje :attribute mora nedostajati kada je prisutno :values.',
    'missing_with_all' => 'Polje :attribute mora nedostajati kada su prisutna :values.',
    'multiple_of' => 'Polje :attribute mora biti umnožak broja :value.',
    'not_in' => 'Izabrana vrednost za :attribute nije validna.',
    'not_regex' => 'Format polja :attribute nije validan.',
    'numeric' => 'Polje :attribute mora biti broj.',
    'password' => [
        'letters' => 'Polje :attribute mora sadržati bar jedno slovo.',
        'mixed' => 'Polje :attribute mora sadržati bar jedno veliko i jedno malo slovo.',
        'numbers' => 'Polje :attribute mora sadržati bar jedan broj.',
        'symbols' => 'Polje :attribute mora sadržati bar jedan simbol.',
        'uncompromised' => 'Uneta vrednost za :attribute se pojavila u curenju podataka. Molimo izaberite drugu vrednost za :attribute.',
    ],
    'present' => 'Polje :attribute mora biti prisutno.',
    'present_if' => 'Polje :attribute mora biti prisutno kada je :other :value.',
    'present_unless' => 'Polje :attribute mora biti prisutno osim ako :other nije :value.',
    'present_with' => 'Polje :attribute mora biti prisutno kada je prisutno :values.',
    'present_with_all' => 'Polje :attribute mora biti prisutno kada su prisutna :values.',
    'prohibited' => 'Polje :attribute je zabranjeno.',
    'prohibited_if' => 'Polje :attribute je zabranjeno kada je :other :value.',
    'prohibited_if_accepted' => 'Polje :attribute je zabranjeno kada je :other prihvaćeno.',
    'prohibited_if_declined' => 'Polje :attribute je zabranjeno kada je :other odbijeno.',
    'prohibited_unless' => 'Polje :attribute je zabranjeno osim ako :other nije u :values.',
    'prohibits' => 'Polje :attribute zabranjuje prisustvo :other.',
    'regex' => 'Format polja :attribute nije validan.',
    'required' => 'Polje :attribute je obavezno.',
    'required_array_keys' => 'Polje :attribute mora sadržati unose za: :values.',
    'required_if' => 'Polje :attribute je obavezno kada je :other :value.',
    'required_if_accepted' => 'Polje :attribute je obavezno kada je :other prihvaćeno.',
    'required_if_declined' => 'Polje :attribute je obavezno kada je :other odbijeno.',
    'required_unless' => 'Polje :attribute je obavezno osim ako :other nije u :values.',
    'required_with' => 'Polje :attribute je obavezno kada je prisutno :values.',
    'required_with_all' => 'Polje :attribute je obavezno kada su prisutna :values.',
    'required_without' => 'Polje :attribute je obavezno kada :values nije prisutno.',
    'required_without_all' => 'Polje :attribute je obavezno kada nijedno od :values nije prisutno.',
    'same' => 'Polje :attribute mora odgovarati polju :other.',
    'size' => [
        'array' => 'Polje :attribute mora sadržati :size stavki.',
        'file' => 'Polje :attribute mora biti :size kilobajta.',
        'numeric' => 'Polje :attribute mora biti :size.',
        'string' => 'Polje :attribute mora imati :size karaktera.',
    ],
    'starts_with' => 'Polje :attribute mora počinjati jednim od sledećeg: :values.',
    'string' => 'Polje :attribute mora biti tekst.',
    'timezone' => 'Polje :attribute mora biti važeća vremenska zona.',
    'unique' => 'Vrednost za :attribute je već zauzeta.',
    'uploaded' => 'Otpremanje polja :attribute nije uspelo.',
    'uppercase' => 'Polje :attribute mora biti velikim slovima.',
    'url' => 'Polje :attribute mora biti važeći URL.',
    'ulid' => 'Polje :attribute mora biti važeći ULID.',
    'uuid' => 'Polje :attribute mora biti važeći UUID.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        'title' => 'naslov',
        'description' => 'opis',
        'due_date' => 'rok',
        'status' => 'status',
        'priority' => 'prioritet',
        'name' => 'ime',
        'email' => 'email',
        'password' => 'lozinka',
        'password_confirmation' => 'potvrda lozinke',
        'current_password' => 'trenutna lozinka',
    ],

];
