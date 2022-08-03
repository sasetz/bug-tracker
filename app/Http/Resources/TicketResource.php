<?php

namespace App\Http\Resources;

use App\Models\Status;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class TicketResource extends JsonResource
{
    public static $wrap = '';
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'name' => $this->name,
            'number' => $this->number,
            'author' => new UserResource($this->author),
            'project_id' => $this->project_id,
            'priority' => new PriorityResource($this->priority),
            'status' => new StatusResource($this->status),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
