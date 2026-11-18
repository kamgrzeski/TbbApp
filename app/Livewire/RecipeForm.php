<?php

namespace App\Livewire;

use App\Queries\RecipeQueries;
use App\Queries\MaltStockQueries;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\Computed;
use App\Models\Recipe;

class RecipeForm extends Component
{
    public $recipeId = null;
    public $isClone = false;
    public bool $locked = false;

    // Pola modelu
    public $recipe_name = '';
    public $style = '';
    public $tank_number = 2;
    public $batch_count = 2;
    public $efficiency = 75;

    public $yeast_pitch_temperature = 0;
    public $fermentation_temperature = 0;
    public $malts = [];
    public $hops = [];
    public float $akcpa_blg = 0.0;
    public string $gopos_item_ids;

    #[Computed]
    public function availableMalts()
    {
        return MaltStockQueries::orderedByName(['id', 'name', 'extract', 'kg', 'is_enzymatic_active']);
    }

    public function mount($recipe = null, $isClone = false)
    {
        $this->isClone = $isClone;

        if ($recipe) {
            $this->recipeId = $isClone ? null : $recipe->id;
            $this->locked = !$isClone && !is_null($recipe->mashed_at);
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

            $this->malts = $recipe->malts->map(fn($m) => [
                'batch_number' => $m->batch_number,
                'malt_stock_id' => $m->malt_stock_id,
                'name' => $m->name,
                'kg' => $m->kg,
                'is_active' => $m->maltStock ? (bool)$m->maltStock->is_enzymatic_active : false,
            ])->toArray();

            $this->hops = $recipe->hops->map(fn($h) => [
                'batch_number' => $h->batch_number,
                'name' => $h->name,
                'amount' => $h->amount,
                'alpha_acids' => $h->alpha_acids,
                'time' => $h->time,
                'is_active' => (bool)$h->is_active,
            ])->toArray();
        } else {
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
        $malt = MaltStockQueries::find($maltId);
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

            if (isset($m['is_active']) && (bool)$m['is_active'] === false) {
                continue;
            }

            $extract = 80;
            if (!empty($m['malt_stock_id'])) {
                $stockMalt = MaltStockQueries::find($m['malt_stock_id']);
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


    private function maltStockIsEnough(): bool
    {
        $ids = collect($this->malts)->pluck('malt_stock_id')->filter()->unique()->values();
        $stockKg = MaltStockQueries::kgByIds($ids);

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
        if ($this->recipeId && !$this->isClone
            && RecipeQueries::isMashed($this->recipeId)) {
            $this->locked = true;
            session()->flash('error', 'Receptura ma wykonany zasyp – nie można jej edytować.');
            return;
        }

        $this->validate([
            'recipe_name' => 'required|string|max:255',
            'style' => 'nullable|string|max:100',
            'efficiency' => 'required|numeric|between:0,100',
            'malts' => 'required|array|min:1',
            'akcpa_blg' => 'nullable|numeric|min:0',
            'gopos_item_ids' => 'nullable|string'
        ]);

        $yeastPitched = $this->recipeId && !$this->isClone
            && RecipeQueries::isYeastPitched($this->recipeId);

        if (!$yeastPitched && !$this->maltStockIsEnough()) {
            return;
        }

        $goposArray = array_values(array_filter(
            array_map('trim', explode(',', $this->gopos_item_ids)),
            fn($item) => $item !== ''
        ));

        DB::transaction(function () use ($goposArray) {
            $recipeData = [
                'tank_number' => $this->tank_number,
                'name'        => $this->recipe_name,
                'style'       => trim((string) $this->style) ?: null,
                'volume'      => $this->batch_count * 500,
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
                // ==========================================

                $recipe = RecipeQueries::findOrFail($this->recipeId);

                $recipe->update($recipeData);

                $recipe->malts()->delete();
                $recipe->hops()->delete();

            } else {
                // ==========================================
                // ==========================================

                $recipeData['user_id'] = Auth::id();


                $recipe = Recipe::create($recipeData);

                // ==========================================
                // ==========================================

            }

            foreach ($this->malts as $maltData) {
                if (empty($maltData['name']) && empty($maltData['kg'])) continue;

                $stockMalt = null;
                if (!empty($maltData['malt_stock_id'])) {
                    $stockMalt = MaltStockQueries::find($maltData['malt_stock_id']);
                }

                $recipe->malts()->create([
                    'malt_stock_id' => $maltData['malt_stock_id'] ?? null,
                    'name'          => $stockMalt ? $stockMalt->name : ($maltData['name'] ?: 'Słód'),
                    'kg'            => $maltData['kg'] ?: 0,
                    'batch_number'  => $maltData['batch_number'],
                ]);
            }

            foreach ($this->hops as $hopData) {
                if (empty($hopData['name']) && empty($hopData['amount'])) continue;
                if ((int)$hopData['batch_number'] > max(1, (int)$this->batch_count)) continue;

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