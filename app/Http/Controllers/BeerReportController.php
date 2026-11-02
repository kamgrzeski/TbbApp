<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BeerReportController extends Controller
{
    public function index()
    {
        return view('beer-report', ['report' => null]);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:5120',
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');

        fgetcsv($handle, 1000, ","); // Pomijamy nagłówek

        $originalName = $file->getClientOriginalName();
        $period = $this->extractPeriodFromFilename($originalName);

        $beers = [];
        $others = [];
        $discountPerProduct = [];
        $totalRevenue = 0;
        $totalLiters = 0;
        $totalBeerUnits = 0;
        $totalSnackUnits = 0;
        $totalDiscounts = 0;

        $total500mlCount = 0;
        $petLiters = 0;
        $petRevenue = 0;
        $petDiscounts = 0;

        $formatTotals = [];

        while (($row = fgetcsv($handle, 1000, ",")) !== false) {
            if (empty($row) || !isset($row[0])) continue;

            $productName = trim($row[0]);
            $qty = (int) $this->cleanNumber($row[1] ?? 0);
            $revenue = (float) $this->cleanNumber($row[2] ?? 0);
            $discount = (float) $this->cleanNumber($row[4] ?? 0);

            if ($qty <= 0 && $revenue <= 0) continue;

            $totalDiscounts += $discount;

            if ($discount > 0) {
                $discountPerProduct[$productName] = ($discountPerProduct[$productName] ?? 0) + $discount;
            }

            // 1. DYNAMICZNA WERYFIKACJA CZY TO PIWO CZY INNY ASORTYMENT
            if ($this->isBeer($productName)) {
                $cleanedName = preg_replace('/^(Kopia\s+|Copy\s+)/ui', '', $productName);
                $volume = $this->extractVolume($cleanedName);
                $baseBeerName = $this->extractBaseBeerName($cleanedName);
                $formatName = $this->extractFormatName($cleanedName);
                $liters = $qty * $volume;

                if (!isset($beers[$baseBeerName])) {
                    $beers[$baseBeerName] = [
                        'name' => $baseBeerName,
                        'revenue' => 0,
                        'liters' => 0,
                        'units' => 0,
                        'discounts' => 0,
                        'formats' => []
                    ];
                }

                $beers[$baseBeerName]['revenue'] += $revenue;
                $beers[$baseBeerName]['liters'] += $liters;
                $beers[$baseBeerName]['units'] += $qty;
                $beers[$baseBeerName]['discounts'] += $discount;

                if (!isset($beers[$baseBeerName]['formats'][$formatName])) {
                    $beers[$baseBeerName]['formats'][$formatName] = [
                        'qty' => 0,
                        'liters' => 0,
                        'revenue' => 0,
                        'discounts' => 0
                    ];
                }
                $beers[$baseBeerName]['formats'][$formatName]['qty'] += $qty;
                $beers[$baseBeerName]['formats'][$formatName]['liters'] += $liters;
                $beers[$baseBeerName]['formats'][$formatName]['revenue'] += $revenue;
                $beers[$baseBeerName]['formats'][$formatName]['discounts'] += $discount;

                $totalLiters += $liters;
                $totalBeerUnits += $qty;

                // Statystyki formatów
                if (!isset($formatTotals[$formatName])) {
                    $formatTotals[$formatName] = ['liters' => 0, 'revenue' => 0, 'units' => 0];
                }
                $formatTotals[$formatName]['liters'] += $liters;
                $formatTotals[$formatName]['revenue'] += $revenue;
                $formatTotals[$formatName]['units'] += $qty;

                if (str_contains($formatName, '500 ml')) {
                    $total500mlCount += $qty;
                }
                if (str_contains(mb_strtoupper($formatName), 'PET')) {
                    $petLiters += $liters;
                    $petRevenue += $revenue;
                    $petDiscounts += $discount;
                }
            } else {
                $others[] = [
                    'name' => $productName,
                    'qty' => $qty,
                    'revenue' => $revenue
                ];

                if ($this->isSnack($productName)) {
                    $totalSnackUnits += $qty;
                }
            }

            $totalRevenue += $revenue;
        }

        fclose($handle);

        // Sortowanie piw wg przychodu malejąco
        uasort($beers, fn($a, $b) => $b['revenue'] <=> $a['revenue']);
        $beerTotalRevenue = array_sum(array_column($beers, 'revenue'));

        $colorPalettes = [
            ['border' => 'border-amber-400', 'text' => 'text-amber-400', 'badge_bg' => 'bg-amber-500/20', 'badge_text' => 'text-amber-300', 'hex' => '#f59e0b'],
            ['border' => 'border-emerald-400', 'text' => 'text-emerald-400', 'badge_bg' => 'bg-emerald-500/20', 'badge_text' => 'text-emerald-300', 'hex' => '#10b981'],
            ['border' => 'border-orange-500', 'text' => 'text-orange-400', 'badge_bg' => 'bg-orange-500/20', 'badge_text' => 'text-orange-300', 'hex' => '#f97316'],
            ['border' => 'border-purple-500', 'text' => 'text-purple-400', 'badge_bg' => 'bg-purple-500/20', 'badge_text' => 'text-purple-300', 'hex' => '#8b5cf6'],
            ['border' => 'border-yellow-400', 'text' => 'text-yellow-400', 'badge_bg' => 'bg-yellow-500/20', 'badge_text' => 'text-yellow-300', 'hex' => '#eab308'],
            ['border' => 'border-cyan-400', 'text' => 'text-cyan-400', 'badge_bg' => 'bg-cyan-500/20', 'badge_text' => 'text-cyan-300', 'hex' => '#06b6d4'],
            ['border' => 'border-pink-500', 'text' => 'text-pink-400', 'badge_bg' => 'bg-pink-500/20', 'badge_text' => 'text-pink-300', 'hex' => '#ec4899'],
            ['border' => 'border-indigo-400', 'text' => 'text-indigo-400', 'badge_bg' => 'bg-indigo-500/20', 'badge_text' => 'text-indigo-300', 'hex' => '#6366f1'],
        ];

        $accumulatedRevenue = 0;
        $i = 0;
        foreach ($beers as $key => &$beer) {
            $palette = $colorPalettes[$i % count($colorPalettes)];
            $beer['theme'] = $palette;
            $beer['rank'] = $i + 1;
            $beer['price_per_liter'] = $beer['liters'] > 0 ? round($beer['revenue'] / $beer['liters'], 2) : 0;

            $accumulatedRevenue += $beer['revenue'];
            $cumulativeShare = $beerTotalRevenue > 0 ? ($accumulatedRevenue / $beerTotalRevenue) * 100 : 0;

            if ($cumulativeShare <= 80) {
                $beer['abc_class'] = 'A';
                $beer['abc_label'] = 'Klasa A (Kluczowe)';
                $beer['abc_badge'] = 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40';
            } elseif ($cumulativeShare <= 95) {
                $beer['abc_class'] = 'B';
                $beer['abc_label'] = 'Klasa B (Uzupełniające)';
                $beer['abc_badge'] = 'bg-blue-500/20 text-blue-300 border-blue-500/40';
            } else {
                $beer['abc_class'] = 'C';
                $beer['abc_label'] = 'Klasa C (Ogon sprzedaży)';
                $beer['abc_badge'] = 'bg-gray-700 text-gray-300 border-gray-600';
            }
            $i++;
        }

        $attachRate = $totalBeerUnits > 0 ? round(($totalSnackUnits / $totalBeerUnits) * 100, 1) : 0;
        $onPremiseLiters = $totalLiters - $petLiters;
        $onPremisePercent = $totalLiters > 0 ? round(($onPremiseLiters / $totalLiters) * 100, 1) : 0;
        $takeoutPercent = $totalLiters > 0 ? round(($petLiters / $totalLiters) * 100, 1) : 0;

        $report = [
            'totalRevenue' => $totalRevenue,
            'beerRevenue' => $beerTotalRevenue,
            'totalLiters' => $totalLiters,
            'totalBeerUnits' => $totalBeerUnits,
            'totalDiscounts' => $totalDiscounts,
            'attachRate' => $attachRate,
            'totalSnackUnits' => $totalSnackUnits,
            'onPremiseLiters' => $onPremiseLiters,
            'onPremisePercent' => $onPremisePercent,
            'petLiters' => $petLiters,
            'takeoutPercent' => $takeoutPercent,
            'petRevenue' => $petRevenue,
            'petDiscounts' => $petDiscounts,
            'formatTotals' => $formatTotals,
            'beers' => $beers,
            'others' => $others,
            'chartLabels' => array_values(array_keys($beers)),
            'chartRevenue' => array_values(array_column($beers, 'revenue')),
            'chartLiters' => array_values(array_map(fn($l) => round($l, 1), array_column($beers, 'liters'))),
            'chartColors' => array_values(array_map(fn($b) => $b['theme']['hex'], $beers)),
            'period' => $period,
        ];

        return view('beer-report', compact('report'));
    }

    private function cleanNumber($val)
    {
        return str_replace([' ', ','], ['', '.'], $val);
    }

    // UNIWERSALNE WYKRYWANIE PIWA (DZIAŁA DLA KAŻDEGO STYLU ŚWIATA)
    private function isBeer(string $name): bool
    {
        $nameLower = mb_strtolower($name);

        // Wykluczenia (np. gadżety ze słowem kufel, szklanka)
        if (str_contains($nameLower, 'kufel') || str_contains($nameLower, 'szklank') || str_contains($nameLower, 'koszulk')) {
            return false;
        }

        $beerKeywords = [
            'piwo', 'ipa', 'apa', 'pils', 'ale', 'stout', 'helles', 'porter', 'sour',
            'gose', 'weizen', 'pszeniczn', 'lager', 'koźlak', 'bock', 'witbier',
            'trippel', 'dubbel', 'barleywine', 'bezalkohol', 'cydr', 'miodowe'
        ];

        foreach ($beerKeywords as $kw) {
            if (str_contains($nameLower, $kw)) return true;
        }

        return false;
    }

    // UNIWERSALNE WYKRYWANIE PRZEKĄSEK
    private function isSnack(string $name): bool
    {
        $nameLower = mb_strtolower($name);
        $snackKeywords = [
            'chips', 'dupetk', 'orzech', 'orzeszk', 'meteoritk', 'krążki',
            'paluszk', 'nachos', 'popcorn', 'kabanos', 'frytk', 'tatar', 'precel', 'snack'
        ];

        foreach ($snackKeywords as $kw) {
            if (str_contains($nameLower, $kw)) return true;
        }

        return false;
    }

    // AUTOMATYCZNE WYCIĄGANIE POJEMNOŚCI W LITRACH
    private function extractVolume(string $name): float
    {
        $nameLower = mb_strtolower($name);

        if (str_contains($nameLower, '1l pet') || str_contains($nameLower, '1 l pet')) return 1.0;
        if (str_contains($nameLower, '1000 ml') || str_contains($nameLower, '1000ml')) return 1.0;
        if (str_contains($nameLower, '500 ml') || str_contains($nameLower, '500ml')) return 0.5;
        if (str_contains($nameLower, '250 ml') || str_contains($nameLower, '250ml')) return 0.25;

        return 0.5; // Domyślnie duży kufel
    }

    // ODCINANIE POJEMNOŚCI Z NAZWY PIWA (POZOSTAWIA CZYSTY STYL PIWA)
    private function extractBaseBeerName(string $name): string
    {
        // 1. Usuń prefiksy Kopia / Copy
        $clean = preg_replace('/^(Kopia\s+|Copy\s+)/ui', '', trim($name));

        // 2. Odetnij format z końca (np. 500 ml, 250ml, 1L PET, 1000ml)
        $clean = preg_replace('/\s*(1\s*L\s*PET|1000\s*ml|500\s*ml|250\s*ml)$/ui', '', $clean);
        $clean = trim($clean);

        // 3. NAPRAWA BŁĘDU: Zamień "Piwo Stout #6" na "Piwo #6 Stout"
        if (preg_match('/^Piwo\s+([a-zA-Z]+)\s*#(\d+)$/ui', $clean, $matches)) {
            $clean = "Piwo #{$matches[2]} " . trim($matches[1]);
        }

        // 4. Zamień wielokrotne spacje na pojedynczą
        $clean = preg_replace('/\s+/', ' ', $clean);

        return trim($clean);
    }

    // NAZWA FORMATU DO PREZENTACJI
    private function extractFormatName(string $name): string
    {
        $nameLower = mb_strtolower($name);

        if (str_contains($nameLower, 'pet')) return '1L PET (Na wynos)';
        if (str_contains($nameLower, '1000')) return '1000 ml';
        if (str_contains($nameLower, '500')) return '500 ml';
        if (str_contains($nameLower, '250')) return '250 ml';

        return 'Standard';
    }

    // AUTOMATYCZNE WYCIĄGANIE MIESIĄCA I ROKU Z NAZWY PLIKU
    private function extractPeriodFromFilename(string $filename): array
    {
        $months = [
            1 => 'Styczeń', 2 => 'Luty', 3 => 'Marzec', 4 => 'Kwiecień',
            5 => 'Maj', 6 => 'Czerwiec', 7 => 'Lipiec', 8 => 'Sierpień',
            9 => 'Wrzesień', 10 => 'Październik', 11 => 'Listopad', 12 => 'Grudzień'
        ];

        // Szuka wzorca DD_MM_YYYY (np. 02_11_2026) nawet z dopiskiem (1).csv
        if (preg_match('/(\d{1,2})_(\d{1,2})_(\d{4})/u', $filename, $matches)) {
            $day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
            $monthNum = (int) $matches[2];
            $year = $matches[3];

            if ($monthNum >= 1 && $monthNum <= 12) {
                $monthName = $months[$monthNum];
                return [
                    'date' => "{$day}.{$matches[2]}.{$year}",
                    'month' => $monthName,
                    'year' => $year,
                    'monthYear' => "{$monthName} {$year}",
                    'filename' => $filename,
                ];
            }
        }

        // Wartość awaryjna jeśli nazwa pliku nie zawiera daty
        return [
            'date' => date('d.m.Y'),
            'month' => 'Bieżący okres',
            'year' => date('Y'),
            'monthYear' => 'Bieżący okres',
            'filename' => $filename,
        ];
    }
}