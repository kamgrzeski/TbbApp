<?php

namespace App\Http\Controllers;

use App\Models\KegMovement;
use App\Models\KegStock;
use App\Models\Recipe;
use App\Services\KegStockService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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
            ->take(10)->get();

        $summary = $this->kegStockService->summary();

        $recipes = Recipe::query()
            ->notArchived()
            ->whereNotNull('finished_at')
            ->orderByDesc('finished_at')
            ->get();

        return view('kegs.admin.index', [
            'stocks' => $stocks,
            'recipes' => $recipes,
            'summary' => $summary,
            'stocksMovements' => $this->kegStockService->stocksMovements(),
            'archivedStocksMovements' => $this->kegStockService->archivedStocksMovements(),
            'recentConnections' => $this->kegStockService->currentTapConnections(),
        ]);
    }

    public function issue(Request $request)
    {
        $data = $request->validate([
            'recipe_id' => [
                'required',
                Rule::exists('recipes', 'id'),
            ],
            'quantity' => ['required', 'integer', 'min:1', 'max:20'],
            'tap_number' => ['required', 'integer', 'between:1,' . KegStockService::TAPS_COUNT],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $recipe = Recipe::findOrFail($data['recipe_id']);

            $this->kegStockService->issue(
                $recipe,
                $data['quantity'],
                $data['tap_number'],
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

    public function pool(Request $request)
    {
        $data = $request->validate([
            'recipe_id' => [
                'required',
                Rule::exists('recipes', 'id')
                    ->whereNotNull('finished_at')
                    ->where('is_archived', false),
            ],
            'quantity' => ['required', 'integer', 'min:1', 'max:20'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $recipe = Recipe::findOrFail($data['recipe_id']);

            $this->kegStockService->pool(
                $recipe,
                $data['quantity'],
                $data['note'] ?? null
            );

            return redirect()
                ->route('kegs.admin.index')
                ->with('success', 'Keg został dodany do puli.');
        } catch (Throwable $e) {
            return back()
                ->withInput()
                ->withErrors([
                    'quantity' => $e->getMessage(),
                ]);
        }
    }

    public function remove(Request $request)
    {
        $data = $request->validate([
            'recipe_id' => ['required', 'exists:recipes,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:20'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $recipe = Recipe::findOrFail($data['recipe_id']);

            $this->kegStockService->remove(
                $recipe,
                $data['quantity'],
                $data['note'] ?? null
            );

            return redirect()
                ->route('kegs.admin.index')
                ->with('success', 'Keg został usunięty z chłodni.');
        } catch (Throwable $e) {
            return back()
                ->withInput()
                ->withErrors([
                    'quantity' => $e->getMessage(),
                ]);
        }
    }

    public function updateMovement(Request $request, KegMovement $movement)
    {
        $data = $request->validate([
            'capacity' => ['required', 'integer', 'min:0'],
        ]);

        try {
            $movement->update([
                'capacity' => $data['capacity'],
            ]);

            return redirect()
                ->route('kegs.admin.index')
                ->with('success', 'Ilość mililitrów została zaktualizowana.');
        } catch (Throwable $e) {
            return back()
                ->withErrors(['capacity' => 'Wystąpił błąd podczas aktualizacji.']);
        }
    }
}