<?php

namespace App\Http\Requests;

class StoreProjectRequest extends ProjectRequest
{
    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'reporter_user_id' => $this->user()->id,
        ]);
    }
}
