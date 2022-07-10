<?php

namespace App\Http\Requests;

use App\Rules\InProject;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTicketRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'string|max:255',
            'label_ids' => 'array',
            'label_ids.*' => 'exists:labels,id',
            'assignee_ids' => 'array',
            'assignee_ids.*' => ['exists:users,id', new InProject($this->route('ticket')->project)],
            'status_id' => 'exists:statuses,id',
            'priority_id' => 'exists:priorities,id',
        ];
    }
}
