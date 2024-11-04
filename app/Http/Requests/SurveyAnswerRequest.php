<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
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
            "receipt_number" => [
                "sometimes:required",
                "required",
                Rule::exists('receipt_numbers', 'receipt_number')->where(function ($query) {
                    $query->where('is_valid', true);
                })
            ],
            "store_id" => [
                "sometimes:required",
                "required",
                "exists:store_names,id"
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
                "regex:/^\+63\d{10}$/",
                Rule::exists('receipt_numbers', 'contact_details')->where(function ($query) {
                    $query->where('is_valid', true);
                })
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
                "exists:users,id"
            ]
        ];
    }
}
