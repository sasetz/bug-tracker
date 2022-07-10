<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePriorityRequest;
use App\Http\Requests\UpdatePriorityRequest;
use App\Http\Resources\PriorityResource;
use App\Models\Priority;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Throwable;

class PriorityController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Priority::class, 'priority');
    }

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
     * @return Response
     */
    public function store(StorePriorityRequest $request): Response
    {
        $priority = new Priority();
        $priority->project()->associate(Project::find($request->input('project_id')));
        $priority->fill($request->all());
        $priority->save();
        
        return response('OK');
    }

    /**
     * Display the specified resource.
     *
     * @param Priority $priority
     * @return PriorityResource|Response
     */
    public function show(Priority $priority): Response|PriorityResource
    {
        return new PriorityResource($priority);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdatePriorityRequest $request
     * @param Priority $priority
     * @return Response
     */
    public function update(UpdatePriorityRequest $request, Priority $priority): Response
    {
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
        $priority->deleteOrFail();
        return response('OK');
    }
}
