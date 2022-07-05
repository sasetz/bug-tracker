<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInviteRequest;
use App\Http\Resources\InviteResource;
use App\Models\Invite;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Request;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
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
     */
    public function store(StoreInviteRequest $request): Response
    {
        $invite = new Invite;
        $invite->fill($request->all());
        $invite->user_id = $request->user()->id();
        $invite->save();
        return response('OK', SymfonyResponse::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param Invite $invite
     * @return InviteResource
     */
    public function show(Invite $invite): InviteResource
    {
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
        $invite->deleteOrFail();
        return response('OK');
    }
}
