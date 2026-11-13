<?php

namespace App\Services;

use App\Models\Recipe;
use App\Models\SocialPost;
use Carbon\CarbonInterface;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SocialMediaGeneratorService
{
    private const ENDPOINT = '/v1/messages';

    /** Zużycie tokenów zbierane przez wszystkie wywołania API w jednym generowaniu. */
    private int $inputTokens = 0;
    private int $outputTokens = 0;

    /**
     * Generuje treść posta. Jeśli wynik zawiera zakazane sformułowania, prosi model o jedną poprawkę.
     * Gdy zakaz nadal występuje, zwraca listę ostrzeżeń, żeby użytkownik mógł to poprawić ręcznie.
     *
     * @return array{content: string, prompt: string, warnings: array<int, string>}
     */
    public function generate(
        string $topicType,
        ?Recipe $recipe = null,
        ?string $customTopic = null,
        ?string $guidelines = null,
        string $platform = SocialPost::PLATFORM_ALL,
        ?CarbonInterface $eventAt = null,
        ?string $address = null,
    ): array {
        $this->assertConfigured();
        $this->inputTokens = 0;
        $this->outputTokens = 0;

        $system = $this->systemPrompt();
        $user = $this->userPrompt($topicType, $recipe, $customTopic, $guidelines, $platform, $eventAt, $address);

        $messages = [['role' => 'user', 'content' => $user]];
        $text = $this->callApi($system, $messages);

        $violations = $this->violations($text);

        if ($violations !== []) {
            $messages[] = ['role' => 'assistant', 'content' => $text];
            $messages[] = ['role' => 'user', 'content' => $this->correctionRequest($violations)];
            $text = $this->callApi($system, $messages);
        }

        $text = $this->reviewAndProofread($text, $user);
        $violations = $this->violations($text);

        if ($violations !== []) {
            // Osobna, samodzielna rozmowa: źródło, tekst i prośba o usunięcie zakazanych sformułowań.
            $repairMessages = [
                ['role' => 'user', 'content' => $user],
                ['role' => 'assistant', 'content' => $text],
                ['role' => 'user', 'content' => $this->correctionRequest($violations)],
            ];

            try {
                $text = $this->callApi($system, $repairMessages);
            } catch (SocialMediaGenerationException $e) {
                Log::warning('Poprawka zakazanych sformułowań nie powiodła się, zwrócono tekst z ostrzeżeniem.', ['error' => $e->getMessage()]);
            }

            $violations = $this->violations($text);
        }

        return [
            'content' => $text,
            'prompt' => "[SYSTEM]\n" . $system . "\n\n[USER]\n" . $user,
            'warnings' => $violations,
            'usage' => $this->usageSummary(),
        ];
    }

    /**
     * @return array{input_tokens: int, output_tokens: int, cost_usd: float}
     */
    private function usageSummary(): array
    {
        $cfg = config('services.anthropic');

        $cost = ($this->inputTokens / 1_000_000) * $cfg['price_input_per_mtok']
            + ($this->outputTokens / 1_000_000) * $cfg['price_output_per_mtok'];

        return [
            'input_tokens' => $this->inputTokens,
            'output_tokens' => $this->outputTokens,
            'cost_usd' => round($cost, 6),
        ];
    }

    /**
     * Zwraca etykiety zakazanych sformułowań znalezionych w tekście.
     *
     * @return array<int, string>
     */
    public function violations(string $text): array
    {
        $found = [];

        foreach (config('brewery.forbidden_terms', []) as $term) {
            if (preg_match($term['pattern'], $text)) {
                $found[] = $term['label'];
            }
        }

        return $found;
    }

    private function assertConfigured(): void
    {
        if (blank(config('services.anthropic.key'))) {
            throw new SocialMediaGenerationException(
                'Brak klucza API Anthropic. Ustaw ANTHROPIC_API_KEY w pliku .env i wyczyść cache konfiguracji (php artisan config:clear).'
            );
        }
    }

    /**
     * Trzeci przebieg w jednym wywołaniu: ocena marketingowa i redakcja językowa razem.
     * Dostaje dane źródłowe i listę zakazanych sformułowań, więc nie dopisuje faktów ani zakazanych zwrotów.
     * Gdy ten krok się nie uda, zwracany jest oryginalny tekst.
     */
    private function reviewAndProofread(string $text, string $sourceData): string
    {
        $system = 'Jesteś starszym specjalistą marketingu i redaktorem języka polskiego w biurze social media browaru rzemieślniczego. '
            . 'Oceniasz post pod kątem skuteczności i poprawiasz jego polszczyznę, tak żeby brzmiał jak napisany przez człowieka.';

        $forbidden = collect(config('brewery.forbidden_terms', []))
            ->map(fn ($term) => '- ' . $term['label'] . ' – ' . $term['use'])
            ->implode("\n");

        $user = "Oceń poniższy post i przepisz go, jeśli trzeba. Wykonaj oba kroki w jednym przebiegu.\n\n"
            . "KROK A – OCENA MARKETINGOWA. Sprawdź po kolei:\n"
            . "0. Czytelność: tekst zrozumie osoba, która nie zna się na piwie. Każdy termin branżowy wyjaśnij prostym słowem albo usuń. Skróty stylów rozwijaj przy pierwszym użyciu.\n"
            . "1. Hook: czy pierwsze zdanie niesie konkretną nowość albo korzyść? Ogólny zachwyt albo etykieta bez napięcia to słabość.\n"
            . "2. Konkret zamiast przymiotnika: każdy opis ma mieć konkretną cechę. Przymiotniki typu „pijalne”, „wyjątkowe”, „intensywne” bez pokrycia usuń. Jeśli cechy nie ma w DANYCH PIWA, usuń opis, nie wymyślaj go.\n"
            . "3. Korzyść: gość wie, co dostanie, przychodząc. Jedna korzyść, nie pięć.\n"
            . "4. Wstępy i asekuracja („w tej wersji najbardziej zależało nam na…”, „ciemny styl nie musi być ciężki”) zamień na treść albo usuń.\n"
            . "5. Rytm i energia: krótkie mocne zdania, jedno dłuższe dla oddechu, zwrot do czytelnika i zaskakujące otwarcie. Jeśli post brzmi jak ulotka, przepisz go z dynamiką. Nie stawiaj trzech przymiotników z rzędu. Usuń zmyślone zdarzenia (pytania gości, czekanie, powroty), jeśli nie ma ich w danych.\n"
            . "6. CTA: jedno konkretne pytanie do ludzi albo jedno działanie. Pytanie ma wynikać z treści, nie może być retoryczne.\n"
            . "7. Wiarygodność: nic spoza FAKTÓW, DANYCH PIWA, WYTYCZNYCH i LOGISTYKI. Usuń każde zdanie, którego to nie pokrywa.\n"
            . "8. Długość: skracaj. Każde zdanie ma wnosić nową informację.\n\n"
            . "KROK B – REDAKCJA JĘZYKOWA. Przejdź każde zdanie według listy:\n"
            . "  1) odmiana i przypadki, szczególnie przymiotniki po przyimkach (np. „z paloną kawą”, nie „z palona kawą”; „w sobotę”, „po pracy”, „ze znajomymi”, „dla gości”);\n"
            . "  2) zgodność rodzaju, liczby i osoby między podmiotem a orzeczeniem;\n"
            . "  3) kalki z angielskiego i żargon urzędowy – zamień na polskie, naturalne zwroty;\n"
            . "  4) przecinki przed „że”, „który”, „jak”, przy wtrąceniach i w wyliczeniach;\n"
            . "  5) wielkie litery tylko tam, gdzie trzeba (hook, nazwy własne, początek zdania);\n"
            . "  6) polskie znaki diakrytyczne (ą, ę, ł, ó, ś, ż, ź, ć, ń);\n"
            . "  7) sens: każde zdanie ma jedną myśl i logiczny podmiot–orzeczenie–dopełnienie. Zdanie, które brzmi obco, przepisz prościej;\n"
            . "  8) konstrukcje kalkowane, np. „nie chcecie, żeby ciężko było pić” – zamień na naturalne zdanie („które da się pić cały wieczór”);\n"
            . "  9) potoczne „ciężko” w tekście posta zamień na „trudno”;\n"
            . "  10) tautologie: to samo wyjaśnione dwa razy – zostaw jedno wyjaśnienie.\n"
            . "- Pilnuj poprawnych związków wyrazowych z BANKU SŁOWNICTWA (np. „po pracy”, nie „po pracę”; piwo się warzy, leżakuje i trafia do kufli, nie przychodzi ani nie wpada).\n"
            . "- Tytuł/hook musi zgadzać się z treścią (np. nie „w kuflach”, tylko „już dostępne”).\n"
            . "- Zamień żargon branżowy na zrozumiałe słowa albo dopisz krótkie wyjaśnienie w tym samym zdaniu.\n"
            . "- Usuń zdania bez treści, pusty patos, slang (klawo, mega, super), zachęty typu „przekonaj się” i wykrzykniki ponad trzy. Emoji ponad sześć.\n"
            . "- Usuń zdania, które nie wnoszą faktu ani korzyści (np. „dobrze pasuje do wieczoru”, „to wyjątkowe piwo”), chyba że w ich miejsce wstawisz konkretną informację z danych. Jeśli CTA pyta o inny temat niż nowość w poście, przepisz je na pytanie o to piwo.\n"
            . "- Wyliczenia zapisz równolegle (te same przypadki i formy). Cechy typowe dla stylu przypisane temu piwu bez potwierdzenia w DANYCH PIWA przepisz jako cechy stylu („stouty zwykle mają pełne ciało”).\n"
            . "- Usuń prośby o interakcję dla zasięgu (oznacz, udostępnij, polub, napisz w komentarzu). Zostaw konkretne pytanie do czytelników, jeśli jest w tekście.\n"
            . "- Usuń zdania w treści deklarujące, że browar jest otwarty lub działa, chyba że jest to w WYTYCZNYCH. Linia 🕓 w stopce („Zapraszamy od …” / „Otwieramy o …”) zostaje bez zmian.\n"
            . "- Usuń powtórzenia daty, godziny i miejsca warzenia: każdą informację podaj raz.\n"
            . "- Usuń opisy smaku lub zapachu, jeśli nie wynikają z DANYCH PIWA; zastąp je konkretną informacją z tekstu.\n"
            . "- Usuń zdania o fanach, opiniach i sympatii gości („miał wielu fanów”, „pokochali”), jeśli nie ma ich w WYTYCZNYCH.\n"
            . "- Nie dodawaj nowych faktów o browarze, godzin, stanów lokalu ani wydarzeń.\n\n"
            . "ZAKAZANE SŁOWA (usuń każde, także w odmianach):\n" . $forbidden . "\n\n"
            . "Zachowaj strukturę: hook na początku, treść w akapitach lub punktach, linie 📍 i 🕓 na końcu oraz ewentualne hashtagi. Zachowaj zasady formy: emoji 3–6, wykrzykniki 1–3. Nie używaj markdownu.\n"
            . "Zwróć WYŁĄCZNIE poprawiony post, bez komentarza.\n\n"
            . $this->vocabularyPrompt() . "\n\n"
            . "DANE ŹRÓDŁOWE (jedyne, z czego wolno czerpać fakty):\n" . $sourceData . "\n\n"
            . "POST:\n" . $text;

        try {
            return $this->callApi($system, [['role' => 'user', 'content' => $user]]);
        } catch (SocialMediaGenerationException $e) {
            Log::warning('Ocena i redakcja posta nie powiodły się, zwrócono wersję bez zmian.', ['error' => $e->getMessage()]);

            return $text;
        }
    }

    private function correctionRequest(array $violations): string
    {
        $hints = collect(config('brewery.forbidden_terms', []))
            ->filter(fn ($term) => in_array($term['label'], $violations, true))
            ->map(fn ($term) => '- ' . $term['label'] . ': ' . $term['use'])
            ->implode("\n");

        return "Twój tekst zawiera zakazane sformułowania:\n" . implode(', ', $violations)
            . "\n\nWskazówki:\n" . $hints
            . "\n\nPrzepisz cały post bez tych sformułowań i bez wymyślonych informacji, zachowując strukturę i ton. Zwróć wyłącznie gotową treść posta.";
    }

    private function callApi(string $system, array $messages): string
    {
        $cfg = config('services.anthropic');

        try {
            $response = Http::withHeaders([
                    'x-api-key' => $cfg['key'],
                    'anthropic-version' => $cfg['version'],
                    'content-type' => 'application/json',
                ])
                ->timeout($cfg['timeout'])
                ->post(rtrim($cfg['base_url'], '/') . self::ENDPOINT, [
                    'model' => $cfg['model'],
                    'max_tokens' => $cfg['max_tokens'],
                    'system' => $system,
                    'messages' => $messages,
                ]);
        } catch (ConnectionException $e) {
            if (str_contains(strtolower($e->getMessage()), 'timed out') || str_contains(strtolower($e->getMessage()), 'timeout')) {
                throw new SocialMediaGenerationException(
                    'Generowanie trwało zbyt długo i zostało przerwane (timeout). Spróbuj ponownie.'
                );
            }

            throw new SocialMediaGenerationException(
                'Nie udało się połączyć z API Anthropic. Sprawdź połączenie internetowe i spróbuj ponownie.'
            );
        }

        if ($response->failed()) {
            throw new SocialMediaGenerationException($this->describeFailure($response));
        }

        $text = collect($response->json('content', []))
            ->where('type', 'text')
            ->pluck('text')
            ->implode('');

        // Usage zliczamy także przy pustej odpowiedzi – tokeny i tak zostały zużyte.
        $this->inputTokens += (int) $response->json('usage.input_tokens', 0);
        $this->outputTokens += (int) $response->json('usage.output_tokens', 0);

        $text = trim($text);

        if ($text === '') {
            if ($response->json('stop_reason') === 'max_tokens') {
                throw new SocialMediaGenerationException(
                    'Model przerwał generowanie za wcześnie (limit tokenów). Zwiększ ANTHROPIC_MAX_TOKENS w .env i wyczyść cache konfiguracji.'
                );
            }

            throw new SocialMediaGenerationException('API zwróciło pustą odpowiedź. Spróbuj wygenerować ponownie.');
        }

        return $text;
    }

    private function describeFailure(Response $response): string
    {
        $status = $response->status();
        $apiMessage = (string) $response->json('error.message', '');

        return match (true) {
            $status === 401 || $status === 403 => 'Klucz API Anthropic został odrzucony. Sprawdź ANTHROPIC_API_KEY w .env.',
            $status === 404 => 'Model "' . config('services.anthropic.model') . '" nie jest dostępny. Sprawdź ANTHROPIC_MODEL w .env.',
            $status === 429 => 'Przekroczono limit zapytań do API Anthropic. Odczekaj chwilę i spróbuj ponownie.',
            $status === 408 || $status === 504 => 'Generowanie trwało zbyt długo (timeout). Spróbuj ponownie.',
            $status >= 500 => 'API Anthropic chwilowo nie odpowiada (błąd ' . $status . '). Spróbuj ponownie za chwilę.',
            default => 'Generowanie nie powiodło się (HTTP ' . $status . ')' . ($apiMessage !== '' ? ': ' . $apiMessage : '.'),
        };
    }

    private function systemPrompt(): string
    {
        $brewery = config('brewery');
        $forbidden = collect($brewery['forbidden_terms'] ?? [])
            ->map(fn ($term) => '- ' . $term['label'] . ' – ' . $term['use'])
            ->implode("\n");

        $vocabulary = $this->vocabularyPrompt();
        $examples = $this->stylePrompt();

        return <<<PROMPT
Jesteś starszym specjalistą marketingu i copywriterem z wieloletnim doświadczeniem w gastronomii i piwie rzemieślniczym. Prowadzisz social media browaru {$brewery['name']} – rzemieślniczego brewpubu w mieście {$brewery['city']} (adres: {$brewery['address']}).
Piszesz posty na Facebooka i Instagrama w języku polskim. Każdy post piszesz tak, jakby miał zebrać komentarze prawdziwych gości: konkretnie, z własnym głosem i bez sztampy.

ZASADA FAKTÓW (najważniejsza)
- Jako pewne podawaj WYŁĄCZNIE informacje z sekcji FAKTY O BROWARZE, DANE PIWA, WYTYCZNE i LOGISTYKA.
- NIE wymyślaj: stanu lokalu (otwarty, zamknięty, kolejki, kurki, krany, kranowanie), świąt i okazji, wydarzeń, promocji, cen, liczby gości, opinii o smaku wykraczających poza dane piwa.
- Godzina z LOGISTYKI służy wyłącznie do stopki (linia 🕓: „Zapraszamy od …” albo „Otwieramy o …”). W treści nie pisz, że browar jest otwarty, działa ani nie opisuj stanu lokalu, chyba że WYTYCZNE to wyraźnie mówią.
- NIE wymyślaj cytatów ani pytań gości. Cytuj tylko pytania podane w WYTYCZNYCH.
- NIE wymyślaj opinii gości ani sympatii do piwa (np. „skradł wasze serca”, „pokochaliście”, „ulubione piwo gości”), chyba że WYTYCZNE to potwierdzają.
- NIE pisz o zmianach receptury (piana, pijalność, goryczka, „wersja 2.0” z innymi parametrami), jeśli DANE PIWA ani WYTYCZNE tego nie podają.
- NIE wymyślaj tajemnic ani zapowiedzi („nie zdradzamy stylu”, „zgadnijcie, co warzymy”, „szykujemy coś wyjątkowego”), jeśli nie ma ich w WYTYCZNYCH. Brak danych o piwie to powód, żeby pisać ogólniej, nie żeby budować napięcie.
- Jeśli brakuje informacji o konkretnym piwie, pisz ogólniej albo ją pomiń. Nie zgaduj.
- Ogólną wiedzę o stylu, sezonie i typowych składnikach (np. czym jest jesienne piwo dyniowe) MOŻESZ i MASZ dodać, żeby post miał treść. Dotyczy to wiedzy o temacie, nigdy stanu browaru ani smaku konkretnego piwa wykraczającego poza DANE PIWA i WYTYCZNE.
- Cechy typowe dla stylu (np. pełne ciało stouta) pisz jako cechy STYLU („stouty zwykle mają pełne ciało”), nie jako cechy tego piwa, chyba że DANE PIWA lub WYTYCZNE to podają.
- Jeśli WYTYCZNE mówią o temacie, rozwiń go w poście: co to za piwo lub wydarzenie, co z tym robimy i dlaczego warto przyjść. Nie zostawiaj posta z samymi logistykami.

ZAKAZANE SŁOWA (nigdy, także w odmianach)
{$forbidden}

TOŻSAMOŚĆ I TON
- Jesteś pasjonatem piwa, mówisz jak ktoś z browaru, kto naprawdę zna się na piwie, nie jak korporacja.
- Piszesz do wszystkich gości, bez zakładania płci, wieku ani towarzystwa. Do browaru przychodzą pary, kobiety, mężczyźni, grupy znajomych i rodziny. Nie pisz tylko o kumplach ani o męskim towarzystwie.
- Dynamika: energia, zaangażowanie i poczucie, że za piwem stoi konkretny zespół. Krótkie, mocne zdania, zwrot wprost do czytelnika (Wy/Ty), zaskakujące otwarcie i w środku posta zwrot wciągający czytelnika, np. „I wiecie co?”. Taki zwrot nie jest pytaniem do komentarzy, czyli nie jest CTA. Emocje pokazujesz przez rytm i treść, nie przez wykrzykniki w każdym zdaniu.
- Stan piwa musi zgadzać się z danymi: „w warzelni”, „w fermentacji”, „leżakuje” NIE oznacza „dostępne”. Słowo „dostępne” i „już w kuflach” tylko wtedy, gdy DANE lub WYTYCZNE mówią, że piwo można zamówić.
- Energia ma wynikać z tonu, nie z zmyślonych zdarzeń. NIE pisz, że goście o coś pytali, czegoś prosili lub na coś czekali, ani o powrocie piwa, jeśli nie wynika to z WYTYCZNYCH lub DANYCH PIWA.
- Zwracaj się do ludzi wprost, jak w rozmowie przy barze, bez slangu.
- Brzmisz jak piwowar, który pisze po udanym warzeniu z telefonem w ręku.
- ZAKAZANE zwroty i konstrukcje: "W dzisiejszym dynamicznym świecie", "Z dumą ogłaszamy", "W naszym browarze z pasją", "Zapraszamy serdecznie", "Nie czekaj", "Nie przegap", "Przekonaj się" / "Przekonajcie się", "Wpadnijcie na jedno, dwa albo na całe posiedzenie", "Różnicę czuć od pierwszego łyka", "Sprawdźcie, co dla Was uwarzyliśmy", "Widzimy się dziś w", "Ekipo,", slang (klawo, mega, super, top), ogólniki bez treści, puste przymiotniki bez pokrycia (pyszne, wyjątkowe, najlepsze).

CZYTELNOŚĆ (odbiorca to osoba, która nie zna się na piwie, ale interesuje się rzemieślniczym browarem)
- Pisz tak, żeby zrozumiała to osoba, która nigdy nie zamawiała piwa rzemieślniczego. Jeśli post wymaga znajomości branży, jest źle napisany.
- Termin branżowy (np. chmielenie, fermentacja, dry hopping, ekstrakt, słodowanie) używaj tylko z prostym wyjaśnieniem w tym samym zdaniu albo zamień go na zrozumiałe słowo. Np. „fermentacja, czyli moment, w którym drożdże zamieniają cukier w alkohol”.
- Skróty stylów (IPA, APA, NEIPA, Stout) rozwijaj przy pierwszym użyciu: „IPA, czyli mocno chmielone piwo o wyraźnej goryczce”.
- Parametrów (BLG, IBU, EBC) nie podawaj jak z laboratorium. Przełóż je na prosty opis, np. „mocniejsze niż zwykłe jasne piwo”.
- Smak opisuj przez skojarzenia z codzienności: cytrusy, karmel, kawa, chleb, przyprawy, a nie słowami branżowymi.
- Nie zakładaj, że czytelnik zna różnicę między stylem, chmielem i słodem. Jeśli o niej piszesz, wyjaśnij ją jednym zdaniem.
- Najpierw powiedz, co to jest, potem dlaczego warto. Od prostego do szczegółu.
- Jedno zdanie to jedna myśl. Unikaj zdań zagnieżdżonych i długich wyliczeń.

MARKETING (piszesz jak copywriter z doświadczeniem w gastronomii i piwie)
- Każdy post ma jeden cel: NOWOŚĆ (premiera), ZAPROSZENIE (wizyta), KULISY (jak powstaje piwo) albo EDUKACJA (styl). Wybierz jeden i trzymaj się go.
- Każdy post ma jedną korzyść dla gościa: czego spróbuje, co zobaczy, co dostanie. Korzyść pochodzi z FAKTÓW, DANYCH PIWA albo WYTYCZNYCH, nigdy z wyobraźni.
- Każdy post ma jedno działanie (CTA) w treści: przyjść (zaproszenie), zamówić przy barze (tylko gdy piwo jest dostępne) albo odpowiedzieć na pytanie w komentarzu. Stopka z adresem i terminem nie jest CTA. Nie łącz tych działań.
- Hook niesie konkretną informację: co się dzieje i dla kogo, np. „PUMPKIN ALE: DYNIOWA NOWOŚĆ W NASZYM BROWARZE!”. Ogólny zachwyt nie działa.
- Zmieniaj formułę hooka między postami. Używaj schematów: „DZIEŃ? HASŁO!” (np. "PIĄTEK? CZAS NA DOBRY RESET!"), „LUBLIN! NOWOŚĆ: NAZWA!” (np. "LUBLIN! WARZYMY HEFEWEIZENA!"), „NAZWA + jedna cecha piwa”.
- Każde zdanie wnosi nową informację albo konkretną korzyść. Wypełniacze („to dla nas ważne”, „wyjątkowy moment”) usuń.
- Ton: energiczny i ludzki, bez slangu. Entuzjazm ma wynikać z treści i rytmu, nie z przymiotników. Piszesz do wszystkich gości.
- Długość: zgodnie z PLATFORMA z danych (liczona bez stopki i hashtagów).
- Data i godzina: podaj raz, w stopce.
- Brak danych o smaku lub zapachu: nie opisujesz smaku. Piszesz o stylu albo o tym, co gość faktycznie dostaje.
- Krótkie WYTYCZNE = krótszy post. Nie wypełniaj tekstu ogólnikami („dobrze pasuje do wieczoru”, „to wyjątkowe piwo”), tylko dlatego, że brakuje treści.
- Hook i CTA dotyczą nowości z WYTYCZNYCH: hook zawiera nazwę piwa i jedną jego konkretną cechę z WYTYCZNYCH, a CTA pyta o to piwo albo o działanie wobec niego, nie o ogólny temat (np. „ciemne piwa przy jedzeniu”).

ZAANGAŻOWANIE (algorytm Facebooka nagradza rozmowę w komentarzach, nie samo kliknięcie)
- Jeśli wybierasz pytanie jako CTA, ma być konkretne i szczere, np. „Jesienią wolisz piwo dyniowe czy ciemne?”. Nie pytaj retorycznie i nie pytaj o zakupy.
- Hook obiecuje dokładnie to, co post dostarcza. Clickbait obniża zasięg.
- Podawaj konkrety (kiedy, gdzie, co z tym robimy), bo ludzie chętniej komentują konkrety niż ogólny zachwyt.
- ZAKAZANE są prośby o interakcję dla zasięgu: „oznacz znajomego”, „udostępnij”, „polub”, „napisz w komentarzu tak”.

POLSZCZYZNA (ma brzmieć jak napisane przez Polaka, nie przetłumaczone)
- Pisz czynnie i konkretnie: „warzymy”, „nalewamy”, „zamówisz przy barze”. Nie pisz stroną bierną ani rzeczownikami odsłownymi („dokonujemy warzenia”, „jest prowadzone”).
- Nie dokładaj zaimków: w polszczyźnie osobę niesie czasownik („warzymy”, nie „my warzymy”), chyba że zaimek coś wyraźnie podkreśla.
- Liczba, nie ilość, dla rzeczowników policzalnych („liczba gości”).
- Unikaj kalk i zwrotów z angielskiego (np. „na wyciągnięcie ręki”, „w dniu dzisiejszym”, „na bazie”). Mów naturalnie: „dziś”, „na podstawie”.
- Nazwy obce zostają nieodmienne, jeśli odmiana brzmi sztucznie: „piwo Pumpkin Ale”, „styl IPA”. Nie odmieniaj ich na siłę.
- Daty pisz bez przecinka: „w sobotę 15 listopada”.
- Przed „że”, „który”, „jak”, „ponieważ” stawiaj przecinek tam, gdzie wymaga tego zdanie.
- Każde zdanie czytaj na głos: jeśli brzmi sztucznie albo wymaga poprawy w dwóch miejscach, przepisz je prościej.
- Wyliczenia zapisuj równolegle, tymi samymi przypadkami i formami (np. „do jedzenia, na wieczór przy stole albo na koniec dnia”). Nie używaj trybu przypuszczającego („pasowałby”), gdy zwykły tryb oznajmujący brzmi naturalniej.

FORMA TEKSTU
- Akapity po 1–3 zdania. Zdania krótkie i dynamiczne. Post czytasz jak zapowiedź na tablicy, nie jak ulotkę.
- Zdania łącz spójnikami tam, gdzie to ma sens (bo, więc, a, ale). Nie zaczynaj każdego zdania w nowej linii.
- Lista z emoji jest dozwolona tylko w formacie (b) poniżej.
- Emoji: łącznie 3–6 w całym poście. Hook 1–2, w liście jedno przy każdym punkcie, linia 🕓 jedno. Gdy wychodzi więcej niż 6, pomiń emoji przy punktach listy.
- Wykrzykniki: 1–3 w całym poście, najczęściej w hooku i w linii z godziną.

STRUKTURA POSTA
1. HOOK – pierwszy akapit: WIELKIMI LITERAMI, konkretnie, 1–2 emoji. Pierwsze 125 znaków musi zatrzymać scrollowanie.
2. ŚRODEK – wybierz jeden format:
   (a) 1–2 krótkie akapity: co to za piwo lub klimat, opisy zmysłowe z banku słownictwa, po co przyjść;
   (b) lista 3–4 punktów, każdy w osobnej linii, zaczyna się emoji i nazwą, np. „🍺 NAZWA – krótki konkret”;
   (c) krótkie pytanie do ludzi i 1 akapit.
   Parametry (BLG, chmiele, słody) tłumacz na doświadczenie smakowe, nie podawaj ich jak z laboratorium. Nie wymyślaj parametrów, których nie podano.
3. ZAKOŃCZENIE – jedno zdanie CTA: pytanie do czytelników ALBO działanie gościa (nie oba), a potem stopka w osobnych liniach:
📍 [adres browaru]
🕓 [termin z LOGISTYKI: dzień tygodnia, data i godzina, w jednej z form: „Sobota 15 listopada, ZACZYNAMY O 16:00!”, „Sobota 15 listopada, startujemy o 16:00!”, „Sobota 15 listopada, wpadnij od 16:00!”, „Sobota 15 listopada, zapraszamy od 16:00”, „Sobota 15 listopada, otwieramy o 16:00”]
   Datę i godzinę podajesz tylko w tej linii. Poza nią nie pisz o terminie ani o godzinach otwarcia.
   Jeśli nie podano terminu, pomiń linię 🕓.
4. HASHTAGI – opcjonalnie, na samym końcu, w jednej linii, po pustej linii. Na Facebooku 0–3, na Instagramie 3–5. Hashtagi nie zastępują treści.

Nie używaj markdownu (żadnych **gwiazdek**, nagłówków #, list z myślnikami w tekście głównym). Nie dodawaj komentarzy ani wyjaśnień od siebie. Zwróć WYŁĄCZNIE gotową treść posta.

{$examples}

{$vocabulary}

PROMPT;
    }

    /**
     * Przykłady opublikowanych postów z aplikacji, żeby model uczył się naszego stylu (rytm, długość, hook, stopka).
     */
    private function stylePrompt(): string
    {
        $count = (int) config('brewery.style_examples_count', 5);

        if ($count <= 0) {
            return '';
        }

        $posts = SocialPost::query()
            ->where('status', SocialPost::STATUS_PUBLISHED)
            ->whereNotNull('generated_content')
            ->latest('id')
            ->limit($count * 4)
            ->pluck('generated_content')
            // Posty z zakazanymi sformułowaniami nie mogą uczyć modelu złych nawyków.
            ->reject(fn ($text) => $this->violations((string) $text) !== [])
            ->take($count);

        if ($posts->isEmpty()) {
            return '';
        }

        $lines = ['PRZYKŁADY OPUBLIKOWANYCH POSTÓW BROWARU (wzoruj się na stylu, rytmie, długości i układzie; NIE kopiuj zdań, dat, godzin, cen ani faktów z przykładów):'];

        foreach ($posts->values() as $i => $text) {
            $lines[] = '--- Przykład ' . ($i + 1) . ' ---';
            $lines[] = Str::limit(trim((string) $text), 1200, '…');
        }

        return implode("\n", $lines);
    }

    /**
     * Bank słownictwa browarniczego z config/brewing_vocabulary.php, zamieniony na sekcję promptu.
     */
    private function vocabularyPrompt(): string
    {
        $vocab = config('brewing_vocabulary', []);

        $lines = ['BANK SŁOWNICTWA BROWARNICZEGO (materiał pomocniczy, nie lista do przepisania)'];

        if (!empty($vocab['terminology'])) {
            $lines[] = 'Nazewnictwo (używaj konsekwentnie):';
            foreach ($vocab['terminology'] as $word => $meaning) {
                $lines[] = '- „' . $word . '” – ' . $meaning;
            }
        }

        if (!empty($vocab['collocations'])) {
            $lines[] = 'Poprawne związki wyrazowe: ' . implode('; ', $vocab['collocations']) . '.';
        }

        if (!empty($vocab['common_errors'])) {
            $lines[] = 'Typowe błędy – NIE pisz tak:';
            foreach ($vocab['common_errors'] as $wrong => $right) {
                $lines[] = '- „' . $wrong . '” → ' . $right;
            }
        }


        $labels = [
            'aroma' => 'Zapach',
            'taste' => 'Smak i odczucia w ustach',
            'look' => 'Wygląd i piana',
            'process' => 'Proces warzenia (tylko kulisy i edukacja)',
            'community' => 'Relacje z ludźmi',
            'hops' => 'Chmiel (odmiany i charakter)',
            'malts' => 'Słody (rodzaje i charakter)',
        ];

        foreach ($labels as $key => $label) {
            if (!empty($vocab[$key])) {
                $lines[] = $label . ': ' . implode(', ', $vocab[$key]) . '.';
            }
        }

        if (!empty($vocab['styles'])) {
            $lines[] = 'Style – profile (wiedza o stylu): ' . collect($vocab['styles'])
                ->map(fn ($desc, $style) => $style . ' (' . $desc . ')')
                ->implode('; ') . '.';
        }

        $lines[] = '';
        $lines[] = 'JAK Z TEGO KORZYSTAĆ:';
        foreach ($vocab['usage_rules'] ?? [] as $rule) {
            $lines[] = '- ' . $rule;
        }

        return implode("\n", $lines);
    }

    private function userPrompt(
        string $topicType,
        ?Recipe $recipe,
        ?string $customTopic,
        ?string $guidelines,
        string $platform,
        ?CarbonInterface $eventAt,
        ?string $address,
    ): string {
        $lines = [];

        $lines[] = 'FAKTY O BROWARZE (jedyne informacje, które możesz podać jako pewne):';
        foreach (config('brewery.facts', []) as $fact) {
            $lines[] = '- ' . $fact;
        }

        $lines[] = '';
        $lines[] = 'RODZAJ POSTA: ' . $this->topicInstruction($topicType, $customTopic, $recipe !== null);

        if ($recipe) {
            $lines[] = '';
            $lines[] = 'DANE PIWA (receptura z browaru):';
            $lines[] = '- Nazwa: ' . $recipe->name;
            $lines[] = '- Styl: ' . ($recipe->style ?: 'nie podano – nie zgaduj stylu');

            if ($profile = $this->styleProfile($recipe->style)) {
                $lines[] = '- Profil stylu (ogólna wiedza o stylu, trzymaj się jej): ' . $profile;
            }

            if ($recipe->blg) {
                $lines[] = '- Ekstrakt brzeczki: ' . $recipe->blg . ' °BLG (przetłumacz na wrażenie w ustach, nie podawaj jak raportu)';
            }

            $hops = $recipe->hops()->get();
            if ($hops->isNotEmpty()) {
                $lines[] = '- Chmiele: ' . $hops->pluck('name')->filter()->unique()->implode(', ');
            }

            $malts = $recipe->malts()->get();
            if ($malts->isNotEmpty()) {
                $lines[] = '- Słody: ' . $malts->pluck('name')->filter()->unique()->implode(', ');
            }
        }

        if ($guidelines = trim((string) $guidelines)) {
            $lines[] = '';
            $lines[] = 'WYTYCZNE OD ZESPOŁU (uwzględnij je obowiązkowo i rozwiń temat, to jedyne źródło dodatkowych informacji o browarze i piwie):';
            $lines[] = $guidelines;
        }

        $lines[] = '';
        $lines[] = 'LOGISTYKA (użyj w zakończeniu):';
        $lines[] = '- Adres: ' . ($address ?: config('brewery.address'));
        if ($eventAt) {
            $lines[] = '- Termin (dzień i godzina do linii 🕓 w stopce): ' . $eventAt->locale('pl')->translatedFormat('l j F, H:i');
        }
        if (filled(config('brewery.opening_hours'))) {
            $lines[] = '- Godziny otwarcia (to NIE jest termin wydarzenia; nie wpisuj ich do linii 🕓): ' . config('brewery.opening_hours');
        }

        $lines[] = '';
        $lines[] = 'PLATFORMA: ' . match ($platform) {
            SocialPost::PLATFORM_FB => 'Facebook – post może być nieco dłuższy (do ok. 120 słów), czytelny, zakończony jednym CTA (najlepiej pytaniem do komentarzy), 0–3 hashtagi.',
            SocialPost::PLATFORM_IG => 'Instagram – podpis do zdjęcia, zwięzły (do ok. 80 słów), hook w pierwszym zdaniu, jedno CTA, 3–5 hashtagów na końcu.',
            default => 'Facebook i Instagram jednocześnie – tekst uniwersalny, do ok. 100 słów, zakończony jednym CTA (najlepiej pytaniem do komentarzy), 0–3 hashtagi.',
        };

        $lines[] = '';
        $lines[] = 'Napisz gotowy post zgodnie z zasadami z promptu systemowego: hook, środek, jedno CTA i stopka. Zdania łącz w akapity, nie łam każdego zdania osobno.';

        return implode("\n", $lines);
    }

    /**
     * Profil stylu z banku (dopasowanie bez względu na wielkość liter), albo null, gdy stylu nie ma w banku.
     */
    private function styleProfile(?string $style): ?string
    {
        if (blank($style)) {
            return null;
        }

        $needle = mb_strtolower(trim($style));

        foreach (config('brewing_vocabulary.styles', []) as $name => $profile) {
            if (mb_strtolower($name) === $needle) {
                return $profile;
            }
        }

        return null;
    }

    private function topicInstruction(string $topicType, ?string $customTopic, bool $hasRecipe): string
    {
        return match ($topicType) {
            SocialPost::TOPIC_NEW_BEER => $hasRecipe
                ? 'PREMIERA PIWA – ogłaszamy nowe piwo z DANYCH PIWA. Skup się na tym, że właśnie się pojawiło.'
                : 'PREMIERA – ogłaszamy nowość w browarze, ale nie podano konkretnego piwa. Nie wymyślaj nazwy, stylu ani parametrów piwa.',
            SocialPost::TOPIC_BEHIND_SCENES => 'Z ŻYCIA BROWARU / KULISY WARZENIA – pokaż pracę przy warzeniu, z energią piwowara. Opieraj się wyłącznie na FAKTACH i WYTYCZNYCH.',
            SocialPost::TOPIC_WEEKEND => 'WEEKEND / ZAPROSZENIE – zaproś gości do odwiedzenia browaru na weekend. Nie opisuj stanu lokalu ani szczegółów wyposażenia.',
            SocialPost::TOPIC_EDUCATION => 'EDUKACJA PIWNA / O STYLACH – krótko i przystępnie wyjaśnij styl lub zjawisko, bez akademickiego tonu.',
            SocialPost::TOPIC_CUSTOM => 'WŁASNY TEMAT – ' . trim((string) $customTopic),
            default => 'Luźny post o browarze i piwie.',
        };
    }
}
