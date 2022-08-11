<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInviteRequest;
use App\Http\Resources\InviteResource;
use App\Models\Invite;
use App\Models\Project;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Throwable;

class InviteController extends Controller
{
    /**
     * List all received invitations
     *
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $invites = Invite::query();
        $empty = true;
        if ($request->input('received')) {
            $invites->orWhere('receiver_id', $request->user()->id);
            $empty = false;
        } 
        if ($request->input('sent')) {
            $invites->orWhere('user_id', $request->user()->id);
            $empty = false;
        }
        
        // default fallback behavior, if no inputs are specified
        if($empty) {
            $invites->orWhere('receiver_id', $request->user()->id);
        }
        return InviteResource::collection($invites->paginate());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreInviteRequest $request
     * @param Project $project
     * @return Response
     * @throws AuthorizationException
     */
    public function store(StoreInviteRequest $request, Project $project): Response
    {
        $this->authorize('create_invite', $project);
        $invite = new Invite;
        $invite->fill($request->all());
        $invite->user_id = $request->user()->id;
        $invite->project()->associate($project);
        $invite->save();
        return response('OK');
    }

    /**
     * Display the specified resource.
     *
     * @param Invite $invite
     * @return InviteResource
     * @throws AuthorizationException
     */
    public function show(Invite $invite): InviteResource
    {
        $this->authorize('view', $invite);
        return new InviteResource($invite);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Invite $invite
     * @return Response
     * @throws Throwable
     */
    public function destroy(Invite $invite): Response
    {
        $this->authorize('delete', $invite);
        $invite->deleteOrFail();
        return response('OK');
    }

    /**
     * Accept existing invite.
     * 
     * @param Invite $invite
     * @return Response
     * @throws AuthorizationException
     */
    public function accept(Invite $invite): Response
    {
        $this->authorize('change_status', $invite);
        
        $invite->accepted = true;
        $invite->save();
        
        // send acceptance notification
        
        return response('OK');
    }

    /**
     * Reject existing invite.
     *
     * @param Invite $invite
     * @return Response
     * @throws AuthorizationException
     */
    public function reject(Invite $invite): Response
    {
        $this->authorize('change_status', $invite);

        $invite->accepted = false;
        $invite->save();
        
        // send rejection notification

        return response('OK');
    }
}
