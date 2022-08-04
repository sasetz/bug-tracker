<?php

namespace App\Http\Controllers;

use App\Events\AssigneeChanged;
use App\Events\CommentPosted;
use App\Events\LabelsChanged;
use App\Events\PriorityChanged;
use App\Events\StatusChanged;
use App\Events\TitleChanged;
use App\Http\Requests\SearchTicketRequest;
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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Throwable;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param SearchTicketRequest $request
     * @param Project $project
     * @return AnonymousResourceCollection|Response
     * @throws AuthorizationException
     */
    public function index(SearchTicketRequest $request, Project $project): Response|AnonymousResourceCollection
    {
        $this->authorize('view', $project);
        $data = $request->collect();

        $tickets = $project->tickets();

        // search for similar ticket titles
        if ($request->has('title')) {
            $tickets->where('title', 'like', $data->get('title'));
        }

        // for requested array authors are processed with 'or', because there can be only one author
        // to a ticket.
        if ($request->has('author_ids')) {
            $data->get('author_ids')->each(function ($item, $key) use ($tickets) {
                if ($key == 0) {
                    // start the where chain
                    $tickets->where('author_id', $item);
                } else {
                    // continue the where chain
                    $tickets->orWhere('author_id', $item);
                }
            });
        }

        // search priorities ('or')
        if ($request->has('priority_ids')) {
            $data->get('priority_ids')->each(function ($item, $key) use ($project, $tickets) {
                // skip the priority, if it's not in the project
                if(Priority::find($item)->project->isNot($project)) {
                    return;
                }
                if ($key == 0) {
                    // start the where chain
                    $tickets->where('priority_id', $item);
                } else {
                    // continue the where chain
                    $tickets->orWhere('priority_id', $item);
                }
            });
        }

        // search statuses ('or')
        if ($request->has('status_ids')) {
            $data->get('status_ids')->each(function ($item, $key) use ($tickets) {
                if ($key == 0) {
                    // start the where chain
                    $tickets->where('status_id', $item);
                } else {
                    // continue the where chain
                    $tickets->orWhere('status_id', $item);
                }
            });
        }

        // assignee ids are processed with 'or', too
        if ($request->has('assignee_ids')) {
            $data->get('assignee_ids')->each(function ($item, $key) use ($tickets, $data) {
                if ($key == 0) {
                    // start the where chain
                    // get all assignees that match user id
                    $tickets->whereHas('assignees', function (Builder $query) use ($data, $item) {
                        $query->where('user_id', $item);
                    });
                } else {
                    // continue the where chain
                    $tickets->orWhereHas('assignees', function (Builder $query) use ($data, $item) {
                        $query->where('user_id', $item);
                    });
                }
            });
        }

        // labels are processed with 'or', too
        if ($request->has('label_ids')) {
            $data->get('label_ids')->each(function ($item, $key) use ($project, $tickets, $data) {
                // skip the label, if it's not in the project
                if(Label::find($item)->project->isNot($project)) {
                    return;
                }
                if ($key == 0) {
                    // start the where chain
                    $tickets->whereHas('labels', function (Builder $query) use ($data, $item) {
                        $query->where('label_id', $item);
                    });
                } else {
                    // continue the where chain
                    $tickets->orWhereHas('labels', function (Builder $query) use ($data, $item) {
                        $query->where('label_id', $item);
                    });
                }
            });
        }

        $order = array();
        if($request->has('order_by')) {
            $data->get('order_by')->each(function ($item, $key) use ($data, $request, $order) {
                $order_field = 'created_at';

                if($item == 'author') {
                    $order_field = 'author_id';
                } else if($item == 'title') {
                    $order_field = 'name';
                } else if($item == 'status') {
                    $order_field = 'status_id';
                }

                $direction = 'desc';
                if($request->has('direction') && $data->get('direction')->get($key) !== null) {
                    $direction = $data->get('direction')->get($key) ? 'desc' : 'asc';
                }

                $order[] = [
                    'column' => $order_field,
                    'direction' => $direction,
                ];
            });
        }
        else if($request->has('direction')) {
            $order[] = [
                'column' => 'created_at',
                'direction' => $data->get('direction')->get(0) ? 'desc' : 'asc',
            ];
        }
        else {
            $order[] = [
                'column' => 'created_at',
                'direction' => 'desc',
            ];
        }

        $order = collect($order);
        $order->each(function ($item, $key) use ($tickets) {
            $tickets->orderBy($item['column'], $item['direction']);
        });

        return TicketResource::collection($tickets->paginate());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreTicketRequest $request
     * @param Project $project
     * @return Response
     * @throws AuthorizationException
     */
    public function store(StoreTicketRequest $request, Project $project): Response
    {
        $this->authorize('view', $project);
        
        $ticket = new Ticket();
        $ticket->fill($request->all());
        $ticket->number = Ticket::where('project_id', $project->id)
                ->max('number') + 1;
        $ticket->author()->associate($request->user());
        $ticket->project()->associate($project);

        // status-id
        $ticket->status()->associate(Status::find(1));
        $ticket->save();

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
     * @throws AuthorizationException
     */
    public function show(Ticket $ticket): Response|TicketResource
    {
        $this->authorize('view', $ticket);
        return new TicketResource($ticket);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateTicketRequest $request
     * @param Ticket $ticket
     * @return Response
     * @throws AuthorizationException
     */
    public function update(UpdateTicketRequest $request, Ticket $ticket): Response
    {
        $this->authorize('update', $ticket);
        
        if ($request->has('name')) {
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

        if ($request->has('priority_id')) {
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
                LabelsChanged::dispatch($item, true);
            });

            $removed->each(function ($item, $key) use ($ticket) {
                $ticket->labels()->detach($item);
                LabelsChanged::dispatch($item, false);
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
        $this->authorize('delete', $ticket);
        
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
