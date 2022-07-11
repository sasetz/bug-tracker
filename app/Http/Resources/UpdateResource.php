<?php

namespace App\Http\Resources;

use App\Models\UpdateType;
use Illuminate\Http\Resources\Json\JsonResource;

class UpdateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'type' => new UpdateTypeResource($this->type),
            'ticket_id' => $this->ticket_id,
            'old' => $this->when(!is_null($this->old), $this->old),
            'new' => $this->new,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
