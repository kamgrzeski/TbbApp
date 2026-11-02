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

        $beers = [];
        $others = [];
        $totalRevenue = 0;
        $totalLiters = 0;
        $totalUnits = 0;

        // Statystyki do wniosków biznesowych
        $total500mlCount = 0;
        $petLiters = 0;
        $petRevenue = 0;

        while (($row = fgetcsv($handle, 1000, ",")) !== false) {
            if (empty($row) || !isset($row[0])) continue;

            $productName = trim($row[0]);
            $qty = (int) $this->cleanNumber($row[1] ?? 0);
            $revenue = (float) $this->cleanNumber($row[2] ?? 0);

            if ($this->isBeer($productName)) {
                $cleanedName = preg_replace('/^Kopia\s+/ui', '', $productName);
                $volume = $this->extractVolume($cleanedName);
                $baseBeerName = $this->getBaseBeerName($cleanedName);
                $liters = $qty * $volume;

                if (!isset($beers[$baseBeerName])) {
                    $beers[$baseBeerName] = [
                        'name' => $baseBeerName,
                        'revenue' => 0,
                        'liters' => 0,
                        'units' => 0,
                        'formats' => []
                    ];
                }

                $beers[$baseBeerName]['revenue'] += $revenue;
                $beers[$baseBeerName]['liters'] += $liters;
                $beers[$baseBeerName]['units'] += $qty;

                $formatName = $this->extractFormatName($cleanedName);
                if (!isset($beers[$baseBeerName]['formats'][$formatName])) {
                    $beers[$baseBeerName]['formats'][$formatName] = [
                        'qty' => 0,
                        'liters' => 0,
                        'revenue' => 0
                    ];
                }
                $beers[$baseBeerName]['formats'][$formatName]['qty'] += $qty;
                $beers[$baseBeerName]['formats'][$formatName]['liters'] += $liters;
                $beers[$baseBeerName]['formats'][$formatName]['revenue'] += $revenue;

                $totalLiters += $liters;
                $totalUnits += $qty;

                // Dane do wniosków biznesowych
                if (str_contains($formatName, '500 ml')) {
                    $total500mlCount += $qty;
                }
                if (str_contains($formatName, 'PET')) {
                    $petLiters += $liters;
                    $petRevenue += $revenue;
                }
            } else {
                $others[] = [
                    'name' => $productName,
                    'qty' => $qty,
                    'revenue' => $revenue
                ];
            }

            $totalRevenue += $revenue;
        }

        fclose($handle);

        // Sortowanie piw wg przychodu malejąco
        uasort($beers, fn($a, $b) => $b['revenue'] <=> $a['revenue']);

        // Paleta stylizacji dla poszczególnych boksów piw
        $colorPalettes = [
            ['border' => 'border-amber-400', 'text' => 'text-amber-400', 'badge_bg' => 'bg-amber-500/20', 'badge_text' => 'text-amber-300', 'hex' => '#f59e0b'],
            ['border' => 'border-emerald-400', 'text' => 'text-emerald-400', 'badge_bg' => 'bg-emerald-500/20', 'badge_text' => 'text-emerald-300', 'hex' => '#10b981'],
            ['border' => 'border-orange-500', 'text' => 'text-orange-400', 'badge_bg' => 'bg-orange-500/20', 'badge_text' => 'text-orange-300', 'hex' => '#f97316'],
            ['border' => 'border-purple-500', 'text' => 'text-purple-400', 'badge_bg' => 'bg-purple-500/20', 'badge_text' => 'text-purple-300', 'hex' => '#8b5cf6'],
            ['border' => 'border-yellow-400', 'text' => 'text-yellow-400', 'badge_bg' => 'bg-yellow-500/20', 'badge_text' => 'text-yellow-300', 'hex' => '#eab308'],
            ['border' => 'border-cyan-400', 'text' => 'text-cyan-400', 'badge_bg' => 'bg-cyan-500/20', 'badge_text' => 'text-cyan-300', 'hex' => '#06b6d4'],
            ['border' => 'border-pink-500', 'text' => 'text-pink-400', 'badge_bg' => 'bg-pink-500/20', 'badge_text' => 'text-pink-300', 'hex' => '#ec4899'],
        ];

        $chartColors = [];
        $i = 0;
        foreach ($beers as $key => &$beer) {
            $palette = $colorPalettes[$i % count($colorPalettes)];
            $beer['theme'] = $palette;
            $beer['rank'] = $i + 1;
            $chartColors[] = $palette['hex'];
            $i++;
        }

        $beerTotalRevenue = array_sum(array_column($beers, 'revenue'));

        // Dynamiczne wyliczenie TOP 2 do wniosków biznesowych
        $beerValues = array_values($beers);
        $top1Name = $beerValues[0]['name'] ?? 'Brak';
        $top2Name = $beerValues[1]['name'] ?? 'Brak';
        $topTwoRevenue = ($beerValues[0]['revenue'] ?? 0) + ($beerValues[1]['revenue'] ?? 0);
        $topTwoShare = $beerTotalRevenue > 0 ? round(($topTwoRevenue / $beerTotalRevenue) * 100, 1) : 0;

        $report = [
            'totalRevenue' => $totalRevenue,
            'beerRevenue' => $beerTotalRevenue,
            'totalLiters' => $totalLiters,
            'totalUnits' => $totalUnits,
            'beers' => $beers,
            'others' => $others,
            'chartLabels' => array_keys($beers),
            'chartRevenue' => array_column($beers, 'revenue'),
            'chartLiters' => array_map(fn($l) => round($l, 1), array_column($beers, 'liters')),
            'chartColors' => $chartColors,
            // Wnioski biznesowe
            'insights' => [
                'top1' => $top1Name,
                'top2' => $top2Name,
                'topTwoRevenue' => $topTwoRevenue,
                'topTwoShare' => $topTwoShare,
                'total500mlCount' => $total500mlCount,
                'petLiters' => $petLiters,
                'petRevenue' => $petRevenue,
            ]
        ];

        return view('beer-report', compact('report'));
    }

    private function cleanNumber($val)
    {
        return str_replace([' ', ','], ['', '.'], $val);
    }

    private function isBeer(string $name): bool
    {
        $nameLower = mb_strtolower($name);
        return str_contains($nameLower, 'piwo') ||
            str_contains($nameLower, 'ipa') ||
            str_contains($nameLower, 'pils') ||
            str_contains($nameLower, 'ale') ||
            str_contains($nameLower, 'stout') ||
            str_contains($nameLower, 'helles');
    }

    private function extractVolume(string $name): float
    {
        if (preg_match('/(1000\s*ml|1\s*l|1l)/ui', $name)) return 1.0;
        if (preg_match('/500\s*ml/ui', $name)) return 0.5;
        if (preg_match('/250\s*ml/ui', $name)) return 0.25;
        return 0.5;
    }

    private function getBaseBeerName(string $name): string
    {
        $patterns = [
            '/Piwo #5 Helles/ui' => 'Piwo #5 Helles',
            '/Piwo #5 Miodowe/ui' => 'Piwo #5 Miodowe (Helles)',
            '/Piwo #6 Stout/ui' => 'Piwo #6 Stout',
            '/Piwo #7 NZ IPA/ui' => 'Piwo #7 NZ IPA',
            '/Piwo #7 Pumpkin Ale/ui' => 'Piwo #7 Pumpkin Ale',
            '/IPA Bezalkoholowe/ui' => 'IPA Bezalkoholowe',
            '/Pils klasyczny/ui' => 'Pils Klasyczny',
        ];

        foreach ($patterns as $pattern => $cleanName) {
            if (preg_match($pattern, $name)) return $cleanName;
        }

        return preg_replace('/\s+(1000\s*ml|1L\s*PET|500\s*ml|250\s*ml).*$/ui', '', $name);
    }

    private function extractFormatName(string $name): string
    {
        if (preg_match('/1L\s*PET/ui', $name)) return '1L PET (Na wynos)';
        if (preg_match('/1000\s*ml/ui', $name)) return '1000 ml (Kufel)';
        if (preg_match('/500\s*ml/ui', $name)) return '500 ml (Lany/Butelka)';
        if (preg_match('/250\s*ml/ui', $name)) return '250 ml (Mały)';
        return 'Standard';
    }
}