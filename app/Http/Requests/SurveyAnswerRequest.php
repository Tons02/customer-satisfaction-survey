<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SurveyAnswerRequest extends FormRequest
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
            "entry_code" => [
                "required",
            ],
            "first_name" => [
                "required",
            ],
            "last_name" => [
                "required",
            ],
            "mobile_number" => [
                "regex:/^\+63\d{10}$/"
            ],
            "mobile_number_verified" => [
                'required',
                'in:1'
            ],
            "gender" => [
                'required',
                'in:male,female',
            ],
            "age" => [
                'required',
            ],
        ];
    }
}
