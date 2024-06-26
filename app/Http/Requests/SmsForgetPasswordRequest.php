<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SmsForgetPasswordRequest extends FormRequest
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
            "mobile_number" => [
                "regex:/^\+63\d{10}$/",
                "exists:users,contact_details"
            ],
        ];
    }
    public function messages()
    {
        return [
            "mobile_number.exists" => "The mobile number is not associated with any users.",
        ];
    }

}
