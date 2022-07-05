<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display information about the currently logged-in user.
     * 
     * @param Request $request
     * @return UserResource
     */
    public function self(Request $request): UserResource
    {
        return new UserResource($request->user());
    }
    
    /**
     * Display the specified resource.
     *
     * @param User $user
     * @return UserResource|Response
     */
    public function show(User $user): Response|UserResource
    {
        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateUserRequest $request
     * @return Response
     * @throws AuthorizationException
     */
    public function update(UpdateUserRequest $request): Response
    {
        $user = $request->user();
        $user->fill($request->all());
        $user->save();
        return response('OK');
    }

    /**
     * @param Request $request
     * @return Response|Application|ResponseFactory
     */
    public function destroy(Request $request): Response|Application|ResponseFactory
    {
        $user = $request->user();
        Auth::logout();
        $user->deleteOrFail();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response('OK');
    }
}
