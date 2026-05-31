<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Kelas;
use App\Models\User;

class CheckKelasAccess
{
    public function handle(Request $request, Closure $next)
    {
        $kelasId = $request->route('id') ?? $request->route('kelas');
        
        if (!$kelasId) {
            return redirect()->back()->with('error', 'Kelas tidak ditemukan');
        }

        $kelas = Kelas::with('users')->find($kelasId);
        
        if (!$kelas) {
            return redirect()->back()->with('error', 'Kelas tidak ditemukan');
        }

        // If kelas is private, check if user is in the class
        if ($kelas->isPrivate()) {
            if (!auth()->check()) {
                return redirect()->route('login')->with('error', 'Silakan login untuk mengakses kelas ini');
            }

            $user = auth()->user();
            if (!$kelas->users->contains($user->id) && !$user->hasRole('administrator')) {
                return redirect()->back()->with('error', 'Anda tidak memiliki akses ke kelas ini');
            }
        }

        return $next($request);
    }
}
