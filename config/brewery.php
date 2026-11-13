<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Dane browaru używane domyślnie w module postów social media
    |--------------------------------------------------------------------------
    */

    'name' => env('BREWERY_NAME', 'Twin Brothers Brewery'),
    'city' => env('BREWERY_CITY', 'Lublin'),
    'address' => env('BREWERY_ADDRESS', 'ul. Staszica 3A, Lublin'),
    'opening_hours' => env('BREWERY_OPENING_HOURS', ''),

    /*
    | Ile ostatnich opublikowanych postów (status „published”) trafia do promptu jako przykłady stylu. 0 = wyłączone.
    */
    'style_examples_count' => (int) env('BREWERY_STYLE_EXAMPLES', 5),

    /*
    |--------------------------------------------------------------------------
    | Fakty, które model może podawać jako pewne
    |--------------------------------------------------------------------------
    | Wszystko poza tą listą (oraz danymi piwa, wytycznymi i logistyką) model
    | ma traktować jako nieznane i nie może o tym pisać. Rozszerzaj tę listę,
    | gdy browar ma nowe, potwierdzone informacje.
    */
    'facts' => [
        'Brewpub w Lublinie, ul. Staszica 3A.',
        'Piwo warzymy w browarze na miejscu.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Zakazane sformułowania
    |--------------------------------------------------------------------------
    | 'pattern' – wyrażenie regularne (case-insensitive, UTF-8) sprawdzane w wygenerowanym tekście.
    | 'label'   – jak nazwać zakazane słowo w komunikacie dla użytkownika.
    | 'use'     – czym je zastąpić (podpowiedź dla modelu).
    */
    'forbidden_terms' => [
        [
            'pattern' => '/multitap\w*/iu',
            'label' => 'multitap',
            'use' => 'browar lub piwo z browaru',
        ],
        [
            'pattern' => '/\bkurk\w*/iu',
            'label' => 'kurki / kurek',
            'use' => 'nie wspominaj o kurkach',
        ],
        [
            'pattern' => '/\bkran\w*/iu',
            'label' => 'krany / kranowanie',
            'use' => 'nie wspominaj o kranach ani o ich stanie; mów o piwie, nie o kranach',
        ],
        [
            'pattern' => '/\bbrowar\w*\s+(jest\s+|ju[żz]\s+|dzi[śs]\s+)?otwart\w*/iu',
            'label' => 'browar otwarty',
            'use' => 'nie pisz o stanie lokalu; termin wydarzenia podaj bez deklaracji otwarcia',
        ],
        [
            'pattern' => '/\bzapraszamy\b(?!\s+od\s+\d)/iu',
            'label' => 'zapraszamy',
            'use' => 'napisz konkretnie, co gość może zrobić, bez formuły zaproszenia',
        ],
        [
            'pattern' => '/\b(oznacz\w*|udostępni\w*|polub\w*|napisz\w*\s+w\s+komentarz\w*)/iu',
            'label' => 'prośba o interakcję dla zasięgu',
            'use' => 'zamiast prośby o polubienie, oznaczenie lub udostępnienie zadaj konkretne pytanie do czytelników',
        ],
        [
            'pattern' => '/\bprzekonaj\w*\s+si[ęe]\b/iu',
            'label' => 'przekonaj się',
            'use' => 'napisz konkretnie, czym jest piwo, zamiast zachęcać do sprawdzenia',
        ],
        [
            'pattern' => '/\bnie\s+przegap\w*/iu',
            'label' => 'nie przegap',
            'use' => 'podaj konkretną informację lub działanie gościa',
        ],
        [
            'pattern' => '/\b(klawo|mega|super)\b/iu',
            'label' => 'slang: klawo / mega / super',
            'use' => 'zastąp konkretnym opisem bez wzmacniaczy',
        ],
        [
            'pattern' => '/\btank\w*/iu',
            'label' => 'tanki / zbiorniki',
            'use' => 'nie wspominaj o tankach ani o tym, gdzie leżakuje piwo; napisz konkretnie o piwie',
        ],
        [
            'pattern' => '/\bniepasteryz\w*/iu',
            'label' => 'niepasteryzowane',
            'use' => 'nie pisz o pasteryzacji, chyba że WYTYCZNE to potwierdzają',
        ],
    ],
];
