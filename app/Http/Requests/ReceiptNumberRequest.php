<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

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
                    ? "unique:receipt_numbers,receipt_number," . $this->route('receipt_number') . ",id,store_id," . $this->input('store_id')
                    : "unique:receipt_numbers,receipt_number,NULL,id,store_id," . $this->input('store_id'),
            ],
           "contact_details" => [
            "required",
            "regex:/^\+63\d{10}$/",
            "unique:receipt_numbers,contact_details," . $this->route('receipt_number'), // Adjust to match route parameter name
        ],
            "store_id" => ["required", "exists:store_names,id"],
        ];
        
    }

    public function messages()
    {
        return [
            "contact_details.regex" => "The mobile number field format is invalid.",
        ];
    }
}
