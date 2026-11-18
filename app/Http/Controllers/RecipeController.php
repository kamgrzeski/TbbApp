<?php

namespace App\Http\Controllers;

use App\Queries\Contracts\RecipeBatchesQueriesInterface;
use App\Queries\Contracts\RecipeQueriesInterface;
use App\Models\MaltStock;
use App\Models\Recipe;
use App\Http\Requests\StoreGravityReadingRequest;
use App\Http\Requests\StoreRecipeCommentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RecipeController extends Controller
{
    public function __construct(
        private readonly RecipeQueriesInterface $recipeQueries,
        private readonly RecipeBatchesQueriesInterface $recipeBatchesQueries,
    ) {}

    public function index()
    {
        $recipes = $this->recipeQueries->findAllWithBatchesNewestFirst();

        return view('recipe.index', compact('recipes'));
    }

    public function create()
    {
        return view('recipe.create');
    }

    public function show(Recipe $recipe)
    {
        $recipe->load(['malts', 'comments.user']);
        return view('recipe.show', compact('recipe'));
    }

    public function storeComment(StoreRecipeCommentRequest $request, Recipe $recipe) {

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

        if ($recipe->user_id !== auth()->id()) {
            abort(403);
        }

        $recipe->delete();

        return redirect()->route('recipe.index')->with('success', 'Receptura została usunięta.');
    }

    public function updateStatus(Request $request, Recipe $recipe)
    {
        $type = $request->input('type');

        if ($type === 'mash') {
            if (!$recipe->mashed_at && !$recipe->yeast_pitched_at) {
                DB::transaction(function () use ($recipe) {
                    foreach ($recipe->malts as $malt) {
                        if (!empty($malt->malt_stock_id)) {
                            MaltStock::where('id', $malt->malt_stock_id)->decrement('kg', $malt->kg);
                        }
                    }

                    if ($recipe->batches()->doesntExist()) {
                        $this->createBatchesWithNextNumbers($recipe);
                    }

                    if ($recipe->number === null) {
                        $recipe->number = $this->recipeQueries->findMaxNumberForUser($recipe->user_id) + 1;
                    }

                    $recipe->mashed_at = now();
                });
            }
        }

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

    public function storeGravity(StoreGravityReadingRequest $request, Recipe $recipe) {
        $recipe->gravityReadings()->create(['value' => $request->value]);
        return back()->with('success', 'Pomiar BLG dodany!');
    }

    public function update(Request $request, Recipe $recipe)
    {
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

        return redirect()->route('recipe.show', $recipe)->with('success', 'Receptura została zaktualizowana!');
    }

    public function cloneRecipe(Recipe $recipe)
    {
        $newRecipe = $recipe->replicate();

        $newRecipe->name = $recipe->name . ' (Kopia)';
        $newRecipe->created_at = now();
        $newRecipe->updated_at = now();

        $newRecipe->number = null;
        $newRecipe->mashed_at = null;
        $newRecipe->yeast_pitched_at = null;
        $newRecipe->finished_at = null;
        $newRecipe->primary_fermentation_start = null;
        $newRecipe->secondary_fermentation_start = null;
        $newRecipe->is_archived = false;

        $newRecipe->save();

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
            ->route('recipe.edit', $newRecipe)
            ->with('success', 'Receptura została sklonowana.');
    }

    public function edit(Recipe $recipe)
    {
        $recipe->load(['malts', 'hops', 'comments.user']);
        return view('recipe.create', [
            'recipe' => $recipe,
            'comments' => $recipe->comments,
        ]);
    }
    public function clone(Recipe $recipe)
    {
        $recipe->load(['malts', 'hops', 'comments.user']);
        return view('recipe.create', [
            'recipe' => $recipe,
            'isClone' => true,
            'comments' => $recipe->comments,
        ]);
    }

    public function print(Recipe $recipe)
    {
        return view('recipe.print', compact('recipe'));
    }

    public function generateEmailContentExciseForRecipe(Recipe $recipe)
    {
        $recipe->load(['malts', 'hops', 'batches']);

        $totalMalt = $recipe->malts->sum('kg');

        return view('recipe.excise-email', compact(
            'recipe',
            'totalMalt'
        ));
    }


    private function createBatchesWithNextNumbers(Recipe $recipe): void
    {
        $count = $recipe->batch_count ?: 1;

        for ($attempt = 1; ; $attempt++) {
            try {
                DB::transaction(function () use ($recipe, $count) {
                    $next = $this->recipeBatchesQueries->findMaxBatchNumber() + 1;

                    for ($i = 0; $i < $count; $i++) {
                        $recipe->batches()->create(['batch_number' => $next + $i]);
                    }
                });

                return;
            } catch (\Illuminate\Database\QueryException $e) {
                if ($e->getCode() !== '23000' || $attempt >= 5) {
                    throw $e;
                }
            }
        }
    }
}
