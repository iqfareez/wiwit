<?php

use Illuminate\Support\Facades\Route;

it('returns problem details for unexpected API failures', function () {
    Route::get('/api/v1/test-error', fn () => throw new RuntimeException('secret internals'));

    $this->getJson('/api/v1/test-error')
        ->assertStatus(500)
        ->assertJsonPath('type', '/problems/internal-server-error')
        ->assertJsonMissing(['secret internals']);
});
