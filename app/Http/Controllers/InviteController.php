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
    public function __construct()
    {
        $this->authorizeResource();
    }

    /**
     * List all received invitations
     *
     * @param Request $request
     * @return AnonymousResourceCollection
     * @throws AuthorizationException
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        if ($request->input('received') === false) {
            $invites = collect();
        } else {
            $invites = collect($request->user()->receivedInvites()->get());
        }
        if ($request->input('sent') === true) {
            $invites = $invites->concat($request->user()->sentInvites()->get());
        }
        return InviteResource::collection($invites);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreInviteRequest $request
     * @return Response
     * @throws AuthorizationException
     */
    public function store(StoreInviteRequest $request): Response
    {
        $this->authorize('create', Project::find($request->input('project_id')));
        $invite = new Invite;
        $invite->fill($request->all());
        $invite->user_id = $request->user()->id;
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
}
