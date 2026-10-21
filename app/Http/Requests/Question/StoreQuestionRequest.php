<?php

namespace App\Http\Requests\Question;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class StoreQuestionRequest extends BaseFormRequest
{
    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if (! $this->has('questions')) {
            $questionInput = $this->input('quiztions')
                ?? $this->input('question')
                ?? $this->input('area_questions')
                ?? $this->input('area_quiztions');

            if ($questionInput !== null) {
                $this->merge(['questions' => $questionInput]);
            }
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'questions' => ['required', 'string'],
        ];
    }
}
