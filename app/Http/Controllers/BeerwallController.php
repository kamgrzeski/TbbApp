<?php

namespace App\Http\Controllers;

use App\Queries\BeerwallQueries;
use App\Http\Requests\BeerwallSettingsRequest;
use App\Http\Requests\StoreBeerwallBeerRequest;
use App\Http\Requests\UpdateBeerwallBeerRequest;
use App\Models\Beerwall;
use App\Services\FirebaseService;
use Illuminate\Http\Request;

class BeerwallController extends Controller
{
    public function indexFront()
    {
        return view('beerwall.index')->with([
            'beerwall' => BeerwallQueries::ordered(),
        ]);
    }
    public function indexHalloween()
    {
        return view('beerwall.index_halloween')->with([
            'beerwall' => BeerwallQueries::ordered(),
        ]);
    }
    public function indexChristmas()
    {
        return view('beerwall.index_christmas')->with([
            'beerwall' => BeerwallQueries::ordered(),
        ]);
    }
    public function index()
    {
        $beers = BeerwallQueries::ordered();
        $drafts = BeerwallQueries::drafts();
        return view('beerwall.admin.index', compact('beers', 'drafts'));
    }

    public function editSettings(FirebaseService $firebase)
    {
        $settings = $firebase->getSettings();
        return view('beerwall.admin.settings', compact('settings'));
    }

    public function updateSettings(BeerwallSettingsRequest $request, FirebaseService $firebase)
    {
        $data = $request->validated();

        if (! $firebase->setSettings($data)) {
            return redirect()->route('beerwall.admin.settings.edit')
                ->with('error', 'Nie udało się zapisać ustawień w Firebase. Sprawdź log aplikacji.');
        }

        return redirect()->route('beerwall.admin.settings.edit')->with('success', 'Ustawienia godzin zamknięcia zostały zapisane!');
    }

    public function create()
    {
        return view('beerwall.admin.create');
    }

    public function store(StoreBeerwallBeerRequest $request)
    {
        $data = $request->validated();

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

    public function update(UpdateBeerwallBeerRequest $request, Beerwall $beerwall)
    {
        $data = $request->validated();

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

    public function duplicate(Beerwall $beerwall)
    {
        $beer = $beerwall->replicate();
        $beer->beer_name = $beerwall->beer_name . ' (kopia)';

        $beer->is_ended = $beerwall->is_ended;
        $beer->is_coming_soon = $beerwall->is_coming_soon;
        $beer->is_premiere = $beerwall->is_premiere;
        $beer->is_draft = $beerwall->is_draft;

        $beer->save();

        return redirect()->route('beerwall.admin.edit', $beer)->with('success', 'Piwo zostało skopiowane. Możesz teraz edytować jego dane!');
    }
}
