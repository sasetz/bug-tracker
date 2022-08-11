<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UpdateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'user' => new UserResource($this->user),
            'ticket' => new TicketResource($this->ticket),
            'project' => new ProjectResource($this->ticket->project),
            'type' => $this->changeable->type, // type of the change
            'data' => $this->changeable, // automatically generate resource, nothing additional is needed
        ];
    }
}
