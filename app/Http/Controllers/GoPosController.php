<?php

namespace App\Http\Controllers;

use App\Models\GoPosItems;
use App\Models\KegMovement;
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
        $eventType = $data['event_type'];

        if ($eventType == 'ORDER_CREATED') {

            $orderDetails = $this->goPosApiService->getOrder($data['resource_id']);

            $items = $orderDetails['data']['items'];

            $itemsName = [];

            foreach ($items as $item) {

                $deductionMl = 0;

                if (preg_match('/(\d+)\s*(ml|L)\b/i', $item['name'], $matches)) {

                    $value = (int) $matches[1];
                    $unit = strtolower($matches[2]);

                    $deductionMl = $unit === 'l'
                        ? $value * 1000
                        : $value;
                }

                $itemsName[] = [
                    'item_id' => $item['item_id'],
                    'original_name' => $item['name'],
                    'deduction' => $deductionMl,
                    'created_at' => $item['created_at']
                ];
            }

            foreach ($itemsName as $item) {
                $kegMovement = KegMovement::where('is_current', 1)
                    ->whereHas('recipe', function ($query) use ($item) {
                        $query->whereJsonContains('gopos_item_ids', (string) $item['item_id']);
                    })
                    ->first();

                if($kegMovement) {
                    $kegMovement->decrement('capacity', $item['deduction']);
                }
            }
        }
    }
}
