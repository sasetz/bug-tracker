<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchTicketRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'title' => 'string',

            'author_ids' => 'array',
            'author_ids.*' => 'exists:users,id',
            
            'assignee_ids' => 'array',
            'assignee_ids.*' => 'exists:users,id',
            
            'label_ids' => 'array',
            'label_ids.*' => 'exists:labels,id',
            
            'priority_ids' => 'array',
            'priority_ids.*' => 'exists:priorities,id',
            
            'status_ids' => 'array',
            'status_ids.*' => 'exists:statuses,id',

            'order_by' => 'array',
            'order_by.*' => 'string',

            'direction' => 'array',
            'direction.*' => 'boolean',
        ];
    }
}
