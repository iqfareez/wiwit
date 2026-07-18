<?php

namespace App\Providers;

use App\Models\User;
use Filament\Support\Facades\FilamentTimezone;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Knuckles\Camel\Extraction\ExtractedEndpointData;
use Knuckles\Scribe\Scribe;

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
        FilamentTimezone::set(config('app.timezone'));

        if (class_exists(Scribe::class)) {
            Scribe::beforeResponseCall(function (Request $request, ExtractedEndpointData $endpointData) {
                $abilities = ['read', 'create', 'update', 'delete'];

                // create temporary user
                $user = User::factory()->create();
                $token = $user->createToken('scribe_temporary', $abilities);
                $request->headers->add(['Authorization' => "Bearer {$token->plainTextToken}"]);
            });
        }
    }
}
