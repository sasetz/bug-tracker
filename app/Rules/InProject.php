<?php

namespace App\Rules;

use App\Models\Project;
use App\Models\User;
use Illuminate\Contracts\Validation\Rule;

class InProject implements Rule
{
    protected Project $project;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $user = User::find($value);
        
        return $user->isAdded($this->project);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('validation.in_project');
    }
}
