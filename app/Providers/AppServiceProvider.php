<?php

namespace App\Providers;

use App\Models\Diary;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        JsonResource::withoutWrapping();

        Vite::prefetch(concurrency: 3);

        Route::bind('diary', function (string $value) {
            return Diary::where('date', $value)->firstOrFail();
        });
        Route::pattern('date', '\d{4}-\d{2}-\d{2}');
        Route::pattern('diary', '\d{4}-\d{2}-\d{2}');
    }
}
