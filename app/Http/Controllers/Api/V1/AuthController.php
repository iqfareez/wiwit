<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

/**
 * @group Authentication
 *
 * Authenticate the API
 *
 * @unauthenticated
 */
class AuthController extends Controller
{
    private const ABILITIES = ['read', 'create', 'update', 'delete'];

    /**
     * Get Token
     *
     * Obtain a bearer token using email and password credentials.
     *
     * @return JsonResponse
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string'],
            'device_name' => ['required', 'string', 'max:255'],
        ]);
        $email = Str::lower($validated['email']);

        if (! Auth::validate(['email' => $email, 'password' => $validated['password']])) {
            abort(401);
        }

        RateLimiter::clear(Str::transliterate($email.'|'.$request->ip()));
        $user = User::where('email', $email)->firstOrFail();

        return $this->tokenResponse($user, $validated['device_name']);
    }

    private function tokenResponse(User $user, string $deviceName)
    {
        $token = $user->createToken($deviceName, self::ABILITIES);

        return response()->json([
            'token' => $token->plainTextToken,
            'token_type' => 'Bearer',
            'abilities' => self::ABILITIES,
            'expires_at' => null,
        ]);
    }
}
