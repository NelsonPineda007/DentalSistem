<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiting\Limit;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // 🛡️ ESCUDO ANTI FUERZA BRUTA PARA EL LOGIN
        RateLimiter::for('login', function (Request $request) {
            // Limita a 5 intentos por minuto basándose en la IP del usuario
            return Limit::perMinute(5)->by($request->ip());
        });
    }
}