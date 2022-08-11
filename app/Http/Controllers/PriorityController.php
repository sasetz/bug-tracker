<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePriorityRequest;
use App\Http\Requests\UpdatePriorityRequest;
use App\Http\Resources\PriorityResource;
use App\Models\Priority;
use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Throwable;

class PriorityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Project $project
     * @return AnonymousResourceCollection|Response
     */
    public function index(Project $project): Response|AnonymousResourceCollection
    {
        return PriorityResource::collection($project->priorities);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StorePriorityRequest $request
     * @param Project $project
     * @return Response
     * @throws AuthorizationException
     */
    public function store(StorePriorityRequest $request, Project $project): Response
    {
        $this->authorize('update', $project);
        
        $priority = new Priority();
        $priority->project()->associate($project);
        $priority->fill($request->all());
        $priority->save();
        
        return response('OK');
    }

    /**
     * Display the specified resource.
     *
     * @param Priority $priority
     * @return PriorityResource|Response
     * @throws AuthorizationException
     */
    public function show(Priority $priority): Response|PriorityResource
    {
        $this->authorize('view', $priority);
        
        return new PriorityResource($priority);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdatePriorityRequest $request
     * @param Priority $priority
     * @return Response
     * @throws AuthorizationException
     */
    public function update(UpdatePriorityRequest $request, Priority $priority): Response
    {
        $this->authorize('update', $priority);
        $priority->fill($request->all());
        $priority->save();
        
        return response('OK');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Priority $priority
     * @return Response
     * @throws Throwable
     */
    public function destroy(Priority $priority): Response
    {
        $this->authorize('delete', $priority);
        
        $priority->deleteOrFail();
        return response('OK');
    }
}
