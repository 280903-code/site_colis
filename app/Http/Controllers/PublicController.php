<?php

namespace App\Http\Controllers;

use App\Models\Agency;
use App\Models\AgencyRoute;
use App\Support\Country;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function home()
    {
        $agencies = Agency::approved()
            ->with(['routes', 'flights' => fn ($q) => $q->upcoming()])
            ->get();
        $countryOptions = $this->countryOptions();

        return view('home', compact('agencies', 'countryOptions'));
    }

    public function agences(Request $request)
    {
        $filters = $request->validate([
            'from' => ['nullable', 'string', 'max:100'],
            'to' => ['nullable', 'string', 'max:100'],
        ]);
        $from = $this->countryFilter($filters['from'] ?? '_');
        $to = $this->countryFilter($filters['to'] ?? '_');

        $query = Agency::approved()->with(['routes', 'flights' => fn ($q) => $q->upcoming()]);

        if ($from !== '_' || $to !== '_') {
            $query->whereHas('routes', function ($q) use ($from, $to) {
                if ($from !== '_') {
                    $q->whereRaw('LOWER(TRIM(from_country)) = ?', [mb_strtolower($from)]);
                }
                if ($to !== '_') {
                    $q->whereRaw('LOWER(TRIM(to_country)) = ?', [mb_strtolower($to)]);
                }
            });
        }

        $filteredAgencies = $query->get();
        $hasFilter = $from !== '_' || $to !== '_';

        if ($from !== '_' && $to !== '_') {
            $subtitle = Country::label($from).' → '.Country::label($to);
        } elseif ($from !== '_') {
            $subtitle = 'Départ : '.Country::label($from);
        } elseif ($to !== '_') {
            $subtitle = 'Destination : '.Country::label($to);
        } else {
            $subtitle = 'Tous les trajets';
        }

        $countryOptions = $this->countryOptions();

        return view('agences', compact('filteredAgencies', 'from', 'to', 'hasFilter', 'subtitle', 'countryOptions'));
    }

    public function agence(Request $request, $slug)
    {
        $filters = $request->validate([
            'from' => ['nullable', 'string', 'max:100'],
            'to' => ['nullable', 'string', 'max:100'],
        ]);
        $from = $this->countryFilter($filters['from'] ?? '_');
        $to = $this->countryFilter($filters['to'] ?? '_');

        $agency = Agency::approved()
            ->with(['routes', 'flights' => fn ($q) => $q->upcoming()])
            ->where('slug', $slug)
            ->firstOrFail();

        $allFlights = $agency->flights->sortBy('flight_date');
        $totalKgAvailable = $allFlights->sum('remaining_kg');

        $filteredFlights = $allFlights
            ->filter(fn ($f) => $from === '_' || Country::normalize($f->from_country) === $from)
            ->filter(fn ($f) => $to === '_' || Country::normalize($f->to_country) === $to);

        return view('agence', compact('agency', 'from', 'to', 'allFlights', 'filteredFlights', 'totalKgAvailable'));
    }

    private function countryFilter(string $country): string
    {
        $country = trim($country);

        return $country === '' || $country === '_' ? '_' : Country::normalize($country);
    }

    /**
     * @return list<string>
     */
    private function countryOptions(): array
    {
        $routeCountries = AgencyRoute::query()
            ->whereHas('agency', fn ($query) => $query->approved())
            ->get(['from_country', 'to_country'])
            ->flatMap(fn (AgencyRoute $route) => [$route->from_country, $route->to_country])
            ->map(fn (string $country) => Country::label($country));

        return collect(Country::suggestions())
            ->merge($routeCountries)
            ->unique()
            ->values()
            ->all();
    }
}
