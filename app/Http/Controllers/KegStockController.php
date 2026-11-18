<?php

namespace App\Http\Controllers;

use App\Queries\Contracts\RecipeQueriesInterface;
use App\Queries\Contracts\KegMovementQueriesInterface;
use App\Queries\Contracts\KegStockQueriesInterface;
use App\Models\KegMovement;
use App\Services\KegStockService;
use App\Http\Requests\IssueKegRequest;
use App\Http\Requests\PoolKegRequest;
use App\Http\Requests\RemoveKegRequest;
use App\Http\Requests\UpdateKegMovementRequest;
use Throwable;

class KegStockController extends Controller
{
    public function __construct(
        private readonly KegStockService $kegStockService,
        private readonly KegStockQueriesInterface $kegStockQueries,
        private readonly KegMovementQueriesInterface $kegMovementQueries,
        private readonly RecipeQueriesInterface $recipeQueries,
    ) {
    }

    public function index()
    {
        $stocks = $this->kegStockQueries->findTopByFullKegs(10);

        $summary = $this->kegStockService->summary();

        $recipes = $this->recipeQueries->findActiveFinishedNewestFirst();

        return view('kegs.admin.index', [
            'stocks' => $stocks,
            'recipes' => $recipes,
            'summary' => $summary,
            'stocksMovements' => $this->kegStockService->stocksMovements(),
            'coolerStocks' => $this->kegStockQueries->findNotArchivedOrderedByFullKegs(),
            'recentConnections' => $this->kegStockService->currentTapConnections(),
        ]);
    }

    public function issue(IssueKegRequest $request)
    {
        $data = $request->validated();

        try {
            $recipe = $this->recipeQueries->findByIdOrFail($data['recipe_id']);

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
        $movements = $this->kegMovementQueries->paginateWithRecipe(30);

        return view('kegs.admin.movements', [
            'movements' => $movements,
        ]);
    }

    public function pool(PoolKegRequest $request)
    {
        $data = $request->validated();

        try {
            $recipe = $this->recipeQueries->findByIdOrFail($data['recipe_id']);

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

    public function remove(RemoveKegRequest $request)
    {
        $data = $request->validated();

        try {
            $recipe = $this->recipeQueries->findByIdOrFail($data['recipe_id']);

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

    public function updateMovement(UpdateKegMovementRequest $request, KegMovement $movement)
    {
        $data = $request->validated();

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