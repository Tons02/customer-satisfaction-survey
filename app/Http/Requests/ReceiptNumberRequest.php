<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Carbon\Carbon;

class ReceiptNumberRequest extends FormRequest
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
                "required",
                "string",
                $this->route('receipt_number')
                    ? "unique:receipt_numbers,receipt_number," . $this->route('receipt_number') . ",id,store_id," . auth('sanctum')->user()->store_id
                    : "unique:receipt_numbers,receipt_number,NULL,id,store_id," . auth('sanctum')->user()->store_id,
            ],
        // validation for all number
        //    "contact_details" => [
        //     "required",
        //     "regex:/^\+63\d{10}$/",
        //     "unique:receipt_numbers,contact_details," . $this->route('receipt_number'), // Adjust to match route parameter name
        // ],
           "contact_details" => [
            "required",
            "regex:/^\+63\d{10}$/",
            Rule::unique('receipt_numbers')->where(function ($query) {
                $query->where('is_valid', true) // only valid receipt numbers
                      ->where('expiration_date', '>', Carbon::now()); // Ensure expiration_date is greater than today
            })->ignore($this->route('receipt_number')),
        ]
        ];

    }

    public function messages()
    {
        return [
            "contact_details.regex" => "The mobile number field format is invalid.",
            "contact_details.unique" => "User has already been selected.",
        ];
    }
}
