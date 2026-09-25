<?php

namespace App\Http\Controllers;

use App\Models\GoPosItems;
use App\Models\KegMovement;
use App\Models\WebhookHistory;
use App\Services\GoPosApiService;

class GoPosController extends Controller
{
    public function __construct(private readonly GoPosApiService $goPosApiService)
    {
    }

    public function index()
    {
        $items = GoPosItems::query()->get();

        return view('gopos.index', [
            'items' => $items
        ]);
    }

    public function sync()
    {
        $items = $this->goPosApiService->getItems();

        foreach ($items['data'] as $item) {
            if ($item['category_id'] == 2 && $item['status'] === 'ENABLED') {
                GoPosItems::updateOrCreate(
                    [
                        'gopos_id' => $item['id'],
                    ],
                    [
                        'gopos_name' => $item['name'],
                        'gopos_price' => $item['price']['amount'],
                        'gopos_catgory_id' => $item['category_id'],
                        'gopos_status' => $item['status'],
                    ]
                );
            }
        }

        return redirect()
            ->route('gopos.admin.index')
            ->with('success', 'Produkty GoPos zostały zsynchronizowane.');
    }

    public function webhook()
    {
        $data = request()->all();

        $eventType = $data['event_type'] ?? null;

        $webhookHistory = WebhookHistory::create([
            'event_type' => $data['event_type'] ?? null,
            'type' => $data['type'] ?? null,
            'organization_id' => $data['organization_id'] ?? null,
            'resource_id' => $data['resource_id'] ?? null,
            'occurred_at' => $data['occurred_at'] ?? null,
            'payload' => $data,
            'status' => 'received',
        ]);

        $description = [];

        if ($eventType === 'ORDER_CREATED') {

            $orderDetails = $this->goPosApiService->getOrder(
                $data['resource_id']
            );

            $items = $orderDetails['data']['items'] ?? [];

            $description[] = sprintf(
                'Utworzono zamówienie. Liczba pozycji: %d.',
                count($items)
            );

            $processedItems = 0;
            $skippedItems = 0;
            $totalDeduction = 0;

            foreach ($items as $item) {

                $deductionMl = 0;

                if (
                    !empty($item['name']) &&
                    preg_match(
                        '/(\d+(?:[.,]\d+)?)\s*(ml|L)\b/i',
                        $item['name'],
                        $matches
                    )
                ) {
                    $value = (float) str_replace(',', '.', $matches[1]);
                    $unit = strtolower($matches[2]);

                    $deductionMl = $unit === 'l'
                        ? $value * 1000
                        : $value;
                }

                // Nie ma pojemności w nazwie produktu
                if ($deductionMl <= 0) {

                    $skippedItems++;

                    $description[] = sprintf(
                        'Pominięto produkt "%s" (ID GoPos: %s) - nie znaleziono pojemności w nazwie.',
                        $item['name'] ?? '-',
                        $item['item_id'] ?? '-'
                    );

                    continue;
                }

                $kegMovement = KegMovement::where('is_current', 1)
                    ->whereHas('recipe', function ($query) use ($item) {
                        $query->whereJsonContains(
                            'gopos_item_ids',
                            (string) $item['item_id']
                        );
                    })
                    ->first();

                if (!$kegMovement) {

                    $skippedItems++;

                    $description[] = sprintf(
                        'Pominięto produkt "%s" (ID GoPos: %s, %s ml) - nie znaleziono aktywnej beczki z przypisaną recepturą.',
                        $item['name'] ?? '-',
                        $item['item_id'] ?? '-',
                        $deductionMl
                    );

                    continue;
                }

                $oldCapacity = $kegMovement->capacity;

                $kegMovement->decrement(
                    'capacity',
                    $deductionMl
                );

                $newCapacity = $oldCapacity - $deductionMl;

                $processedItems++;
                $totalDeduction += $deductionMl;

                $description[] = sprintf(
                    'Produkt "%s" (ID GoPos: %s) - odjęto %s ml z KegMovement #%s. Pojemność: %s ml → %s ml.',
                    $item['name'] ?? '-',
                    $item['item_id'] ?? '-',
                    number_format($deductionMl, 0, ',', ' '),
                    $kegMovement->id,
                    number_format($oldCapacity, 0, ',', ' '),
                    number_format($newCapacity, 0, ',', ' ')
                );
            }

            $description[] = sprintf(
                'Podsumowanie: przetworzono %d pozycji, pominięto %d pozycji, łącznie odjęto %s ml.',
                $processedItems,
                $skippedItems,
                number_format($totalDeduction, 0, ',', ' ')
            );
        } else {

            $description[] = sprintf(
                'Webhook typu "%s" został odebrany. Brak dodatkowej obsługi dla tego typu zdarzenia.',
                $eventType ?? '-'
            );
        }

        $webhookHistory->update([
            'status' => 'processed',
            'description' => implode("\n", $description),
            'processed_at' => now(),
        ]);

        return response()->json([
            'success' => true,
        ]);
    }

    public function webhooksList()
    {
        $webhooks = WebhookHistory::orderByDesc('id')->get();

        return view('gopos.webhooks', [
            'webhooks' => $webhooks
        ]);
    }
}
