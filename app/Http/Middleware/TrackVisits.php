<?php

namespace App\Http\Middleware;

use App\Models\Visit;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackVisits
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // On ne compte pas les visites sur le panel admin, ni les appels API internes
        if (! $request->is('admin*') &&
            ! $request->is('livewire*') &&
            ! $request->is('up') &&
            ! $request->is('_debugbar*')) {

            // On compte une "Visite" par session (et non par page vue)
            if (! $request->session()->has('visit_recorded')) {
                
                // RGPD : Vérification du consentement
                $consent = $request->cookie('cookie_consent');
                $ipAddress = $request->ip();

                // Si pas de consentement explicite, on anonymise l'IP
                if ($consent !== 'accepted') {
                    $ipAddress = 'ANONYMOUS'; 
                }

                Visit::create([
                    'ip_address' => $ipAddress,
                    'url' => $request->fullUrl(),
                ]);

                $request->session()->put('visit_recorded', true);
            }
        }

        return $next($request);
    }
}
