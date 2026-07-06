<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

// Use to debug if reverse proxy setup is working
// More info: https://github.com/mptwaktusolat/api-waktusolat-x/tree/main/docs/deployments/docker-compose.md#reverse-proxy
Route::get('/_debug/proxy-headers', function (Request $request) {
    return [
        'is_secure' => $request->isSecure(),
        'scheme' => $request->getScheme(),
        'forwarded_proto' => $request->header('X-Forwarded-Proto'),
        'forwarded_for' => $request->header('X-Forwarded-For'),
        'url' => $request->url(),
    ];
});
