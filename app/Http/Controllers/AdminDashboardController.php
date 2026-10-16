<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Agency;
use App\Models\Flight;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_agencies' => Agency::count(),
            'pending_agencies' => Agency::pending()->count(),
            'approved_agencies' => Agency::approved()->count(),
            'upcoming_flights' => Flight::upcoming()->count(),
            'total_users' => User::count(),
        ];

        $pendingAgencies = Agency::pending()
            ->with('user')
            ->latest()
            ->take(5)
            ->get();

        $recentUsers = User::latest()
            ->take(5)
            ->get();

        $recentActivity = ActivityLog::with('user')
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'pendingAgencies', 'recentUsers', 'recentActivity'));
    }

    public function agencies(Request $request)
    {
        $status = $request->get('status', 'all');
        $search = $request->get('search', '');

        $query = Agency::with('user');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");
            });
        }

        $agencies = $query->latest()->paginate(15);

        return view('admin.agencies', compact('agencies', 'status', 'search'));
    }

    public function editAgency(Agency $agency): View
    {
        return view('admin.agency-form', compact('agency'));
    }

    public function updateAgency(Request $request, Agency $agency): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:1000'],
            'address' => ['required', 'string', 'max:500'],
            'opening_hours' => ['required', 'string', 'max:255'],
            'whatsapp' => ['required', 'string', 'regex:/^[0-9]+$/', 'min:10', 'max:15'],
        ]);

        $agency->update($validated);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'update_agency',
            'subject_type' => Agency::class,
            'subject_id' => $agency->id,
            'description' => "Modifié l'agence {$agency->name}",
        ]);

        return redirect()->route('admin.agencies')->with('success', 'Agence modifiée avec succès.');
    }

    public function approveAgency(Agency $agency)
    {
        $agency->update(['status' => 'approved']);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'approve_agency',
            'subject_type' => Agency::class,
            'subject_id' => $agency->id,
            'description' => "Approuvé l'agence {$agency->name}",
        ]);

        // Notify agency user
        if ($agency->user) {
            // In production: $agency->user->notify(new AgencyApprovedNotification($agency));
        }

        return back()->with('success', 'Agence approuvée avec succès.');
    }

    public function rejectAgency(Request $request, Agency $agency)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $agency->update(['status' => 'rejected']);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'reject_agency',
            'subject_type' => Agency::class,
            'subject_id' => $agency->id,
            'description' => "Rejeté l'agence {$agency->name}. Raison: {$request->reason}",
        ]);

        // Notify agency user
        if ($agency->user) {
            // In production: $agency->user->notify(new AgencyRejectedNotification($agency, $request->reason));
        }

        return back()->with('success', 'Agence rejetée.');
    }

    public function suspendAgency(Agency $agency)
    {
        $agency->update(['status' => 'suspended']);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'suspend_agency',
            'subject_type' => Agency::class,
            'subject_id' => $agency->id,
            'description' => "Suspendu l'agence {$agency->name}",
        ]);

        return back()->with('success', 'Agence suspendue.');
    }

    public function reactivateAgency(Agency $agency)
    {
        $agency->update(['status' => 'approved']);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'reactivate_agency',
            'subject_type' => Agency::class,
            'subject_id' => $agency->id,
            'description' => "Réactivé l'agence {$agency->name}",
        ]);

        return back()->with('success', 'Agence réactivée.');
    }

    public function deleteAgency(Agency $agency)
    {
        $agency->delete();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'delete_agency',
            'subject_type' => Agency::class,
            'subject_id' => $agency->id,
            'description' => "Supprimé l'agence {$agency->name}",
        ]);

        return back()->with('success', 'Agence supprimée.');
    }

    public function users(Request $request)
    {
        $search = $request->get('search', '');

        $query = User::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(15);

        return view('admin.users', compact('users', 'search'));
    }

    public function flights(Request $request)
    {
        $search = $request->get('search', '');

        $query = Flight::with([
            'agency' => fn ($agencyQuery) => $agencyQuery->withTrashed(),
        ]);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('airline', 'like', "%{$search}%")
                    ->orWhereHas('agency', fn ($agencyQuery) => $agencyQuery
                        ->withTrashed()
                        ->where('name', 'like', "%{$search}%"));
            });
        }

        $flights = $query->upcoming()->latest('flight_date')->paginate(15);

        return view('admin.flights', compact('flights', 'search'));
    }

    public function activity()
    {
        $activities = ActivityLog::with('user')
            ->latest()
            ->paginate(50);

        return view('admin.activity', compact('activities'));
    }
}
