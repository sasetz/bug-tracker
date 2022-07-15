<?php

namespace App\Http\Controllers;

use App\Events\AssigneeChanged;
use App\Events\CommentPosted;
use App\Events\LabelChanged;
use App\Events\PriorityChanged;
use App\Events\StatusChanged;
use App\Events\TitleChanged;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Http\Resources\TicketResource;
use App\Models\Comment;
use App\Models\Label;
use App\Models\Priority;
use App\Models\PriorityChange;
use App\Models\Project;
use App\Models\Status;
use App\Models\StatusChange;
use App\Models\Ticket;
use App\Models\TitleChange;
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
     * @param Project $project
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
     * @param Project $project
     * @return Response
     */
    public function store(StoreTicketRequest $request, Project $project): Response
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

        $comment = new Comment();
        $comment->user()->associate($request->user());
        $comment->ticket()->associate($ticket);
        $comment->body = $request->input('message');
        $comment->save();
        
        CommentPosted::dispatch($comment);

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
        if($request->has('name')) {
            $title_update = new TitleChange();
            $title_update->user()->associate($request->user());
            $title_update->ticket()->associate($ticket);
            $title_update->old = $ticket->name;
            $title_update->new = $request->input('name');
            $title_update->save();
            
            $ticket->name = $request->input('name');
            
            TitleChanged::dispatch($title_update);
        }
        
        if ($request->has('status_id')) {
            $status_update = new StatusChange();
            $status_update->ticket()->associate($ticket);
            $status_update->user()->associate($request->user());
            $status_update->oldStatus()->associate($ticket->status);
            $status_update->newStatus()->associate(Status::find($request->input('status_id')));
            $status_update->save();
            
            StatusChanged::dispatch($status_update);
            
            $ticket->status()->associate(Status::find($request->input('status_id')));
        }
        
        if($request->has('priority_id')) {
            $priority_update = new PriorityChange();
            $priority_update->ticket()->associate($ticket);
            $priority_update->user()->associate($request->user());
            $priority_update->oldPriority()->associate($ticket->priority);
            $priority_update->newPriority()->associate(Priority::find($request->input('priority_id')));
            $priority_update->save();

            PriorityChanged::dispatch($priority_update);

            $ticket->priority()->associate(Priority::find($request->input('priority_id')));
        }

        if ($request->has('label_ids')) {
            $labels = Label::whereIn('id', $request->input('label_ids'))->get();
            $added = $labels->diff($ticket->labels()->get());
            $removed = $ticket->labels()->get()->diff($labels);
            
            $added->each(function ($item, $key) use ($ticket) {
                $ticket->labels()->attach($item);
                LabelChanged::dispatch($item, true);
            });
            
            $removed->each(function ($item, $key) use ($ticket) {
                $ticket->labels()->detach($item);
                LabelChanged::dispatch($item, false);
            });
        }
        if ($request->has('assignee_ids')) {
            $assignees = User::whereIn('id', $request->input('assignee_ids'))->get();
            $added = $assignees->diff($ticket->assignees()->get());
            $removed = $ticket->assignees()->get()->diff($assignees);

            $added->each(function ($item, $key) use ($ticket) {
                $ticket->assignees()->attach($item);
                AssigneeChanged::dispatch($item, true);
            });

            $removed->each(function ($item, $key) use ($ticket) {
                $ticket->assignees()->detach($item);
                AssigneeChanged::dispatch($item, false);
            });
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
