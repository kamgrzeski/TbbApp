<?php

/*
|--------------------------------------------------------------------------
| Bank słownictwa browarniczego dla generatora postów
|--------------------------------------------------------------------------
| Model dostaje te listy jako materiał pomocniczy. Nie należy wstawiać wszystkich
| haseł do jednego posta, tylko wybrać kilka pasujących do konkretnego piwa.
|
| Uwaga: generator czyta tylko klucze: aroma, taste, look, process, community,
| hops, malts, styles, collocations, terminology, common_errors, usage_rules.
| Nowa kategoria wymaga dopisania do $labels w SocialMediaGeneratorService.
*/

return [

    /*
    | Zapach (aromat) – to, co czuć przed łykiem.
    */
    'aroma' => [
        // słód i pieczywo
        'chlebowa skórka', 'biszkopt', 'herbatniki', 'tost', 'ciasto drożdżowe', 'karmel',
        'toffi', 'miód', 'miód lipowy', 'orzechy', 'migdały', 'czekolada', 'wanilia',
        // owoce
        'suszone owoce', 'śliwka', 'figa', 'banan', 'jabłko', 'gruszka', 'brzoskwinia',
        'morela', 'malina', 'porzeczka', 'agrest', 'skórka cytrusowa', 'cytrusy', 'cytryna',
        'limonka', 'pomarańcza', 'grejpfrut', 'ananas', 'mango', 'marakuja', 'liczi',
        'owoce tropikalne',
        // przyprawy, zioła, żywica
        'goździk', 'przyprawy', 'pieprz', 'kolendra', 'mięta', 'zioła', 'trawa',
        'żywica', 'sosna', 'eukaliptus', 'lukrecja',
        // kwiaty, palone, ziemiste
        'kwiaty', 'jaśmin', 'kawa', 'kakao', 'palona nuta', 'dym', 'ziemia',
    ],

    /*
    | Smak i odczucia w ustach (mouthfeel).
    */
    'taste' => [
        'słodowy trzon', 'lekka słodycz', 'słodkawe', 'wytrawne wykończenie', 'suche wykończenie',
        'długie wykończenie', 'przyjemna goryczka', 'wyraźna goryczka', 'delikatna goryczka',
        'łagodna goryczka', 'żywiczna goryczka', 'orzeźwiająca goryczka', 'zbalansowana goryczka',
        'pełne ciało', 'lekkie ciało', 'kremowe', 'aksamitne', 'gładkie', 'miękkie',
        'owocowe', 'chlebowe', 'ziołowe', 'pikantne', 'lekko kwaskowe', 'cierpkie',
        'intensywne', 'mocne', 'delikatne', 'łagodne', 'sycące',
        'orzeźwiające', 'pijalne', 'gaz w sam raz', 'lekko gazowane',
    ],

    /*
    | Wygląd, piana i kolor.
    */
    'look' => [
        'złociste', 'słomkowe', 'jasne', 'kolor miodu', 'bursztynowe', 'miedziane',
        'pomarańczowe', 'rubinowe', 'ciemnobrązowe', 'ciemne jak kawa', 'czarne',
        'mętne', 'lekko mgliste', 'klarowne', 'przezroczyste', 'opalizujące', 'lśniące',
        'gęsta, kremowa piana', 'zwarta biała piana', 'drobna piana',
        'piana, która długo trzyma', 'piana szybko opada',
    ],

    /*
    | Proces warzenia – do kulis i edukacji. Używaj tylko, gdy temat tego wymaga.
    */
    'process' => [
        'słodowanie', 'zacieranie', 'filtracja brzeczki', 'gotowanie brzeczki', 'brzeczka',
        'chmielenie na gorąco', 'chmielenie na zimno', 'dry hopping',
        'chmiel aromatyczny', 'chmiel goryczkowy',
        'fermentacja', 'fermentacja górna', 'fermentacja dolna',
        'drożdże górnej fermentacji', 'drożdże dolnej fermentacji',
        'leżakowanie', 'zimne leżakowanie', 'klarowanie piwa',
        'nagazowanie', 'schładzanie brzeczki',
        'butelkowanie', 'drożdże', 'warka', 'słód', 'chmiel',
    ],

    /*
    | Chmiel – odmiany i ich charakter. Używaj tylko odmian, które faktycznie są w DANYCH PIWA.
    */
    'hops' => [
        'Cascade – cytrusy i grejpfrut', 'Citra – limonka, mango, liczi', 'Mosaic – owoce tropikalne i jagody',
        'Saaz – delikatne, ziołowe, korzenne', 'Hallertau – kwiatowe, ziołowe, lekkie',
        'Simcoe – żywica, sosna, owoce tropikalne', 'Magnum – czysta, mocna goryczka',
        'Amarillo – pomarańcza i kwiaty', 'Motueka – cytrusy i zioła', 'Galaxy – marakuja i brzoskwinia',
    ],

    /*
    | Słody – rodzaje i ich charakter. Używaj tylko słodów z DANYCH PIWA.
    */
    'malts' => [
        'pilzneński – czysty, chlebowy', 'monachijski – pieczywo, orzechy, bursztyn',
        'pszeniczny – lekki, kremowa piana', 'karmelowy – karmel, toffi, bursztyn',
        'palony / czekoladowy – kawa, kakao, czerń', 'pszenica karmelowa – słodycz, pełne ciało',
        'monachijski jasny – chlebowy, lekko słodki', 'owsiany – kremowość, gładkość',
    ],

    /*
    | Profile popularnych stylów: fermentacja, kolor, goryczka, ciało i typowe składniki.
    | To ogólna wiedza o stylu (nie o konkretnym piwie). Klucz to nazwa stylu w kanonicznej formie.
    | Gdy styl z DANYCH PIWA jest w tym banku, model dostaje jego profil i ma się go trzymać.
    */
    'styles' => [
        'Hefeweizen' => 'pszeniczne, górna fermentacja (drożdże dają banan i goździk), mętne, bardzo lekka goryczka, wysoka gazowana, obfita pianka',
        'Witbier' => 'belgijskie pszeniczne, mętne, z dodatkiem kolendry i skórki pomarańczy, lekka goryczka, lekkie i orzeźwiające',
        'Pilsner' => 'dolna fermentacja (lager), jasne i klarowne, chmiel szlachetny (np. Saaz) daje wyraźną, ale czystą goryczkę, chlebowa słodycz, suche wykończenie',
        'Lager' => 'dolna fermentacja, czyste i klarowne, delikatny chlebowy słód, chmiel w tle, pijalne',
        'Helles' => 'jasny lager z Bawarii, miękki chlebowy słód, delikatna goryczka, bardzo pijalne',
        'Dunkel' => 'ciemny lager z Bawarii, karmel i pieczywo, gładkie, mało goryczki',
        'Kölsch' => 'jasne i klarowne, fermentacja górna w niskiej temperaturze, delikatne owoce, lekka słodycz i goryczka, orzeźwiające',
        'Märzen' => 'bursztynowy lager, pieczywo i karmel, pełniejsze i gładkie, czysta goryczka',
        'Bock' => 'mocny ciemny lager, karmel, orzechy i pieczywo, pełne ciało, mało chmielu',
        'Pale Ale' => 'górna fermentacja, bursztynowe, karmel i owoce z drożdży, zbalansowana goryczka',
        'APA' => 'amerykańskie pale ale: cytrusy i owoce z chmielu, wyraźna, ale nie intensywna goryczka, pijalne',
        'IPA' => 'jasne, mocno chmielone, żywiczna i cytrusowa lub tropikalna aromatyka z chmielu amerykańskiego, wyraźna goryczka, wytrawne',
        'Double IPA' => 'mocniejsza IPA: intensywna aromatyka chmielu, mocna goryczka, pełniejsze ciało, ciepły alkohol w wykończeniu',
        'Session IPA' => 'lżejsza IPA: aromat chmielu bez mocnej goryczki, lekkie i pijalne, z cytrusową nutą',
        'NEIPA' => 'mętne IPA (New England): miękkie, owocowe, mango i ananas, łagodna goryczka, gładkie ciało',
        'Stout' => 'ciemne, palone słody: palona kawa, gorzka czekolada, gęsta kremowa piana, pełne ciało',
        'Porter' => 'ciemne, czekolada i karmel, suszone śliwki, łagodniejsze palenie niż w stoucie, pełniejsze ciało',
        'Saison' => 'belgijskie farmerskie piwo, górna fermentacja, pieprz i przyprawy z drożdży, wytrawne, bardzo gazowane, orzeźwiające',
        'Gose' => 'kwaśne piwo pszeniczne z Niemiec (Goslar, Lipsk): lekka kwaśność, cytrusy, kolendra i nuta soli, lekkie i orzeźwiające',
        'Tripel' => 'mocne, jasne belgijskie piwo: owoce z drożdży (cytrusy, banan), przyprawy, ciepły alkohol w wykończeniu',
        'Dubbel' => 'ciemne belgijskie piwo: suszone owoce, rodzynki, karmel i przyprawy, pełne ciało',
        'Pumpkin Ale' => 'ale z dodatkiem dyni i przypraw (cynamon, gałka muszkatołowa), karmel, pieczywo, jesienne piwo',
    ],

    /*
    | Słowa o ludziach i miejscu. Nie są faktami – to język relacji, a nie informacje o lokalu.
    */
    'community' => [
        'goście', 'stolik', 'kufel', 'szklanka', 'po pracy', 'wieczór', 'długi weekend',
        'na jedno', 'ze znajomymi', 'w parze', 'przy stole', 'na rozmowę', 'domowa atmosfera',
        'w gronie znajomych', 'z przyjaciółmi', 'wspólny stół', 'towarzystwo', 'dobra kompania',
        'piwna pogawędka', 'sobotnie popołudnie', 'piątkowy wieczór', 'koniec tygodnia',
        'na spokojnie', 'na szybko', 'na dobry początek',
        'ciepły wieczór', 'słoneczne popołudnie',
    ],

    /*
    | Poprawne związki wyrazowe (kolokacje). Gdy piszesz o tej czynności, używaj tych form.
    */
    'collocations' => [
        'po pracy', 'po robocie', 'na piwo', 'napić się piwa', 'napić się czegoś nowego',
        'zamówić kufel', 'zamówić piwo', 'spróbować piwa', 'spróbować nowości',
        'zostać na jedno', 'wpaść na piwo', 'wpaść na kufel', 'przyjść na piwo', 'zajrzeć do nas',
        'usiąść przy stole', 'pogadać przy piwie',
        'piwo warzymy na miejscu', 'piwo trafia do kufli', 'piwo jest już w sprzedaży',
        'piwo pachnie cytrusami', 'piwo ma wyraźną goryczkę', 'w nosie czuć', 'w smaku czuć',
        'nuta owoców', 'długo się utrzymuje na podniebieniu',
        'w czwartek', 'w piątek', 'w środę', 'w sobotę', 'w niedzielę', 'w weekend',
    ],

    /*
    | Nazewnictwo: które słowo dla czego. Używaj konsekwentnie w całym poście.
    */
    'terminology' => [
        'piwo' => 'ogólne określenie napoju; w tekście używaj go konsekwentnie',
        'nazwa piwa' => 'zapisuj wielkimi literami tylko w hooku, dalej normalnym zapisem (np. Pumpkin Ale)',
        'premiera' => 'pierwsze pojawienie się piwa dla gości; użyj, gdy piwo jest nowe',
        'nowość' => 'piwo, którego jeszcze nie było w ofercie; dobry zamiennik „premiery” w dalszej części tekstu',
        'dostępne' => 'stan, w którym gość może je zamówić; „już dostępne”, „w sprzedaży”',
        'kufel' => 'sposób podania piwa w szklance; nie używaj go jako określenia dostępności',
        'warka' => 'partia piwa uwarzona za jednym razem; użyj w kulisach, a nie w ogłoszeniu',
        'warzymy / uwarzyliśmy' => 'piwo warzy się na miejscu; użyj, gdy to faktycznie opisujemy',
        'nuta' => 'pojedyncze wrażenie zapachowe lub smakowe (np. nuta karmelu); nie mieszaj z „aromatem” jako całością',
        'aromat' => 'to, co czuć w nosie, zanim pociągniesz łyk',
        'goryczka' => 'wrażenie wytrawności i gorzkości od chmielu; nie nazywaj jej „goryczką” w każdym zdaniu',
    ],

    /*
    | Typowe błędy generatora. Każdy wpis: błędna forma → poprawna lub co zrobić zamiast tego.
    */
    'common_errors' => [
        'już w kuflach' => 'już dostępne / już w sprzedaży',
        'trafiło do kufli (jako ogłoszenie dostępności)' => 'już dostępne do zamówienia',
        'po pracę' => 'po pracy',
        'przyjdźcie po pracę' => 'wpadnijcie po pracy',
        'piwo wpadło do nas / przyszło do nas' => 'piwo warzymy na miejscu, piwo trafia do kufli',
        'nie chcecie, żeby ciężko było pić' => 'piwo, które da się pić cały wieczór',
        'ciężko' => 'trudno (w tekście posta)',
        'browar restauracyjny, czyli browar połączony z restauracją' => 'jedno wyjaśnienie wystarczy, np. „brewpub, czyli lokal, w którym piwo warzymy i od razu podajemy”',
        'browar restauracyjny' => 'brewpub (nie używaj określenia „browar restauracyjny”)',
        'nasz stout miał wielu fanów' => 'zmyślona opinia gości; pomiń, chyba że WYTYCZNE to potwierdzają',
        'warzymy go u siebie / u nas w domu' => 'warzymy go na miejscu; albo od razu konkret: „Warzymy go w naszym browarze na Staszica”',
        'nasz stout skradł wasze serca / pokochaliście' => 'zmyślona opinia gości; pomiń, chyba że WYTYCZNE to potwierdzają',
        'przyjdźcie na pierwszy łyk nowości' => 'spróbujcie nowości',
        'to dla nas dobry plan na [data]' => 'napisz wprost, co ludzie mogą tego dnia zrobić',
        'na wyciągnięcie ręki' => 'kalka z angielskiego i nadużywany zwrot, pomiń; napisz konkretnie',
        'a my zadbamy o resztę' => 'pusty zwrot, pomiń',
        'otwieramy browar / lokal' => 'nie pisz o otwarciu, jeśli nie ma tego w FAKTACH ani WYTYCZNYCH',
        'wpadnijcie i sprawdźcie, jak smakuje X w wersji z naszego browaru' => 'napisz konkretnie, czym smakuje piwo, zamiast zachęcać do sprawdzenia',
        'w dniu dzisiejszym' => 'dziś',
        'w przeciągu' => 'w ciągu',
        'ilość gości' => 'liczba gości',
        'na bazie' => 'na podstawie',
        'poprzez' => 'przez (często wystarczy samo „przez” albo nic)',
        'posiadać' => 'mieć',
        'zakupić' => 'kupić',
        'odnośnie' => 'w sprawie / dotyczące',
        'w sobotę, 15 listopada' => 'w sobotę 15 listopada',
        'my warzymy' => 'warzymy (osobę niesie czasownik)',
        'z palona kawą' => 'z paloną kawą (narzędnik: -ona → -oną)',    ],

    /*
    | Zasady korzystania z banku.
    */
    'usage_rules' => [
        'Wybierz z banku 2–4 pasujące hasła do konkretnego piwa i wplataj je w zdania. Nie wyliczaj ich listą.',
        'Opisy smaku i zapachu dobieraj do danych piwa: chmiel i słód z DANYCH PIWA oraz styl. Nie opisuj nut, których nic nie uzasadnia.',
        'Jeśli nie znasz piwa, opieraj się na stylu. Gdy nie ma ani piwa, ani stylu, pisz o ogólnym charakterze piwa bez konkretnych nut.',
        'Słowa z kategorii „process” używaj tylko w kulisach i edukacji, nie w zwykłej premierze.',
        'Słowa z kategorii „community” opisują relacje z ludźmi, nie stan lokalu.',
        'Odmieniaj poprawnie i pilnuj przypadków: po pracy, w środę, na kufel, przy stole, ze znajomymi.',
        'Pisz do wszystkich gości: pary, znajomi, rodziny, kobiety i mężczyźni. Nie zakładaj płci ani towarzystwa.',
        'Pisz po polsku naturalnie: czynnie, krótko, bez kalk z angielskiego. Każde zdanie przeczytaj na głos przed zapisaniem.',
        'Odbiorca nie zna się na piwie. Nazwy stylów, odmiany chmielu i słodów podawaj tylko z prostym wyjaśnieniem, np. „IPA, czyli mocno chmielone piwo o wyraźnej goryczce”.',
        'Wybieraj skojarzenia z codzienności (cytrusy, karmel, kawa, chleb, przyprawy) zamiast słów branżowych. Najpierw wyjaśnij, co to jest, potem dlaczego warto.',
        'Piwo się warzy, leżakuje i trafia do kufli. Piwo nie przychodzi i nie wpada do ludzi.',
        'Przymiotniki po przyimku odmieniaj w narzędniku: z paloną kawą, z pełnym ciałem, z kremową pianą (nie „z palona kawą”).',
        'Każde zdanie musi mieć sens logiczny: podmiot, czynność i dopełnienie muszą do siebie pasować. Jeśli zdanie brzmi dziwnie, przepisz je prościej.',
        'Nie pisz o otwarciu browaru ani o stanie lokalu, jeśli nie ma tego w FAKTACH ani WYTYCZNYCH.',
        'Znaj profil stylu i trzymaj się go: nie nazywaj stouta lekkim, nie przypisuj IPA łagodnej goryczki (to cecha NEIPA), nie mieszaj cech różnych stylów. Styl spoza banku: pisz tylko o tym, co wynika z jego nazwy.',
    ],
];
