<?php

namespace App\Http\Controllers;

use App\Http\Resources\UpdateResource;
use App\Models\Ticket;
use App\Models\Update;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class UpdateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Ticket $ticket
     * @return AnonymousResourceCollection|Response
     */
    public function index(Ticket $ticket): Response|AnonymousResourceCollection
    {
        return UpdateResource::collection($ticket->updates()->get());
    }

    /**
     * Display the specified resource.
     *
     * @param Update $update
     * @return UpdateResource|Response
     */
    public function show(Update $update): Response|UpdateResource
    {
        return new UpdateResource($update);
    }
}
