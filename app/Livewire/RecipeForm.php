<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\Computed;
use App\Models\Recipe;

class RecipeForm extends Component
{
    public $recipeId = null;
    public $isClone = false;

    // Pola modelu
    public $recipe_name = '';
    public $tank_number = 2;
    public $batch_count = 2;
    public $efficiency = 75;

    // Składniki
    public $malts = [];
    public $hops = [];

    public function mount($recipe = null, $isClone = false)
    {
        $this->isClone = $isClone;

        if ($recipe) {
            $this->recipeId = $isClone ? null : $recipe->id;
            $this->recipe_name = $isClone ? $recipe->name . ' (Kopia)' : $recipe->name;
            $this->tank_number = $recipe->tank_number;
            $this->batch_count = $recipe->volume <= 500 ? 1 : 2;
            $this->efficiency = $recipe->efficiency ?? 75;

            // Mapowanie słodów z bazy
            $this->malts = $recipe->malts->map(fn($m) => [
                'batch_number' => $m->batch_number,
                'name' => $m->name,
                'kg' => $m->kg,
                'extract' => $m->extract,
                'is_active' => (bool)$m->is_active,
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
            $this->addMalt(1, 'Słód Pilzneński', 100);
            $this->addMalt(2, 'Słód Pilzneński', 100);
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
            'extract' => 80,
            'is_active' => true
        ];
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

            if ($m['is_active']) {
                $data[$b]['ext_sum'] += ($kg * ((float)$m['extract'] / 100));
            }
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
            'volume' => $totalVol
        ];
    }

    public function save()
    {
        // 1. Walidacja danych
        $this->validate([
            'recipe_name' => 'required|string|max:255',
            'efficiency' => 'required|numeric|between:0,100',
            'malts' => 'required|array|min:1',
        ]);

        DB::transaction(function () {
            // 2. Przygotowanie danych receptury
            $recipeData = [
                'tank_number' => $this->tank_number,
                'name'        => $this->recipe_name,
                'volume'      => $this->batch_count * 500, // lub $this->stats['volume']
                'efficiency'  => $this->efficiency,
                'blg'         => $this->stats['total_blg'],
                'batch_count' => $this->batch_count
            ];

            if ($this->recipeId && !$this->isClone) {
                // AKTUALIZACJA ISTNIEJĄCEJ
                $recipe = Recipe::findOrFail($this->recipeId);
                $recipe->update($recipeData);

                // Usuwamy stare składniki, żeby wstawić nowe (najprostszy sposób na "synchronizację")
                $recipe->malts()->delete();
                $recipe->hops()->delete();
            } else {
                // NOWA RECEPTURA LUB KLON
                $recipeData['user_id'] = Auth::id();
                $recipeData['number']  = Auth::user()->recipes()->count() + 1;

                $recipe = Recipe::create($recipeData);
            }

            // 3. Zapis Słodów
            foreach ($this->malts as $maltData) {
                // Pomijamy puste wiersze, jeśli użytkownik takie zostawił
                if (empty($maltData['name']) && empty($maltData['kg'])) continue;

                $recipe->malts()->create([
                    'name'         => $maltData['name'] ?: 'Słód',
                    'kg'           => $maltData['kg'] ?: 0,
                    'extract'      => $maltData['extract'] ?: 80,
                    'is_active'    => (bool)$maltData['is_active'],
                    'batch_number' => $maltData['batch_number'],
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