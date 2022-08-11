<?php

namespace App\Http\Controllers;

use App\Events\NewUpdateCreated;
use App\Http\Requests\SearchTicketRequest;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Http\Resources\TicketResource;
use App\Http\Resources\UpdateResource;
use App\Models\Label;
use App\Models\Priority;
use App\Models\Project;
use App\Models\Status;
use App\Models\Ticket;
use App\Models\TicketChanges\AssigneeChange;
use App\Models\TicketChanges\Comment;
use App\Models\TicketChanges\LabelChange;
use App\Models\TicketChanges\PriorityChange;
use App\Models\TicketChanges\StatusChange;
use App\Models\TicketChanges\TitleChange;
use App\Models\Update;
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
            $data->get('order_by')->each(function ($item, $key) use ($data, $request, &$order) {
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
            $label = Label::find($id);
            $ticket->labels()->attach($label);
            
            $change = new LabelChange();
            $change->label()->associate($label);
            $change->is_added = true;
            $change->save();
            
            $update = new Update();
            $update->user()->associate($request->user());
            $update->ticket()->associate($ticket);
            $update->changeable()->associate($change);
            $update->save();
        }
        foreach ($request->input('assignee_ids') as $id) {
            $ticket->assignees()->attach(User::find($id));
        }
        $ticket->save();

        $comment = new Comment();
        $comment->body = $request->input('message');
        $comment->save();
        
        $this->create_update($comment, $request->user(), $ticket);

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
     * Shows paginated updates for a specific ticket.
     * 
     * @param Ticket $ticket
     * @return AnonymousResourceCollection
     * @throws AuthorizationException
     */
    public function showUpdates(Ticket $ticket): AnonymousResourceCollection
    {
        $this->authorize('view', $ticket);
        return UpdateResource::collection($ticket->updates()->latest()->paginate());
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
            $title_change = new TitleChange();
            $title_change->old = $ticket->name;
            $title_change->new = $request->input('name');
            $title_change->save();
            
            $this->create_update($title_change, $request->user(), $ticket);
            
            $ticket->name = $request->input('name');
        }

        if ($request->has('status_id')) {
            $status_change = new StatusChange();
            $status_change->oldStatus()->associate($ticket->status);
            $status_change->newStatus()->associate(Status::find($request->input('status_id')));
            $status_change->save();

            $this->create_update($status_change, $request->user(), $ticket);
            
            $ticket->status()->associate(Status::find($request->input('status_id')));
        }

        if ($request->has('priority_id')) {
            $priority_change = new PriorityChange();
            $priority_change->oldPriority()->associate($ticket->priority);
            $priority_change->newPriority()->associate(Priority::find($request->input('priority_id')));
            $priority_change->save();
            
            $this->create_update($priority_change, $request->user(), $ticket);
            
            $ticket->priority()->associate(Priority::find($request->input('priority_id')));
        }

        if ($request->has('label_ids')) {
            $labels = Label::whereIn('id', $request->input('label_ids'))->get();
            $added = $labels->diff($ticket->labels()->get());
            $removed = $ticket->labels()->get()->diff($labels);

            $added->each(function ($item, $key) use ($request, $ticket) {
                $ticket->labels()->attach($item);
                
                $change = new LabelChange();
                $change->label()->associate($item);
                $change->is_added = true;
                $change->save();
                
                $this->create_update($change, $request->user(), $ticket);
            });

            $removed->each(function ($item, $key) use ($request, $ticket) {
                $ticket->labels()->detach($item);
                
                $change = new LabelChange();
                $change->label()->associate($item);
                $change->is_added = false;
                $change->save();
                
                $this->create_update($change, $request->user(), $ticket);
            });
        }
        
        if ($request->has('assignee_ids')) {
            $assignees = User::whereIn('id', $request->input('assignee_ids'))->get();
            $added = $assignees->diff($ticket->assignees()->get());
            $removed = $ticket->assignees()->get()->diff($assignees);
            
            $added->each(function ($item, $key) use ($request, $ticket) {
                $ticket->assignees()->attach($item);
                
                $change = new AssigneeChange();
                $change->assignee()->associate($item);
                $change->is_added = true;
                $change->save();
                
                $this->create_update($change, $request->user(), $ticket);
            });

            $removed->each(function ($item, $key) use ($request, $ticket) {
                $ticket->assignees()->detach($item);

                $change = new AssigneeChange();
                $change->assignee()->associate($item);
                $change->is_added = false;
                $change->save();

                $this->create_update($change, $request->user(), $ticket);
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

    /**
     * Helper method, generates an Update and dispatches an update event.
     * 
     * @param $changeable
     * @param $user
     * @param $ticket
     * @return void
     */
    private function create_update($changeable, $user, $ticket): void
    {
        $update = new Update();
        $update->user()->associate($user);
        $update->ticket()->associate($ticket);
        $update->changeable()->associate($changeable);
        $update->save();

        NewUpdateCreated::dispatch($update);
    }
}
