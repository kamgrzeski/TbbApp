<?php

namespace App\Http\Controllers;

use App\Models\KegMovement;
use App\Models\KegStock;
use App\Models\Recipe;
use App\Services\KegStockService;
use Illuminate\Http\Request;
use Throwable;

class KegStockController extends Controller
{
    public function __construct(private readonly KegStockService $kegStockService)
    {
    }

    public function index()
    {
        $stocks = KegStock::query()
            ->with(['recipe', 'movements'])
            ->orderBy('full_kegs', 'desc')
            ->get();

        $summary = $this->kegStockService->summary();

        return view('kegs.admin.index', [
            'stocks' => $stocks,
            'summary' => $summary,
        ]);
    }

    public function createProduction()
    {
        $recipes = Recipe::query()
            ->orderBy('name')
            ->whereNull('finished_at')
            ->get();

        return view('kegs.admin.production', [
            'recipes' => $recipes,
        ]);
    }

    /**
     * Zapis rozlewu.
     */
    public function production(Request $request)
    {
        $data = $request->validate([
            'recipe_id' => ['required', 'exists:recipes,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:20'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $recipe = Recipe::findOrFail($data['recipe_id']);

            $this->kegStockService->produce(
                $recipe,
                $data['quantity'],
                $data['note'] ?? null
            );

            return redirect()
                ->route('kegs.admin.index')
                ->with('success', 'Rozlew został zapisany.');
        } catch (Throwable $e) {
            return back()
                ->withInput()
                ->withErrors([
                    'quantity' => $e->getMessage(),
                ]);
        }
    }

    public function issue(Request $request)
    {
        $data = $request->validate([
            'recipe_id' => ['required', 'exists:recipes,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:20'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $recipe = Recipe::findOrFail($data['recipe_id']);

            $this->kegStockService->issue(
                $recipe,
                $data['quantity'],
                $data['note'] ?? null
            );

            return redirect()
                ->route('kegs.admin.index')
                ->with('success', 'Wydanie zostało zapisane.');
        } catch (Throwable $e) {
            return back()
                ->withInput()
                ->withErrors([
                    'quantity' => $e->getMessage(),
                ]);
        }
    }

    public function movements()
    {
        $movements = KegMovement::query()
            ->with('recipe')
            ->latest()
            ->paginate(30);

        return view('kegs.admin.movements', [
            'movements' => $movements,
        ]);
    }
}