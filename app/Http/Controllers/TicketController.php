<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Http\Resources\TicketResource;
use App\Models\Label;
use App\Models\Project;
use App\Models\Status;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Throwable;

class TicketController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Ticket::class, 'ticket');
    }

    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection|Response
     */
    public function index(Project $project): Response|AnonymousResourceCollection
    {
        return TicketResource::collection($project->tickets()->paginate());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreTicketRequest $request
     * @return Response
     */
    public function store(StoreTicketRequest $request, Project $project)
    {
        $ticket = new Ticket();
        $ticket->fill($request->all());
        $ticket->number = Ticket::where('project_id', $project->id)
                ->max('number') + 1;
        $ticket->author()->associate($request->user());
        $ticket->project()->associate($project);

        // status-id
        $ticket->status()->associate(Status::find(1));

        foreach ($request->input('label_ids') as $id) {
            $ticket->labels()->attach(Label::find($id));
        }
        foreach ($request->input('assignee_ids') as $id) {
            $ticket->assignees()->attach(User::find($id));
        }
        $ticket->save();

        // ----------------------
        // automatic updates here
        // ----------------------

        return response('OK');
    }

    /**
     * Display the specified resource.
     *
     * @param Ticket $ticket
     * @return TicketResource|Response
     */
    public function show(Ticket $ticket): Response|TicketResource
    {
        return new TicketResource($ticket);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateTicketRequest $request
     * @param Ticket $ticket
     * @return Response
     */
    public function update(UpdateTicketRequest $request, Ticket $ticket): Response
    {
        $ticket->fill($request->all());

        if ($request->has('status_id')) {
            $ticket->status()->associate(Status::find($request->input('status_id')));
        }

        if ($request->has('label_ids')) {
            $ticket->labels()->detach();
            foreach ($request->input('label_ids') as $id) {
                $ticket->labels()->attach(Label::find($id));
            }
        }
        if ($request->has('assignee_ids')) {
            $ticket->assignees()->detach();
            foreach ($request->input('assignee_ids') as $id) {
                $ticket->assignees()->attach(User::find($id));
            }
        }
        $ticket->save();
        
        return response('OK');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Ticket $ticket
     * @return Response
     * @throws Throwable
     */
    public function destroy(Ticket $ticket): Response
    {
        $ticket->deleteOrFail();
        return response('OK');
    }

    /**
     * Subscribe to the ticket notifications.
     * 
     * @throws AuthorizationException
     */
    public function subscribe(Request $request, Ticket $ticket): Response|Application|ResponseFactory
    {
        $this->authorize('subscribe', $ticket);
        
        $ticket->subscribers()->attach($request->user());
        
        return response('OK');
    }

    /**
     * Unsubscribe current user from ticket notifications.
     * 
     * @throws AuthorizationException
     */
    public function unsubscribe(Request $request, Ticket $ticket): Response|Application|ResponseFactory
    {

        $this->authorize('unsubscribe', $ticket);

        $ticket->subscribers()->detach($request->user());

        return response('OK');
    }
}
