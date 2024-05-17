<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuestionnaireRequest extends FormRequest
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
            //form
            "title" => [
                "required",
            ],
            "sections" => [
                "required",
            ],
            
        ];
    }

    public function messages()
    {
        return [
            "sections.name.required" => "The section title field is required.",

        ];
    }
}
