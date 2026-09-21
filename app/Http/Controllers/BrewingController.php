<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BrewingController extends Controller
{
    public function index()
    {
        $recipes = Auth::user()->recipes()->latest()->get();
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

        if ($type === 'yeast') $recipe->yeast_pitched_at = now(); // Nowa linia
        if ($type === 'primary') $recipe->primary_fermentation_start = now();
        if ($type === 'secondary') $recipe->secondary_fermentation_start = now();
        if ($type === 'finish') $recipe->finished_at = now();

        $recipe->save();
        return back()->with('success', 'Status warki został zaktualizowany!');
    }

    public function storeGravity(Request $request, Recipe $recipe) {
        $request->validate(['value' => 'required|numeric|min:0|max:100']);
        $recipe->gravityReadings()->create(['value' => $request->value]);
        return back()->with('success', 'Pomiar BLG dodany!');
    }

    public function update(Request $request, Recipe $recipe)
    {
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
        $recipe->load(['malts', 'hops']);
        return view('brewing.create', [
            'recipe' => $recipe
        ]);
    }
    public function clone(Recipe $recipe)
    {
        $recipe->load(['malts', 'hops']);
        return view('brewing.create', [
            'recipe' => $recipe,
            'isClone' => true,
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
}
