<?php

namespace App\Http\Controllers;

use App\Models\Agency;
use App\Models\AgencyRoute;
use App\Models\Flight;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AgencyDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin_agence']);
    }

    private function getAgency()
    {
        return auth()->user()->agency;
    }

    public function index()
    {
        $agency = $this->getAgency();

        if (!$agency) {
            return view('agency.pending');
        }

        $stats = [
            'upcoming_flights' => $agency->flights()->upcoming()->count(),
            'total_kg_available' => $agency->flights()->upcoming()->sum('remaining_kg'),
        ];

        $upcomingFlights = $agency->flights()
            ->upcoming()
            ->latest('flight_date')
            ->take(5)
            ->get();

        return view('agency.dashboard', compact('agency', 'stats', 'upcomingFlights'));
    }

    public function profile()
    {
        $agency = $this->getAgency();

        if (!$agency) {
            return view('agency.pending');
        }

        return view('agency.profile', compact('agency'));
    }

    public function updateProfile(Request $request)
    {
        $agency = $this->getAgency();

        if (!$agency) {
            return back()->with('error', 'Agence non trouvée.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'address' => 'required|string|max:500',
            'opening_hours' => 'required|string|max:255',
            'whatsapp' => 'required|string|regex:/^[0-9]+$/|min:10|max:15',
        ]);

        $agency->update([
            'name' => $request->name,
            'description' => $request->description,
            'address' => $request->address,
            'opening_hours' => $request->opening_hours,
            'whatsapp' => preg_replace('/[^0-9]/', '', $request->whatsapp),
        ]);

        return back()->with('success', 'Profil mis à jour.');
    }

    public function flights(Request $request)
    {
        $agency = $this->getAgency();

        if (!$agency) {
            return view('agency.pending');
        }

        $flights = $agency->flights()
            ->upcoming()
            ->latest('flight_date')
            ->paginate(15);

        return view('agency.flights', compact('agency', 'flights'));
    }

    public function createFlight()
    {
        $agency = $this->getAgency();

        if (!$agency) {
            return view('agency.pending');
        }

        return view('agency.flight-form', compact('agency'));
    }

    public function storeFlight(Request $request)
    {
        $agency = $this->getAgency();

        if (!$agency) {
            return back()->with('error', 'Agence non trouvée.');
        }

        $request->validate([
            'from_country' => 'required|in:SN,KM,FR',
            'to_country' => 'required|in:SN,KM,FR|different:from_country',
            'flight_date' => 'required|date|after:today',
            'airline' => 'required|string|max:255',
            'total_kg' => 'required|integer|min:1',
            'remaining_kg' => 'required|integer|min:0|max:' . $request->total_kg,
            'price_per_kg' => 'required|numeric|min:0',
            'currency' => 'required|in:FCFA,EUR',
        ]);

        $flightDate = \Carbon\Carbon::parse($request->flight_date);

        Flight::create([
            'agency_id' => $agency->id,
            'from_country' => $request->from_country,
            'to_country' => $request->to_country,
            'flight_date' => $flightDate,
            'airline' => $request->airline,
            'total_kg' => $request->total_kg,
            'remaining_kg' => $request->remaining_kg,
            'price_per_kg' => $request->price_per_kg,
            'currency' => $request->currency,
            'drop_off_deadline' => $flightDate->copy()->subDay(),
            'status' => $request->remaining_kg == 0 ? 'full' : 'open',
        ]);

        return redirect()->route('agency.flights')->with('success', 'Vol créé.');
    }

    public function editFlight(Flight $flight)
    {
        $this->authorize('update', $flight);

        $agency = $this->getAgency();

        return view('agency.flight-form', compact('agency', 'flight'));
    }

    public function updateFlight(Request $request, Flight $flight)
    {
        $this->authorize('update', $flight);

        $request->validate([
            'from_country' => 'required|in:SN,KM,FR',
            'to_country' => 'required|in:SN,KM,FR|different:from_country',
            'flight_date' => 'required|date|after:today',
            'airline' => 'required|string|max:255',
            'total_kg' => 'required|integer|min:1',
            'remaining_kg' => 'required|integer|min:0|max:' . $request->total_kg,
            'price_per_kg' => 'required|numeric|min:0',
            'currency' => 'required|in:FCFA,EUR',
            'status' => 'required|in:open,full,cancelled',
        ]);

        $flightDate = \Carbon\Carbon::parse($request->flight_date);

        $flight->update([
            'from_country' => $request->from_country,
            'to_country' => $request->to_country,
            'flight_date' => $flightDate,
            'airline' => $request->airline,
            'total_kg' => $request->total_kg,
            'remaining_kg' => $request->remaining_kg,
            'price_per_kg' => $request->price_per_kg,
            'currency' => $request->currency,
            'drop_off_deadline' => $flightDate->copy()->subDay(),
            'status' => $request->status,
        ]);

        return redirect()->route('agency.flights')->with('success', 'Vol mis à jour.');
    }

    public function deleteFlight(Flight $flight)
    {
        $this->authorize('delete', $flight);

        $flight->delete();

        return back()->with('success', 'Vol supprimé.');
    }

    public function duplicateFlight(Flight $flight)
    {
        $this->authorize('view', $flight);

        $newFlight = $flight->replicate();
        $newFlight->flight_date = $flight->flight_date->addWeek();
        $newFlight->drop_off_deadline = $newFlight->flight_date->copy()->subDay();
        $newFlight->remaining_kg = $newFlight->total_kg;
        $newFlight->status = 'open';
        $newFlight->save();

        return back()->with('success', 'Vol dupliqué.');
    }

    public function routes()
    {
        $agency = $this->getAgency();

        if (!$agency) {
            return view('agency.pending');
        }

        return view('agency.routes', compact('agency'));
    }

    public function updateRoutes(Request $request)
    {
        $agency = $this->getAgency();

        if (!$agency) {
            return back()->with('error', 'Agence non trouvée.');
        }

        $request->validate([
            'routes' => 'required|array|min:1',
            'routes.*' => 'array',
            'routes.*.from' => 'required|in:SN,KM,FR',
            'routes.*.to' => 'required|in:SN,KM,FR|different:routes.*.from',
        ]);

        DB::transaction(function () use ($agency, $request) {
            $agency->routes()->delete();

            foreach ($request->routes as $route) {
                AgencyRoute::create([
                    'agency_id' => $agency->id,
                    'from_country' => $route['from'],
                    'to_country' => $route['to'],
                ]);
            }
        });

        return back()->with('success', 'Trajets mis à jour.');
    }
}
