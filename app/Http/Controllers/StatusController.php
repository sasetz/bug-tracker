<?php

namespace App\Http\Controllers;

use App\Http\Resources\StatusResource;
use App\Models\Status;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class StatusController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection|Response
     */
    public function index(): Response|AnonymousResourceCollection
    {
        return StatusResource::collection(Status::paginate());
    }

    /**
     * Display the specified resource.
     *
     * @param Status $status
     * @return StatusResource|Response
     */
    public function show(Status $status): Response|StatusResource
    {
        return new StatusResource($status);
    }
    
}