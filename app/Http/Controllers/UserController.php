<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends Controller
{
    /**
     * Create a new instance of the controller
     */
    public function __construct()
    {

        $this->authorizeResource(User::class, 'user');
    }

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
     * @param Request $request
     * @return Response
     */
    public function update(Request $request): Response
    {
        $user = $request->user();
        $user->fill($request->all());
        $user->save();
        return response('OK');
    }
}
