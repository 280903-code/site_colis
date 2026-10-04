<?php

namespace App\Http\Controllers;

use App\Models\Agency;
use App\Models\Flight;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function home()
    {
        $agencies = Agency::approved()
            ->with(['routes', 'flights' => fn($q) => $q->upcoming()])
            ->get();

        return view('home', compact('agencies'));
    }

    public function agences(Request $request)
    {
        $from = $request->get('from', '_');
        $to = $request->get('to', '_');
        $countries = ['SN' => 'Sénégal', 'KM' => 'Comores', 'FR' => 'France'];

        $query = Agency::approved()->with(['routes', 'flights' => fn($q) => $q->upcoming()]);

        if ($from !== '_' || $to !== '_') {
            $query->whereHas('routes', function ($q) use ($from, $to) {
                if ($from !== '_') {
                    $q->where('from_country', $from);
                }
                if ($to !== '_') {
                    $q->where('to_country', $to);
                }
            });
        }

        $filteredAgencies = $query->get();
        $hasFilter = $from !== '_' || $to !== '_';

        if ($from !== '_' && $to !== '_') {
            $subtitle = "{$countries[$from]} → {$countries[$to]}";
        } elseif ($from !== '_') {
            $subtitle = "Départ : {$countries[$from]}";
        } elseif ($to !== '_') {
            $subtitle = "Destination : {$countries[$to]}";
        } else {
            $subtitle = "Tous les trajets";
        }

        return view('agences', compact('filteredAgencies', 'from', 'to', 'hasFilter', 'subtitle'));
    }

    public function agence(Request $request, $slug)
    {
        $from = $request->get('from', '_');
        $to = $request->get('to', '_');
        $countries = ['SN' => 'Sénégal', 'KM' => 'Comores', 'FR' => 'France'];

        $agency = Agency::approved()
            ->with(['routes', 'flights' => fn($q) => $q->upcoming()])
            ->where('slug', $slug)
            ->firstOrFail();

        $allFlights = $agency->flights->sortBy('flight_date');
        $totalKgAvailable = $allFlights->sum('remaining_kg');

        $filteredFlights = $allFlights
            ->filter(fn($f) => $from === '_' || $f->from_country === $from)
            ->filter(fn($f) => $to === '_' || $f->to_country === $to);

        return view('agence', compact('agency', 'from', 'to', 'countries', 'allFlights', 'filteredFlights', 'totalKgAvailable'));
    }
}
