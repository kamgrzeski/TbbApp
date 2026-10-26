<?php

namespace App\Http\Controllers;

use App\Models\Beerwall;
use App\Models\BeerwallSetting;
use Illuminate\Http\Request;

class BeerwallController extends Controller
{
    public function indexFront()
    {
        return view('beerwall.index')->with([
            'beerwall' => Beerwall::orderBy('position')->get(),
            'settings' => BeerwallSetting::first()
        ]);
    }
    public function indexHalloween()
    {
        return view('beerwall.index_halloween')->with([
            'beerwall' => Beerwall::orderBy('position')->get(),
            'settings' => BeerwallSetting::first()
        ]);
    }
    public function indexChristmas()
    {
        return view('beerwall.index_christmas')->with([
            'beerwall' => Beerwall::orderBy('position')->get(),
            'settings' => BeerwallSetting::first()
        ]);
    }
    public function index()
    {
        $beers = Beerwall::orderBy('position', 'asc')->get();
        return view('beerwall.admin.index', compact('beers'));
    }

    public function editSettings()
    {
        $settings = BeerwallSetting::first();
        return view('beerwall.admin.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $data = $request->validate([
            'monday_close_time' => 'nullable',
            'tuesday_close_time' => 'nullable',
            'wednesday_close_time' => 'nullable',
            'thursday_close_time' => 'nullable',
            'friday_close_time' => 'nullable',
            'saturday_close_time' => 'nullable',
            'sunday_close_time' => 'nullable',
        ]);

        $settings = BeerwallSetting::firstOrCreate(
            ['id' => 1],
            $data
        );

        $settings->update($data);

        return redirect()->route('beerwall.admin.settings.edit')->with('success', 'Ustawienia godzin zamknięcia zostały zapisane!');
    }

    public function create()
    {
        return view('beerwall.admin.create');
    }


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

        $data['is_ended'] = $request->beer_status === 'ended';
        $data['is_coming_soon'] = $request->beer_status === 'coming_soon';
        $data['is_premiere'] = $request->beer_status === 'premiere';
        $data['is_draft'] = $request->beer_status === 'is_draft';

        unset($data['beer_status']);

        Beerwall::create($data);

        return redirect()->route('beerwall.admin.index')->with('success', 'Piwo zostało dodane do karty!');
    }

    public function edit(Beerwall $beerwall)
    {
        return view('beerwall.admin.create', compact('beerwall'));
    }

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
            'position' => 'required|numeric'
        ]);

        $data['is_ended'] = $request->beer_status === 'ended';
        $data['is_coming_soon'] = $request->beer_status === 'coming_soon';
        $data['is_premiere'] = $request->beer_status === 'premiere';
        $data['is_draft'] = $request->beer_status === 'is_draft';

        unset($data['beer_status']);

        $beerwall->update($data);

        return redirect()->route('beerwall.admin.index')->with('success', 'Dane piwa zostały zaktualizowane!');
    }

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

    public function destroy(Beerwall $beerwall)
    {
        $beerwall->delete();
        return redirect()->route('beerwall.admin.index')->with('success', 'Piwo zostało usunięte z karty.');
    }
}