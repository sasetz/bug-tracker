<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLabelRequest;
use App\Http\Requests\UpdateLabelRequest;
use App\Http\Resources\LabelResource;
use App\Models\Label;
use App\Models\Project;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Throwable;

class LabelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Project $project
     * @return AnonymousResourceCollection
     */
    public function index(Project $project): AnonymousResourceCollection
    {
        return LabelResource::collection($project->labels()->paginate());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreLabelRequest $request
     * @param Project $project
     * @return Response
     * @throws AuthorizationException
     */
    public function store(StoreLabelRequest $request, Project $project): Response
    {
        $this->authorize('update', $project);
        $label = new Label();
        $label->project()->associate($project);
        $label->fill($request->all());
        $label->save();
        
        return response('OK');
    }

    /**
     * Display the specified resource.
     *
     * @param Label $label
     * @return LabelResource|Response
     * @throws AuthorizationException
     */
    public function show(Label $label): Response|LabelResource
    {
        $this->authorize('view', $label);
        return new LabelResource($label);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateLabelRequest $request
     * @param Label $label
     * @return Response
     * @throws AuthorizationException
     */
    public function update(UpdateLabelRequest $request, Label $label): Response
    {
        $this->authorize('update', $label);
        $label->fill($request->all());
        $label->save();
        
        return response('OK');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Label $label
     * @return Response
     * @throws Throwable
     */
    public function destroy(Label $label): Response
    {
        $this->authorize('delete', $label);
        $label->deleteOrFail();
        
        return response('OK');
    }
}
