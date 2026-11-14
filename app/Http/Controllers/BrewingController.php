<?php

namespace App\Http\Controllers;

use App\Models\MaltStock;
use App\Models\Recipe;
use App\Models\RecipeBatches;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BrewingController extends Controller
{
    public function index()
    {
        $recipes = Recipe::with('batches')->orderByDesc('recipes.id')->get();

        return view('brewing.index', compact('recipes'));
    }

    public function create()
    {
        return view('brewing.create');
    }

    public function show(Recipe $recipe)
    {
        $recipe->load(['malts', 'comments.user']);
        return view('brewing.show', compact('recipe'));
    }

    public function storeComment(Request $request, Recipe $recipe) {
        $request->validate([
            'body' => 'required|string|max:1000',
        ]);

        $recipe->comments()->create([
            'body' => $request->body,
            'user_id' => auth()->id(),
        ]);

        return back()->with('success', 'Komentarz został dodany!');
    }

    public function destroy(Recipe $recipe)
    {
        if ($recipe->yeast_pitched_at && $recipe->yeast_pitched_at->isPast()) {
            return back()->with('error', 'Nie można usunąć warki, która została już zrealizowana.');
        }

        // Sprawdzenie uprawnień
        if ($recipe->user_id !== auth()->id()) {
            abort(403);
        }

        $recipe->delete();

        return redirect()->route('brewing.index')->with('success', 'Receptura została usunięta.');
    }

    public function updateStatus(Request $request, Recipe $recipe)
    {
        $type = $request->input('type');

        // Wykonano zasyp – dopiero tutaj odejmujemy słody z magazynu (tylko raz)
        if ($type === 'mash') {
            if (!$recipe->mashed_at && !$recipe->yeast_pitched_at) {
                DB::transaction(function () use ($recipe) {
                    foreach ($recipe->malts as $malt) {
                        if (!empty($malt->malt_stock_id)) {
                            MaltStock::where('id', $malt->malt_stock_id)->decrement('kg', $malt->kg);
                        }
                    }

                    // Numery warek nadajemy przy zasypie, kolejno po ostatniej uwarzonej warce
                    if ($recipe->batches()->doesntExist()) {
                        $this->createBatchesWithNextNumbers($recipe);
                    }

                    // Numer receptury również przy zasypie – kolejny po ostatniej uwarzonej
                    if ($recipe->number === null) {
                        $recipe->number = (Recipe::where('user_id', $recipe->user_id)->max('number') ?? 0) + 1;
                    }

                    $recipe->mashed_at = now();
                });
            }
        }

        // Drożdże można zadać dopiero po zasypie
        if ($type === 'yeast' && !$recipe->mashed_at && !$recipe->yeast_pitched_at) {
            return back()->with('error', 'Najpierw wykonaj zasyp.');
        }

        if ($type === 'yeast') {
            $recipe->yeast_pitched_at = now();
            $recipe->created_at = now();
        }

        if ($type === 'primary') $recipe->primary_fermentation_start = now();
        if ($type === 'secondary') $recipe->secondary_fermentation_start = now();
        if ($type === 'finish') $recipe->finished_at = now();

        $recipe->save();

        return back()->with('success', 'Status warki został zaktualizowany!');
    }

    public function toggleArchive(Recipe $recipe)
    {
        $recipe->is_archived = !$recipe->is_archived;
        $recipe->save();

        return back()->with('success', $recipe->is_archived
            ? 'Receptura została zarchiwizowana.'
            : 'Receptura została przywrócona z archiwum.');
    }

    public function storeGravity(Request $request, Recipe $recipe) {
        $request->validate(['value' => 'required|numeric|min:0|max:100']);
        $recipe->gravityReadings()->create(['value' => $request->value]);
        return back()->with('success', 'Pomiar BLG dodany!');
    }

    public function update(Request $request, Recipe $recipe)
    {
        // Receptura z wykonanym zasypem nie może być edytowana
        if ($recipe->mashed_at) {
            return back()->with('error', 'Receptura ma wykonany zasyp – nie można jej edytować.');
        }

        $recipe->update([
            'name' => $request->recipe_name,
            'tank_number' => $request->tank_number,
            'volume' => $request->volume,
            'efficiency' => $request->efficiency,
        ]);

        $recipe->malts()->delete();
        $recipe->hops()->delete();

        if ($request->has('malts')) {
            foreach ($request->malts as $maltData) {
                $recipe->malts()->create([
                    'batch_number' => $maltData['batch_number'],
                    'name' => $maltData['name'] ?? 'Słód',
                    'kg' => $maltData['kg'] ?? 0,
                    'extract' => $maltData['extract'] ?? 80,
                    'unit' => $maltData['unit'] ?? 'kg',
                    'is_active' => isset($maltData['active']),
                ]);
            }
        }

        if ($request->has('hops')) {
            foreach ($request->hops as $hopData) {
                // Chmiele z warki spoza zakresu batch_count nie są zapisywane
                if ((int)$hopData['batch_number'] > max(1, (int)$recipe->batch_count)) continue;

                $recipe->hops()->create([
                    'batch_number' => $hopData['batch_number'],
                    'name' => $hopData['name'] ?? 'Chmiel',
                    'amount' => $hopData['amount'] ?? 0,
                    'alpha_acids' => $hopData['alpha_acids'] ?? 0,
                    'time' => $hopData['time'] ?? 0,
                    'is_active' => isset($hopData['active']),
                ]);
            }
        }

        return redirect()->route('brewing.show', $recipe)->with('success', 'Receptura została zaktualizowana!');
    }

    public function cloneRecipe(Recipe $recipe)
    {
        // Kopiowanie głównego rekordu
        $newRecipe = $recipe->replicate();

        $newRecipe->name = $recipe->name . ' (Kopia)';
        $newRecipe->created_at = now();
        $newRecipe->updated_at = now();

        // Klon to nowa receptura: bez numeru, zasypu, drożdży i fermentacji
        $newRecipe->number = null;
        $newRecipe->mashed_at = null;
        $newRecipe->yeast_pitched_at = null;
        $newRecipe->finished_at = null;
        $newRecipe->primary_fermentation_start = null;
        $newRecipe->secondary_fermentation_start = null;
        $newRecipe->is_archived = false;

        $newRecipe->save();

        // Kopiowanie słodów
        foreach ($recipe->malts as $malt) {
            $newRecipe->malts()->create([
                'batch_number' => $malt->batch_number,
                'name' => $malt->name,
                'kg' => $malt->kg,
                'extract' => $malt->extract,
                'unit' => $malt->unit,
                'is_active' => $malt->is_active,
            ]);
        }

        // Kopiowanie chmieli
        foreach ($recipe->hops as $hop) {
            $newRecipe->hops()->create([
                'batch_number' => $hop->batch_number,
                'name' => $hop->name,
                'amount' => $hop->amount,
                'alpha_acids' => $hop->alpha_acids,
                'time' => $hop->time,
                'is_active' => $hop->is_active,
            ]);
        }

        return redirect()
            ->route('brewing.edit', $newRecipe)
            ->with('success', 'Receptura została sklonowana.');
    }

    public function edit(Recipe $recipe)
    {
        $recipe->load(['malts', 'hops', 'comments.user']);
        return view('brewing.create', [
            'recipe' => $recipe,
            'comments' => $recipe->comments,
        ]);
    }
    public function clone(Recipe $recipe)
    {
        $recipe->load(['malts', 'hops', 'comments.user']);
        return view('brewing.create', [
            'recipe' => $recipe,
            'isClone' => true,
            'comments' => $recipe->comments,
        ]);
    }

    public function print(Recipe $recipe)
    {
        return view('brewing.print', compact('recipe'));
    }

    public function generateEmailContentExciseForRecipe(Recipe $recipe)
    {
        $recipe->load(['malts', 'hops', 'batches']);

        $totalMalt = $recipe->malts->sum('kg');

        return view('brewing.excise-email', compact(
            'recipe',
            'totalMalt'
        ));
    }

    /**
     * Tworzy warki receptury z numerami kolejnymi po ostatnim numerze w bazie.
     * Unikalny indeks na batch_number wykrywa kolizję przy równoległym zasypie,
     * wtedy numer jest liczony ponownie z nowego maksimum.
     */
    private function createBatchesWithNextNumbers(Recipe $recipe): void
    {
        $count = $recipe->batch_count ?: 1;

        for ($attempt = 1; ; $attempt++) {
            try {
                // Zagnieżdżona transakcja = savepoint, więc po błędzie zewnętrzna transakcja zostaje ważna
                DB::transaction(function () use ($recipe, $count) {
                    $next = (RecipeBatches::max('batch_number') ?? 0) + 1;

                    for ($i = 0; $i < $count; $i++) {
                        $recipe->batches()->create(['batch_number' => $next + $i]);
                    }
                });

                return;
            } catch (\Illuminate\Database\QueryException $e) {
                // 23000 = naruszenie ograniczenia unikalności
                if ($e->getCode() !== '23000' || $attempt >= 5) {
                    throw $e;
                }
            }
        }
    }
}
