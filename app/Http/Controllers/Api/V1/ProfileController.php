<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\UserResource;
use Illuminate\Http\Request;

/**
 * @group Profile
 *
 * View your profile information
 *
 * @authenticated
 */
class ProfileController extends Controller
{
    /**
     * Show profile information for current user
     */
    public function show(Request $request): UserResource
    {
        return UserResource::make($request->user());
    }
}
