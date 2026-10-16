<?php

namespace App\Http\Controllers;

use App\Http\Requests\PartnerRegistrationRequest;
use App\Models\Agency;
use App\Models\PartnerApplication;
use App\Models\User;
use App\Notifications\NewPartnerNotification;
use App\Notifications\PartnerEmailVerificationNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PartnerController extends Controller
{
    public function showRegistrationForm(): View
    {
        return view('auth.register-partner');
    }

    public function showVerificationPending(): View
    {
        return view('auth.partner-verification-pending');
    }

    public function register(PartnerRegistrationRequest $request): RedirectResponse
    {
        if ($request->filled('website')) {
            return back()->with('success', 'Votre demande a été envoyée avec succès.');
        }

        $validated = $request->validated();
        $token = Str::random(64);
        $application = PartnerApplication::updateOrCreate(
            ['email' => $validated['email']],
            [
                'contact_name' => $validated['contact_name'],
                'password' => Hash::make($validated['password']),
                'agency_name' => $validated['agency_name'],
                'address' => $validated['address'],
                'opening_hours' => $validated['opening_hours'],
                'whatsapp' => $validated['whatsapp'],
                'routes' => $validated['routes'],
                'verification_token_hash' => hash('sha256', $token),
                'expires_at' => now()->addDay(),
            ],
        );

        $this->sendVerificationEmail($application, $token);

        return redirect()->route('partner.verification.pending')->with(
            'status',
            'Un e-mail de vérification vous a été envoyé. Confirmez votre adresse pour créer votre compte partenaire.',
        );
    }

    public function resendVerificationEmail(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        $application = PartnerApplication::query()
            ->where('email', Str::lower(trim($validated['email'])))
            ->first();

        if ($application) {
            $token = Str::random(64);

            $application->update([
                'verification_token_hash' => hash('sha256', $token),
                'expires_at' => now()->addDay(),
            ]);

            $this->sendVerificationEmail($application, $token);
        }

        return redirect()->route('partner.verification.pending')->with(
            'status',
            'Si une demande est en attente pour cette adresse, un nouveau lien de vérification vient d’être envoyé.',
        );
    }

    public function verify(Request $request, PartnerApplication $partnerApplication, string $token): View|RedirectResponse
    {
        $this->ensureValidApplicationToken($partnerApplication, $token);

        if ($request->isMethod('GET')) {
            return view('auth.verify-partner');
        }

        $agency = DB::transaction(function () use ($partnerApplication, $token): Agency {
            $application = PartnerApplication::query()
                ->whereKey($partnerApplication->id)
                ->lockForUpdate()
                ->firstOrFail();

            $this->ensureValidApplicationToken($application, $token);

            abort_if(
                User::query()->where('email', $application->email)->exists(),
                409,
                'Cette adresse e-mail est déjà utilisée.',
            );

            $user = User::create([
                'name' => $application->contact_name,
                'email' => $application->email,
                'password' => $application->password,
                'role' => 'admin_agence',
            ]);
            $user->markEmailAsVerified();

            $agency = Agency::create([
                'user_id' => $user->id,
                'name' => $application->agency_name,
                'slug' => Str::slug($application->agency_name).'-'.Str::random(6),
                'description' => 'Agence partenaire',
                'address' => $application->address,
                'opening_hours' => $application->opening_hours,
                'whatsapp' => $application->whatsapp,
                'status' => 'pending',
            ]);

            foreach ($application->routes as $route) {
                $agency->routes()->create([
                    'from_country' => $route['from'],
                    'to_country' => $route['to'],
                ]);
            }

            $application->delete();

            return $agency;
        });

        $admin = User::query()->where('role', 'admin')->first();

        if ($admin) {
            $admin->notify((new NewPartnerNotification($agency))->afterCommit());
        }

        return redirect()->route('login')->with(
            'status',
            'Votre adresse e-mail est confirmée. Votre demande de partenariat attend maintenant l’approbation de l’administrateur.',
        );
    }

    private function ensureValidApplicationToken(PartnerApplication $application, string $token): void
    {
        abort_unless(
            hash_equals($application->verification_token_hash, hash('sha256', $token))
                && $application->expires_at->isFuture(),
            403,
            'Ce lien de vérification est invalide ou expiré.',
        );
    }

    private function sendVerificationEmail(PartnerApplication $application, string $token): void
    {
        $verificationUrl = URL::temporarySignedRoute(
            'partner.verify',
            now()->addDay(),
            [
                'partnerApplication' => $application->id,
                'token' => $token,
            ],
        );

        Notification::route('mail', $application->email)->notify(
            new PartnerEmailVerificationNotification($application->contact_name, $verificationUrl),
        );
    }
}
