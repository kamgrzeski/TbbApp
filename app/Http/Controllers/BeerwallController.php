<?php

namespace App\Http\Controllers;

use App\Models\Beerwall;
use Illuminate\Http\Request;

class BeerwallController extends Controller
{
    /**
     * Widok publiczny (dla telewizora)
     */
    public function indexFront()
    {
        return view('beerwall.index')->with([
            'beerwall' => Beerwall::all()
        ]);
    }
    /**
     * Widok publiczny (dla telewizora)
     */
    public function indexFrontOld()
    {
        return view('beerwall.index-old')->with([
            'beerwall' => Beerwall::all()
        ]);
    }

    /**
     * Lista piw w panelu admina
     */
    public function index()
    {
        $beers = Beerwall::orderBy('id', 'asc')->get();
        return view('beerwall.admin.index', compact('beers'));
    }

    /**
     * Formularz dodawania nowego piwa
     */
    public function create()
    {
        return view('beerwall.admin.create');
    }

    /**
     * Zapisywanie nowego piwa
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'beer_name' => 'required|string|max:255',
            'beer_style' => 'nullable|string|max:255',
            'beer_description' => 'nullable|string',
            'beer_blg' => 'nullable|string',
            'beer_alc' => 'nullable|string',
            'beer_price_small' => 'required|numeric',
            'beer_price_medium' => 'required|numeric',
            'beer_price_large' => 'required|numeric',
        ]);

        $data['is_ended'] = $request->has('is_ended');
        $data['is_coming_soon'] = $request->has('is_coming_soon');
        $data['is_premiere'] = $request->has('is_premiere');

        Beerwall::create($data);

        return redirect()->route('beerwall.admin.index')->with('success', 'Piwo zostało dodane do karty!');
    }

    /**
     * Formularz edycji
     */
    public function edit(Beerwall $beerwall)
    {
        return view('beerwall.admin.create', compact('beerwall'));
    }

    /**
     * Aktualizacja piwa
     */
    public function update(Request $request, Beerwall $beerwall)
    {
        $data = $request->validate([
            'beer_name' => 'required|string|max:255',
            'beer_style' => 'nullable|string|max:255',
            'beer_description' => 'nullable|string',
            'beer_blg' => 'nullable|string',
            'beer_alc' => 'nullable|string',
            'beer_price_small' => 'required|numeric',
            'beer_price_medium' => 'required|numeric',
            'beer_price_large' => 'required|numeric',
        ]);

        $data['is_ended'] = $request->has('is_ended');
        $data['is_coming_soon'] = $request->has('is_coming_soon');
        $data['is_premiere'] = $request->has('is_premiere');

        $beerwall->update($data);

        return redirect()->route('beerwall.admin.index')->with('success', 'Dane piwa zostały zaktualizowane!');
    }

    /**
     * Szybka zmiana statusu (np. wyprzedane) bez wchodzenia w edycję
     */
    public function updateStatus(Request $request, Beerwall $beerwall)
    {
        $type = $request->input('type'); // 'ended', 'coming', 'premiere'

        if ($type === 'ended') {
            $beerwall->is_ended = !$beerwall->is_ended;
        } elseif ($type === 'coming') {
            $beerwall->is_coming_soon = !$beerwall->is_coming_soon;
        }

        $beerwall->save();

        return back()->with('success', 'Status piwa został zmieniony!');
    }

    /**
     * Usunięcie piwa z karty
     */
    public function destroy(Beerwall $beerwall)
    {
        $beerwall->delete();
        return redirect()->route('beerwall.admin.index')->with('success', 'Piwo zostało usunięte z karty.');
    }

    /**
     * Klonowanie piwa (przydatne przy nowej warce tego samego stylu)
     */
    public function clone(Beerwall $beerwall)
    {
        $newBeer = $beerwall->replicate();
        $newBeer->beer_name = $beerwall->beer_name . ' (Kopia)';
        $newBeer->is_ended = false; // Nowa kranówka nie jest na start wyprzedana
        $newBeer->save();

        return redirect()->route('beerwall.admin.edit', $newBeer)->with('success', 'Skopiowano dane piwa.');
    }
}