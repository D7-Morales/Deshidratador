<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateManual
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->session()->has('id_usuario')) {
            return redirect()->route('login.form')->withErrors([
                'auth' => 'Debes iniciar sesión para acceder al sistema.'
            ]);
        }

        return $next($request);
    }
}
