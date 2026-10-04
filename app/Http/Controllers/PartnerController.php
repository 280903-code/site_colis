<?php

namespace App\Http\Controllers;

use App\Http\Requests\PartnerRegistrationRequest;
use App\Models\Agency;
use App\Models\AgencyRoute;
use App\Models\User;
use App\Notifications\NewPartnerNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PartnerController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register-partner');
    }

    public function register(PartnerRegistrationRequest $request)
    {
        // Check honeypot
        if ($request->filled('website')) {
            return back()->with('success', 'Votre demande a été envoyée avec succès.');
        }

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->contact_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'admin_agence',
            ]);

            $agency = Agency::create([
                'user_id' => $user->id,
                'name' => $request->agency_name,
                'slug' => str()->slug($request->agency_name) . '-' . str()->random(6),
                'description' => 'Agence partenaire',
                'address' => $request->address,
                'opening_hours' => $request->opening_hours,
                'whatsapp' => $request->whatsapp,
                'status' => 'pending',
            ]);

            foreach ($request->routes as $route) {
                AgencyRoute::create([
                    'agency_id' => $agency->id,
                    'from_country' => $route['from'],
                    'to_country' => $route['to'],
                ]);
            }

            // Send verification email
            $user->sendEmailVerificationNotification();

            // Notify admin
            $admin = User::where('role', 'admin')->first();
            if ($admin) {
                $admin->notify(new NewPartnerNotification($agency));
            }
        });

        return redirect()->route('login')->with('status', 'Votre inscription a été envoyée. Veuillez vérifier votre email et attendre l\'approbation de l\'administrateur.');
    }
}
