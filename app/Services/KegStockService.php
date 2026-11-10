<?php

namespace App\Services;

use App\Models\GoPosItems;
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
    public const KEG_MAX_ML = 47000;

    // Liczba kranów - ich kegi są zawsze podłączone, więc nie wchodzą do puli
    public const TAPS_COUNT = 4;

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

            $stock = KegStock::query()
                ->where('recipe_id', $recipe->id)
                ->lockForUpdate()
                ->first();

            $goPosItems = GoPosItems::get();

            if (!$stock || $stock->full_kegs < $quantity) {
                $available = $stock?->full_kegs ?? 0;

                throw new RuntimeException(
                    "Brak wystarczającej ilości pełnych kegów. " .
                    "Dostępne: {$available}."
                );
            }

            $onOtherTap = KegMovement::query()
                ->where('type', 'issue')
                ->where('is_current', true)
                ->where('recipe_id', $recipe->id)
                ->where('tap_number', '!=', $tapNumber)
                ->lockForUpdate()
                ->first();

            if ($onOtherTap) {
                throw new RuntimeException("Piwo {$recipe->name} jest już podłączone do kranu {$onOtherTap->tap_number}.");
            }

            $tapCurrent = KegMovement::query()
                ->where('type', 'issue')
                ->where('is_current', true)
                ->where('tap_number', $tapNumber)
                ->lockForUpdate()
                ->get();

            if ($tapCurrent->isNotEmpty()) {
                // Inne piwo można podłączyć dopiero, gdy poprzednie skończyło się w chłodni.
                $otherRecipeIds = $tapCurrent
                    ->pluck('recipe_id')
                    ->reject(fn ($id) => $id === $recipe->id);

                if ($otherRecipeIds->isNotEmpty()) {
                    $stillInCooler = KegStock::query()
                        ->whereIn('recipe_id', $otherRecipeIds)
                        ->where('full_kegs', '>', 0)
                        ->exists();

                    if ($stillInCooler) {
                        throw new RuntimeException("Kran {$tapNumber} ma podłączone inne piwo z pełnymi kegami w chłodni.");
                    }
                }

                // Nowy keg zastępuje poprzedni w rejestrze podłączeń tego kranu.
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
            'available_empty' => max(0, $empty - self::TAPS_COUNT),
            'full_liters' => $full * self::KEG_CAPACITY_LITERS,
        ];
    }

    public function stocksMovements()
    {
        return KegMovement::with(['recipe', 'stock'])->where('is_current', 1)->where('type', 'issue')->orderByDesc('id')->get();
    }

    /**
     * Keg podłączone obecnie do kranów (is_current = 1).
     */
    public function currentTapConnections()
    {
        return KegMovement::with('recipe')
            ->where('type', 'issue')
            ->where('is_current', true)
            ->orderByDesc('id')
            ->get();
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

            if (!$stock) {
                $stock = KegStock::create([
                    'recipe_id' => $recipe->id,
                    'full_kegs' => 0,
                ]);
            }

            $inventory = KegInventories::query()
                ->lockForUpdate()
                ->firstOrCreate(
                    [],
                    [
                        'total_kegs' => self::TOTAL_KEGS,
                        'empty_kegs' => self::TOTAL_KEGS,
                    ]
                );

            $available = max(0, $inventory->empty_kegs - self::TAPS_COUNT);

            if ($available < $quantity) {
                throw new RuntimeException(
                    "Brak wystarczającej ilości kegów do napełnienia (4 krany są zajęte). " .
                    "Dostępne: {$available}."
                );
            }

            $stock->increment('full_kegs', $quantity);

            // Piwo z pełnymi kegami nie może pozostać w archiwum, inaczej nie da się go podłączyć.
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
            $stock = KegStock::query()
                ->where('recipe_id', $recipe->id)
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

            $inventory = KegInventories::query()
                ->lockForUpdate()
                ->firstOrCreate(
                    [],
                    [
                        'total_kegs' => self::TOTAL_KEGS,
                        'empty_kegs' => self::TOTAL_KEGS,
                    ]
                );

            $inventory->increment('empty_kegs', $quantity);

            if ($stock->full_kegs == 0) {
                $stock->update(['is_archived' => true]);
            }
        });
    }
}