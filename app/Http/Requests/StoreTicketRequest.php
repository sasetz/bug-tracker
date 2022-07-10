<?php

namespace App\Http\Requests;

use App\Rules\InProject;
use Illuminate\Foundation\Http\FormRequest;

class StoreTicketRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'message' => 'string',
            'label_ids' => 'required|array',
            'label_ids.*' => 'exists:labels,id',
            'assignee_ids' => 'array',
            'assignee_ids.*' => ['exists:users,id', new InProject($this->route('ticket')->project)],
            'priority_id' => 'required|exists:priorities,id',
        ];
    }
}
