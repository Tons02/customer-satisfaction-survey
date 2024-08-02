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
                "sometimes:required",
                "required",
            ],
            "first_name" => [
                "sometimes:required",
                "required",
            ],
            "last_name" => [
                "sometimes:required",
                "required",
            ],
            "mobile_number" => [
                "sometimes:required",
                "regex:/^\+63\d{10}$/"
            ],
            "mobile_number_verified" => [
                "sometimes:required",
                'required',
                'in:1'
            ],
            "gender" => [
                "sometimes:required",
                'required',
                'in:male,female',
            ],
            "birthday" => [
                "sometimes:required",
                'required',
                'date_format:Y-m-d',
            ],
            "claim_by_user_id" => [
                "sometimes:required",
                "required",
            ]
        ];
    }
}
