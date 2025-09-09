<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SalesAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        // Se autenticato ma non Admin, blocca l’accesso
        if (!in_array(Auth::user()->role, ['Sales', 'Admin'])) {
            abort(403, 'Accesso negato');
        }

        return $next($request);
    }
}
