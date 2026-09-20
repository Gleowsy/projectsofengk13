<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class VercelServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Cek apakah aplikasi berjalan di Vercel (mendeteksi variabel VERCEL)
        if (env('VERCEL')) {
            $this->app->useStoragePath(env('APP_STORAGE', '/tmp/storage'));
        }
    }

    public function boot()
    {
        //
    }
}