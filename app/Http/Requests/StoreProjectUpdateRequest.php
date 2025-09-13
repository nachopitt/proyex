<?php

namespace App\Http\Requests;

use App\Enums\Status;
use App\Rules\AllowedStateTransition;
use Illuminate\Validation\Rule;

class StoreProjectUpdateRequest extends ProjectUpdateRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $project = $this->route('project');
        return array_merge(parent::rules(), [
            'project.current_status' => [
                'required',
                Rule::enum(Status::class),
                new AllowedStateTransition(
                    Status::class,
                    $project->current_status,
                    'This project cannot be moved from :old to :new.',
                ),
            ],
            'project.current_progress_percentage' => ['required', 'integer', 'min:0', 'max:100'],
        ]);
    }
}
