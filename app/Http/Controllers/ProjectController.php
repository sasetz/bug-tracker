<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\UserResource;
use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Throwable;

class ProjectController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Project::class, 'project');
    }

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
    public function store(StoreProjectRequest $request): Response
    {
        $project = Project::create($request->all());
        $project->owner()->associate($request->user());
        $project->users()->attach($request->user());
        $project->users->find($request->user())->pivot->is_admin = 1;
        $project->save();
        return response('OK');
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
        return response('OK');
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
        return response('OK');
    }

    /**
     * List all users in the project
     * 
     * @param Project $project
     * @return AnonymousResourceCollection
     * @throws Throwable
     */
    public function users(Project $project): AnonymousResourceCollection
    {
        return UserResource::collection($project->users->get()->concat($project->owner()->get()));
    }

    /**
     * Make a user admin
     * 
     * @param Project $project
     * @param User $user
     * @return Response
     * @throws AuthorizationException
     */
    public function makeAdmin(Project $project, User $user): Response
    {
        $this->authorize('update', $project);
        
        if(!$user->isAdded($project)) {
            return response('', 403);
        }
        
        $pivot = $project->users->find($user)->pivot;
        $pivot->is_admin = 1;
        $pivot->save();
        
        return response('OK');
    }
}