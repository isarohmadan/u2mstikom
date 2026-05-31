<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();
    
        if (!$user) {
            return redirect()->route('login');
        }
    
        $allowed = match ($role) {
            'administrator' => $user->isAdministrator(),
            'pengurus' => $user->isPengurus(),
            'anggota' => $user->isAnggota(),
            default => false,
        };
    
        if (!$allowed) {
            abort(403, 'Unauthorized action.');
        }
    
        return $next($request);
    }
    
} 