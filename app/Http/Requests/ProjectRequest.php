<?php

namespace App\Http\Requests;

use App\Enums\Priority;
use App\Enums\Status;
use App\Rules\AllowedStateTransition;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // TODO: Implement proper authorization logic.
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'priority' => ['required', Rule::enum(Priority::class)],
            'current_status' => [
                'required',
                Rule::enum(Status::class),
                new AllowedStateTransition(
                    Status::class,
                    $this->project?->current_status,
                    'This project cannot be moved from :old to :new.',
                ),
            ],
            'current_progress_percentage' => ['required', 'integer', 'min:0', 'max:100'],
            'start_date' => ['nullable', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:due_date'],
            'parent_id' => ['nullable', 'exists:projects,id'],
            'reporter_user_id' => ['exists:users,id'],
            'assigned_user_id' => ['nullable', 'exists:users,id'],
            'tags' => ['nullable', 'array', 'min:1'],
            'tags.*' => ['string', 'max:255', 'distinct'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages()
    {
        return [
            'tags' => 'Select at least one tag.',
            'tags.*.max' => 'The tag #:position must not be greater than 255 characters.',
        ];
    }
}
