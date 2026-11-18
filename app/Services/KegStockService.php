<?php

namespace App\Services;

use App\Models\KegMovement;
use App\Models\KegStock;
use App\Models\Recipe;
use App\Queries\GoPosItemsQueries;
use App\Queries\KegInventoryQueries;
use App\Queries\KegMovementQueries;
use App\Queries\KegStockQueries;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class KegStockService
{
    public const TOTAL_KEGS = 20;
    public const KEG_CAPACITY_LITERS = 50;
    public const KEG_MAX_ML = 47000;

    public const TAPS_COUNT = 4;

    public function produce(Recipe $recipe, int $quantity, ?string $note = null): void
    {
        DB::transaction(function () use ($recipe, $quantity, $note) {

            $inventory = KegInventoryQueries::singleton(lock: true);

            if ($inventory->empty_kegs < $quantity) {
                throw new \RuntimeException(
                    "Brak wystarczającej liczby pustych kegów. Dostępne: {$inventory->empty_kegs}."
                );
            }

            $stock = KegStockQueries::forRecipe($recipe->id)
                ->lockForUpdate()
                ->first();

            if (!$stock) {
                $stock = KegStock::create([
                    'recipe_id' => $recipe->id,
                    'full_kegs' => 0,
                ]);
            }

            $inventory->decrement('empty_kegs', $quantity);

            $stock->increment('full_kegs', $quantity);

            if ($stock->is_archived) {
                $stock->update(['is_archived' => false]);
            }

            KegMovement::create([
                'recipe_id' => $recipe->id,
                'keg_stock_id' => $stock->id,
                'type' => 'production',
                'quantity' => $quantity,
                'note' => $note,
            ]);
        });
    }

    public function issue(Recipe $recipe, int $quantity, int $tapNumber, ?string $note = null): KegStock
    {
        if ($quantity <= 0) {
            throw new RuntimeException('Ilość kegów musi być większa od 0.');
        }

        if ($tapNumber < 1 || $tapNumber > self::TAPS_COUNT) {
            throw new RuntimeException('Nieprawidłowy numer kranu.');
        }

        return DB::transaction(function () use ($recipe, $quantity, $tapNumber, $note) {

            $stock = KegStockQueries::forRecipe($recipe->id)
                ->lockForUpdate()
                ->first();

            $goPosItems = GoPosItemsQueries::all();

            if (!$stock || $stock->full_kegs < $quantity) {
                $available = $stock?->full_kegs ?? 0;

                throw new RuntimeException(
                    "Brak wystarczającej ilości pełnych kegów. " .
                    "Dostępne: {$available}."
                );
            }

            $onOtherTap = KegMovementQueries::currentIssuesForRecipeOnOtherTap($recipe->id, $tapNumber)
                ->lockForUpdate()
                ->first();

            if ($onOtherTap) {
                throw new RuntimeException("Piwo {$recipe->name} jest już podłączone do kranu {$onOtherTap->tap_number}.");
            }

            $tapCurrent = KegMovementQueries::currentIssuesOnTap($tapNumber)
                ->lockForUpdate()
                ->get();

            if ($tapCurrent->isNotEmpty()) {
                $otherRecipeIds = $tapCurrent
                    ->pluck('recipe_id')
                    ->reject(fn ($id) => $id === $recipe->id);

                if ($otherRecipeIds->isNotEmpty()) {
                    $stillInCooler = KegStockQueries::anyFullKegsForRecipes($otherRecipeIds->all());

                    if ($stillInCooler) {
                        throw new RuntimeException("Kran {$tapNumber} ma podłączone inne piwo z pełnymi kegami w chłodni.");
                    }
                }

                KegMovement::query()
                    ->whereIn('id', $tapCurrent->pluck('id'))
                    ->update(['is_current' => false]);
            }

            $stock->decrement('full_kegs', $quantity);

            KegMovement::create([
                'recipe_id' => $recipe->id,
                'keg_stock_id' => $stock->id,
                'type' => 'issue',
                'quantity' => -$quantity,
                'note' => $note,
                'is_current' => true,
                'tap_number' => $tapNumber,
                'capacity' => self::KEG_MAX_ML,
            ]);

            $inventory = KegInventoryQueries::lockedFirstOrFail();

            $stock->refresh();


            if($stock->full_kegs == 0) {
                $stock->update(['is_archived' => true]);
            }

            $inventory->increment('empty_kegs', $quantity);

            return $stock->fresh();
        });
    }

    public function summary(): array
    {
        $inventory = KegInventoryQueries::singleton();

        $full = KegStockQueries::totalFullKegs();
        $empty = $inventory->empty_kegs;

        return [
            'total' => $inventory->total_kegs,
            'full' => $full,
            'empty' => $empty,
            'available_empty' => max(0, $empty - self::TAPS_COUNT),
            'full_liters' => $full * self::KEG_CAPACITY_LITERS,
        ];
    }

    public function stocksMovements()
    {
        return KegMovementQueries::currentIssuesWithRecipeAndStock();
    }

    public function currentTapConnections()
    {
        return KegMovementQueries::currentTapConnections();
    }

    public function pool(Recipe $recipe, int $quantity, ?string $note = null): KegStock
    {
        if ($quantity <= 0) {
            throw new RuntimeException('Ilość kegów musi być większa od 0.');
        }

        return DB::transaction(function () use ($recipe, $quantity, $note) {

            $stock = KegStockQueries::forRecipe($recipe->id)
                ->lockForUpdate()
                ->first();

            if (!$stock) {
                $stock = KegStock::create([
                    'recipe_id' => $recipe->id,
                    'full_kegs' => 0,
                ]);
            }

            $inventory = KegInventoryQueries::singleton(lock: true);

            $available = max(0, $inventory->empty_kegs - self::TAPS_COUNT);

            if ($available < $quantity) {
                throw new RuntimeException(
                    "Brak wystarczającej ilości kegów do napełnienia (4 krany są zajęte). " .
                    "Dostępne: {$available}."
                );
            }

            $stock->increment('full_kegs', $quantity);

            if ($stock->is_archived) {
                $stock->update(['is_archived' => false]);
            }

            KegMovement::create([
                'recipe_id' => $recipe->id,
                'keg_stock_id' => $stock->id,
                'type' => 'production',
                'quantity' => $quantity,
                'note' => $note
            ]);

            $inventory->decrement('empty_kegs', $quantity);

            $stock->refresh();

            return $stock->fresh();
        });
    }

    public function remove(Recipe $recipe, int $quantity, ?string $note = null): void
    {
        if ($quantity <= 0) {
            throw new RuntimeException('Ilość kegów musi być większa od 0.');
        }

        DB::transaction(function () use ($recipe, $quantity, $note) {
            $stock = KegStockQueries::forRecipe($recipe->id)
                ->lockForUpdate()
                ->first();

            if (!$stock || $stock->full_kegs < $quantity) {
                $available = $stock?->full_kegs ?? 0;
                throw new RuntimeException(
                    "Brak wystarczającej ilości pełnych kegów do usunięcia. " .
                    "Dostępne: {$available}."
                );
            }

            $stock->decrement('full_kegs', $quantity);

            KegMovement::create([
                'recipe_id' => $recipe->id,
                'keg_stock_id' => $stock->id,
                'type' => 'return',
                'quantity' => -$quantity,
                'note' => $note,
            ]);

            $inventory = KegInventoryQueries::singleton(lock: true);

            $inventory->increment('empty_kegs', $quantity);

            if ($stock->full_kegs == 0) {
                $stock->update(['is_archived' => true]);
            }
        });
    }
}
