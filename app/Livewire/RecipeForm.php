<?php

namespace App\Livewire;

use App\Models\RecipeBatches;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\Computed;
use App\Models\Recipe;
use App\Models\MaltStock;

class RecipeForm extends Component
{
    public $recipeId = null;
    public $isClone = false;

    // Pola modelu
    public $recipe_name = '';
    public $style = '';
    public $tank_number = 2;
    public $batch_count = 2;
    public $efficiency = 75;

    public $yeast_pitch_temperature = 0;
    public $fermentation_temperature = 0;
    // Składniki
    public $malts = [];
    public $hops = [];
    public float $akcpa_blg = 0.0;
    public string $gopos_item_ids;

    #[Computed]
    public function availableMalts()
    {
        return MaltStock::orderBy('name')
            ->get(['id', 'name', 'extract', 'kg', 'is_enzymatic_active']);
    }

    public function mount($recipe = null, $isClone = false)
    {
        $this->isClone = $isClone;

        if ($recipe) {
            $this->recipeId = $isClone ? null : $recipe->id;
            $this->recipe_name = $isClone ? $recipe->name . ' (Kopia)' : $recipe->name;
            $this->style = (string) ($recipe->style ?? '');
            $this->tank_number = $recipe->tank_number;
            $this->batch_count = $recipe->batch_count;
            $this->yeast_pitch_temperature = $recipe->yeast_pitch_temperature;
            $this->fermentation_temperature = $recipe->fermentation_temperature;
            $this->akcpa_blg = $recipe->akcpa_blg;
            $this->efficiency = $recipe->efficiency ?? 75;

            if (is_array($recipe->gopos_item_ids)) {
                $this->gopos_item_ids = implode(', ', $recipe->gopos_item_ids);
            } else {
                $this->gopos_item_ids = (string) $recipe->gopos_item_ids;
            }

            // Mapowanie słodów z bazy
            $this->malts = $recipe->malts->map(fn($m) => [
                'batch_number' => $m->batch_number,
                'malt_stock_id' => $m->malt_stock_id,
                'name' => $m->name,
                'kg' => $m->kg,
                'is_active' => $m->maltStock ? (bool)$m->maltStock->is_enzymatic_active : false,
            ])->toArray();

            // Mapowanie chmieli z bazy
            $this->hops = $recipe->hops->map(fn($h) => [
                'batch_number' => $h->batch_number,
                'name' => $h->name,
                'amount' => $h->amount,
                'alpha_acids' => $h->alpha_acids,
                'time' => $h->time,
                'is_active' => (bool)$h->is_active,
            ])->toArray();
        } else {
            // Zamiast domyślnego 'Słód Pilzneński', dodajemy puste wiersze 
            // aby użytkownik musiał wybrać słód z listy.
            $this->addMalt(1);
            $this->addMalt(2);
            $this->addHop(1);
            $this->addHop(2);
        }
    }

    public function addMalt($batch, $name = '', $kg = 0)
    {
        $this->malts[] = [
            'batch_number' => $batch,
            'name' => $name,
            'kg' => $kg,
            'malt_stock_id' => null,
            'extract' => 0,
            'is_active' => false,
        ];
    }

    public function updateMaltExtract($index, $maltId)
    {
        $malt = MaltStock::find($maltId);
        if ($malt) {
            $this->malts[$index]['malt_stock_id'] = $maltId;
            $this->malts[$index]['name'] = $malt->name;
            $this->malts[$index]['extract'] = (float)$malt->extract;
            $this->malts[$index]['is_active'] = (bool)$malt->is_enzymatic_active;
        }
    }

    public function removeMalt($index)
    {
        unset($this->malts[$index]);
        $this->malts = array_values($this->malts);
    }

    public function addHop($batch)
    {
        $this->hops[] = [
            'batch_number' => $batch,
            'name' => '',
            'amount' => 0,
            'alpha_acids' => 0,
            'time' => 60,
            'is_active' => true
        ];
    }

    public function removeHop($index)
    {
        unset($this->hops[$index]);
        $this->hops = array_values($this->hops);
    }

    // Obliczenia w locie (Reaktywne podsumowanie)
    #[Computed]
    public function stats()
    {
        $vBatch = 500;
        $eff = (float)$this->efficiency / 100;

        $data = [
            1 => ['kg' => 0, 'ext_sum' => 0],
            2 => ['kg' => 0, 'ext_sum' => 0],
        ];

        foreach ($this->malts as $m) {
            $b = (int)$m['batch_number'];
            if ($this->batch_count == 1 && $b == 2) continue;

            $kg = (float)$m['kg'];
            $data[$b]['kg'] += $kg;

            // Słody nieaktywne enzymatycznie (is_active = 0) nie dokładają się do ekstrakcie
            if (isset($m['is_active']) && (bool)$m['is_active'] === false) {
                continue;
            }

            // Pobieramy ekstrakt z magazynu, jeśli słód jest przypisany
            $extract = 80; // domyślna wartość
            if (!empty($m['malt_stock_id'])) {
                $stockMalt = MaltStock::find($m['malt_stock_id']);
                $extract = $stockMalt ? (float)$stockMalt->extract : 80;
            } elseif (isset($m['extract'])) {
                $extract = (float)$m['extract'];
            }

            $data[$b]['ext_sum'] += ($kg * ($extract / 100));
        }

        $blg1 = ($vBatch > 0) ? ($data[1]['ext_sum'] / $vBatch * 100 * $eff) : 0;
        $blg2 = ($vBatch > 0) ? ($data[2]['ext_sum'] / $vBatch * 100 * $eff) : 0;

        $totalKg = $data[1]['kg'] + ($this->batch_count == 2 ? $data[2]['kg'] : 0);
        $totalVol = $this->batch_count * $vBatch;
        $totalExt = $data[1]['ext_sum'] + ($this->batch_count == 2 ? $data[2]['ext_sum'] : 0);
        $totalBlg = ($totalVol > 0) ? ($totalExt / $totalVol * 100 * $eff) : 0;

        return [
            'w1' => ['kg' => $data[1]['kg'], 'blg' => $blg1, 'bags' => ceil($data[1]['kg'] / 25)],
            'w2' => ['kg' => $data[2]['kg'], 'blg' => $blg2, 'bags' => ceil($data[2]['kg'] / 25)],
            'total_kg' => $totalKg,
            'total_blg' => $totalBlg,
            'akcpa_blg' => $this->akcpa_blg,
            'volume' => $totalVol
        ];
    }

    // Udział procentowy słodów aktywnych enzymatycznie (malt_stocks.is_enzymatic_active = 1)
    // w kg aktywnych słodów tej samej warki. Zwraca [indeks wiersza => procent].
    #[Computed]
    public function enzymaticShares()
    {
        $stocks = $this->availableMalts->keyBy('id');
        $shares = [];

        foreach ([1, 2] as $batch) {
            $activeKg = [];

            foreach ($this->malts as $idx => $m) {
                if ((int)$m['batch_number'] !== $batch || empty($m['malt_stock_id'])) {
                    continue;
                }

                $stock = $stocks->get($m['malt_stock_id']);
                if ($stock && $stock->is_enzymatic_active) {
                    $activeKg[$idx] = (float)$m['kg'];
                }
            }

            $sum = array_sum($activeKg);
            foreach ($activeKg as $idx => $kg) {
                $shares[$idx] = $sum > 0 ? round($kg / $sum * 100, 1) : 0;
            }
        }

        return $shares;
    }

    /**
     * Sprawdza, czy suma kg każdego słodu z magazynu we wszystkich wierszach
     * nie przekracza dostępnego stanu. Błędy dodaje do pól kg konkretnych wierszy.
     */
    private function maltStockIsEnough(): bool
    {
        $ids = collect($this->malts)->pluck('malt_stock_id')->filter()->unique()->values();
        $stockKg = MaltStock::whereIn('id', $ids)->pluck('kg', 'id');

        $requested = [];
        foreach ($this->malts as $m) {
            if (empty($m['malt_stock_id'])) continue;
            $requested[$m['malt_stock_id']] = ($requested[$m['malt_stock_id']] ?? 0) + (float)$m['kg'];
        }

        $ok = true;
        foreach ($this->malts as $idx => $m) {
            if (empty($m['malt_stock_id'])) continue;

            $id = $m['malt_stock_id'];
            $available = (float)($stockKg[$id] ?? 0);

            if ($requested[$id] > $available + 0.0001) {
                $this->addError("malts.$idx.kg", sprintf(
                    'Za mało na stanie: dostępne %s kg, w recepturze łącznie %s kg.',
                    number_format($available, 2, ',', ' '),
                    number_format($requested[$id], 2, ',', ' ')
                ));
                $ok = false;
            }
        }

        return $ok;
    }

    public function save()
    {
        // 1. Walidacja danych
        $this->validate([
            'recipe_name' => 'required|string|max:255',
            'style' => 'nullable|string|max:100',
            'efficiency' => 'required|numeric|between:0,100',
            'malts' => 'required|array|min:1',
            'akcpa_blg' => 'nullable|numeric|min:0',
            'gopos_item_ids' => 'nullable|string'
        ]);

        // Ilość słodu nie może przekraczać stanu magazynowego (dla receptur bez zadanych drożdży –
        // po zadaniu drożdży stan jest już pomniejszony o zużycie)
        $yeastPitched = $this->recipeId && !$this->isClone
            && Recipe::whereKey($this->recipeId)->whereNotNull('yeast_pitched_at')->exists();

        if (!$yeastPitched && !$this->maltStockIsEnough()) {
            return;
        }

        $goposArray = array_values(array_filter(
            array_map('trim', explode(',', $this->gopos_item_ids)),
            fn($item) => $item !== ''
        ));

        DB::transaction(function () use ($goposArray) {
            // 2. Przygotowanie danych receptury
            $recipeData = [
                'tank_number' => $this->tank_number,
                'name'        => $this->recipe_name,
                'style'       => trim((string) $this->style) ?: null,
                'volume'      => $this->batch_count * 500, // lub $this->stats['volume']
                'efficiency'  => $this->efficiency,
                'blg'         => round($this->stats['total_blg'], 1),
                'batch_count' => $this->batch_count,
                'yeast_pitch_temperature' => $this->yeast_pitch_temperature,
                'fermentation_temperature' => $this->fermentation_temperature,
                'akcpa_blg' => $this->akcpa_blg,
                'akcpa_value' => $this->akcpa_blg * $this->batch_count * 5,
                'gopos_item_ids' => $goposArray,
            ];

            if ($this->recipeId && !$this->isClone) {
                // ==========================================
                // AKTUALIZACJA ISTNIEJĄCEJ RECEPTURY
                // ==========================================

                $recipe = Recipe::findOrFail($this->recipeId);

                $recipe->update($recipeData);

                // Usuwamy stare składniki
                $recipe->malts()->delete();
                $recipe->hops()->delete();

            } else {
                // ==========================================
                // NOWA RECEPTURA LUB KLON
                // ==========================================

                $recipeData['user_id'] = Auth::id();

                // Numer receptury nadawany dopiero przy wykonaniu zasypu (wersja robocza nie ma numeru)

                $recipe = Recipe::create($recipeData);

                // ==========================================
                // TWORZENIE WAREK
                // ==========================================

                // Numery warek (globalne) nadawane są dopiero przy wykonaniu zasypu
                // (BrewingController::updateStatus), a nie przy zapisie receptury.
            }

            // 3. Zapis Słodów
            foreach ($this->malts as $maltData) {
                if (empty($maltData['name']) && empty($maltData['kg'])) continue;

                $stockMalt = null;
                if (!empty($maltData['malt_stock_id'])) {
                    $stockMalt = MaltStock::find($maltData['malt_stock_id']);
                }

                $recipe->malts()->create([
                    'malt_stock_id' => $maltData['malt_stock_id'] ?? null,
                    'name'          => $stockMalt ? $stockMalt->name : ($maltData['name'] ?: 'Słód'),
                    'kg'            => $maltData['kg'] ?: 0,
                    'batch_number'  => $maltData['batch_number'],
                ]);
            }

            // 4. Zapis Chmieli
            foreach ($this->hops as $hopData) {
                if (empty($hopData['name']) && empty($hopData['amount'])) continue;

                $recipe->hops()->create([
                    'name'         => $hopData['name'] ?: 'Chmiel',
                    'amount'       => $hopData['amount'] ?: 0,
                    'alpha_acids'  => $hopData['alpha_acids'] ?: 0,
                    'time'         => $hopData['time'] ?: 0,
                    'is_active'    => (bool)$hopData['is_active'],
                    'batch_number' => $hopData['batch_number'],
                ]);
            }
        });

        // 5. Powiadomienie i przekierowanie
        session()->flash('success', 'Receptura zapisana pomyślnie!');
        return redirect()->route('brewing.index');
    }

    public function render()
    {
        return view('livewire.recipe-form');
    }
}