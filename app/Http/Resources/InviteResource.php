<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class InviteResource extends JsonResource
{
    public static $wrap = '';
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id'            => $this->id,
            'sender'        => new UserResource($this->user),
            'receiver'      => new UserResource($this->receiver),
            'project'       => new ProjectResource($this->project),
            'accepted'      => $this->accepted,
        ];
    }
}
