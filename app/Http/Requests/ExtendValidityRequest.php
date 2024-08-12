<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExtendValidityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
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
            'survey_ids' => ['required', 'array'],
            'survey_ids.*' => ['required', 'integer', 'exists:survey_answers,id'],
            'extend_date' => ['required', 'date_format:Y-m-d H:i:s'],
        ];
    }

    public function messages()
    {
        return [
            'survey_ids.*.exists' => 'The selected survey ID is invalid.',
        ];
    }

}
