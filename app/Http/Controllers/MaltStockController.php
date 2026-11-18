<?php

namespace App\Http\Controllers;

use App\Queries\MaltStockQueries;
use App\Http\Requests\StoreMaltStockRequest;
use App\Http\Requests\UpdateMaltStockRequest;
use App\Models\MaltStock;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MaltStockController extends Controller
{
    public function index(): View
    {
        $malts = MaltStockQueries::orderedByKgDesc();

        $stats = [
            'total' => $malts->count(),
            'active' => $malts->where('is_enzymatic_active', true)->count(),
            'inactive' => $malts->where('is_enzymatic_active', false)->count(),
            'empty' => $malts->where('kg', '<=', 0)->count(),
            'total_kg' => $malts->sum('kg'),
        ];

        return view('malts.index', compact('malts', 'stats'));
    }

    public function create(): View
    {
        return view('malts.create');
    }

    public function store(StoreMaltStockRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        MaltStock::create($validated);

        return redirect()->route('malts.admin.index')->with('success', 'Słód został dodany do magazynu.');
    }

    public function edit(MaltStock $maltStock): View
    {
        return view('malts.edit', compact('maltStock'));
    }

    public function update(UpdateMaltStockRequest $request, MaltStock $maltStock): RedirectResponse
    {
        $validated = $request->validated();

        $validated['is_enzymatic_active'] = $request->boolean('is_enzymatic_active');

        $maltStock->update($validated);

        return redirect()->route('malts.admin.index')->with('success', 'Dane słodu zostały zaktualizowane.');
    }

    public function destroy(MaltStock $maltStock): RedirectResponse
    {
        $maltStock->delete();

        return redirect()->route('malts.admin.index')->with('success', 'Słód został usunięty z magazynu.');
    }

    public function subtractBag(MaltStock $maltStock): RedirectResponse
    {
        return redirect()->route('malts.admin.index')->with('error', 'Ta funkcja została wyłączona.');
    }
}
