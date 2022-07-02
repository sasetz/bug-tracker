<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Request;
use Throwable;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        return ProjectResource::collection($request->user()->projects()->paginate());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreProjectRequest $request
     * @return Response
     */
    public function store(StoreProjectRequest $request)
    {
        $project = Project::create($request->all());
        $project->owner()->associate($request->user());
        $project->save();
        return response("", Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param Project $project
     * @return ProjectResource
     */
    public function show(Project $project): ProjectResource
    {
        return new ProjectResource($project);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateProjectRequest $request
     * @param Project $project
     * @return Response
     */
    public function update(UpdateProjectRequest $request, Project $project): Response
    {
        return response('', Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Project $project
     * @return Response
     * @throws Throwable
     */
    public function destroy(Project $project): Response
    {
        $project->deleteOrFail();
        return response('', Response::HTTP_OK);
    }
}