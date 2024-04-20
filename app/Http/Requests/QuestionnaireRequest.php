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
            "form.title" => [
                "required",
            ],

            //section
            "section.id" => [
                "required",
            ],
            "section.section" => [
                "required",
            ],
            "section.name" => [
                "required",
            ],

            //question
            "section.question.id" => [
                "required",
            ],
            "section.question.question" => [
                "required",
            ],
            "section.question.type" => [
                "required",
            ],
            "section.question.required" => [
                "required",
            ],
        ];
    }

    public function messages()
    {
        return [
            "form.title.required" => "The form title field is required.",

            "section.id.required" => "The section id is required.",
            "section.section.required" => "The section is required.",
            "section.name.required" => "The section name is required.",

            
            "section.question.id.required" => "The question id is required.",
            "section.question.question.required" => "The question is required.",
            "section.question.type.required" => "The question type is required.",
            "section.question.required.required" => "The question required is required.",
        ];
    }
}
