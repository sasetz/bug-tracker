<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLabelRequest;
use App\Http\Requests\UpdateLabelRequest;
use App\Http\Resources\LabelResource;
use App\Models\Label;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Throwable;

class LabelController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Label::class, 'label');
    }

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
     * @return Response
     */
    public function store(StoreLabelRequest $request, Project $project)
    {
        $label = new Label();
        $label->project_id = $project->id;
        $label->fill($request->all());
        $label->save();
        
        return response('OK');
    }

    /**
     * Display the specified resource.
     *
     * @param Label $label
     * @return LabelResource|Response
     */
    public function show(Label $label): Response|LabelResource
    {
        return new LabelResource($label);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateLabelRequest $request
     * @param Label $label
     * @return Response
     */
    public function update(UpdateLabelRequest $request, Label $label): Response
    {
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
        $label->deleteOrFail();
        
        return response('OK');
    }
}
