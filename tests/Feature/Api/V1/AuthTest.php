<?php

use App\Models\User;

it('logs in and issues a full ability bearer token', function () {
    $user = User::factory()->create([
        'email' => 'demo-user@example.com',
        'two_factor_secret' => 'ignored',
        'two_factor_recovery_codes' => 'ignored',
        'two_factor_confirmed_at' => now(),
    ]);

    $response = $this->postJson('/api/v1/auth/login', [
        'email' => strtoupper($user->email),
        'password' => 'password',
        'device_name' => 'Budget app',
    ])->assertOk()
        ->assertJsonPath('token_type', 'Bearer')
        ->assertJsonPath('abilities', ['read', 'create', 'update', 'delete'])
        ->assertJsonPath('expires_at', null);

    $token = $response->json('token');

    expect($token)->toBeString();
    expect($user->tokens()->sole()->name)->toBe('Budget app');
});

it('returns generic credentials errors and throttles login attempts', function () {
    User::factory()->create(['email' => 'known@example.com']);

    foreach (range(1, 5) as $attempt) {
        $this->postJson('/api/v1/auth/login', [
            'email' => 'known@example.com',
            'password' => 'wrong-password',
            'device_name' => 'Test',
        ])->assertStatus(401)
            ->assertHeader('Content-Type', 'application/problem+json')
            ->assertJsonPath('type', '/problems/unauthenticated');
    }

    $this->postJson('/api/v1/auth/login', [
        'email' => 'known@example.com',
        'password' => 'wrong-password',
        'device_name' => 'Test',
    ])->assertTooManyRequests()
        ->assertJsonPath('type', '/problems/too-many-requests');
});
