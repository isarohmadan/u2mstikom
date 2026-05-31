<?php

use Illuminate\Support\Facades\Auth;

if (!function_exists('isAdmin')) {
    function isAdmin() {
        return Auth::check() && Auth::user()->role === 'administrator';
    }
}

if (!function_exists('isPengurus')) {
    function isPengurus() {
        return Auth::check() && Auth::user()->hasRole('pengurus');
    }
}

if (!function_exists('isAnggota')) {
    function isAnggota() {
        return Auth::check() && Auth::user()->hasRole('anggota');
    }
} 