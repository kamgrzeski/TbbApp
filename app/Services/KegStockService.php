<?php

namespace App\Services;

use App\Models\KegMovement;
use App\Models\KegStock;
use App\Models\Recipe;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use App\Models\KegInventories;

class KegStockService
{
    public const TOTAL_KEGS = 20;
    public const KEG_CAPACITY_LITERS = 50;

    /**
     * Rozlanie piwa do kegów.
     */
    public function produce(Recipe $recipe, int $quantity, ?string $note = null): void
    {
        DB::transaction(function () use ($recipe, $quantity, $note) {

            $inventory = KegInventories::query()
                ->lockForUpdate()
                ->firstOrCreate(
                    [],
                    [
                        'total_kegs' => self::TOTAL_KEGS,
                        'empty_kegs' => self::TOTAL_KEGS,
                    ]
                );

            if ($inventory->empty_kegs < $quantity) {
                throw new \RuntimeException(
                    "Brak wystarczającej liczby pustych kegów. Dostępne: {$inventory->empty_kegs}."
                );
            }

            $stock = KegStock::query()
                ->where('recipe_id', $recipe->id)
                ->lockForUpdate()
                ->first();

            if (!$stock) {
                $stock = KegStock::create([
                    'recipe_id' => $recipe->id,
                    'full_kegs' => 0,
                ]);
            }

            // Puste kegi -> pełne kegi danego piwa
            $inventory->decrement('empty_kegs', $quantity);

            $stock->increment('full_kegs', $quantity);

            KegMovement::create([
                'recipe_id' => $recipe->id,
                'keg_stock_id' => $stock->id,
                'type' => 'production',
                'quantity' => $quantity,
                'note' => $note,
            ]);
        });
    }

    public function issue(Recipe $recipe, int $quantity, ?string $note = null): KegStock
    {
        if ($quantity <= 0) {
            throw new RuntimeException('Ilość kegów musi być większa od 0.');
        }

        return DB::transaction(function () use ($recipe, $quantity, $note) {

            $stock = KegStock::query()
                ->where('recipe_id', $recipe->id)
                ->lockForUpdate()
                ->first();

            if (!$stock || $stock->full_kegs < $quantity) {
                $available = $stock?->full_kegs ?? 0;

                throw new RuntimeException(
                    "Brak wystarczającej ilości pełnych kegów. " .
                    "Dostępne: {$available}."
                );
            }

            $stock->decrement('full_kegs', $quantity);

            KegMovement::create([
                'recipe_id' => $recipe->id,
                'keg_stock_id' => $stock->id,
                'type' => 'issue',
                'quantity' => -$quantity,
                'note' => $note,
            ]);

            $inventory = KegInventories::query()
                ->lockForUpdate()
                ->firstOrFail();

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
        $inventory = KegInventories::query()->firstOrCreate(
            [],
            [
                'total_kegs' => self::TOTAL_KEGS,
                'empty_kegs' => self::TOTAL_KEGS,
            ]
        );

        $full = KegStock::query()->sum('full_kegs');
        $empty = $inventory->empty_kegs;

        return [
            'total' => $inventory->total_kegs,
            'full' => $full,
            'empty' => $empty,
            'full_liters' => $full * self::KEG_CAPACITY_LITERS,
        ];
    }

    public function stocksMovements()
    {
        return KegMovement::with(['recipe', 'stock'])->where('type', 'issue')->get();
    }

    public function pool(Recipe $recipe, int $quantity, ?string $note = null): KegStock
    {
        if ($quantity <= 0) {
            throw new RuntimeException('Ilość kegów musi być większa od 0.');
        }

        return DB::transaction(function () use ($recipe, $quantity, $note) {

            $stock = KegStock::query()
                ->where('recipe_id', $recipe->id)
                ->lockForUpdate()
                ->first();

            if (!$stock || $stock->full_kegs < $quantity) {
                $available = $stock?->full_kegs ?? 0;

                throw new RuntimeException(
                    "Brak wystarczającej ilości pełnych kegów. " .
                    "Dostępne: {$available}."
                );
            }

            $stock->increment('full_kegs', $quantity);

            KegMovement::create([
                'recipe_id' => $recipe->id,
                'keg_stock_id' => $stock->id,
                'type' => 'production',
                'quantity' => $quantity,
                'note' => $note
            ]);

            $inventory = KegInventories::query()
                ->lockForUpdate()
                ->firstOrCreate(
                    [],
                    [
                        'total_kegs' => self::TOTAL_KEGS,
                        'empty_kegs' => self::TOTAL_KEGS,
                    ]
                );

            $inventory->decrement('empty_kegs', $quantity);

            $stock->refresh();

            return $stock->fresh();
        });
    }
}