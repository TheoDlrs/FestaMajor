<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Frame-Options', 'SAMEORIGIN'); // Empêche le clickjacking
        $response->headers->set('X-Content-Type-Options', 'nosniff'); // Empêche le sniffing MIME
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin'); // Protège la vie privée
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()'); // Bloque l'accès aux capteurs inutilement
        
        // HSTS (HTTP Strict Transport Security) - Force HTTPS for 1 year
        if (app()->environment('production')) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }
}
