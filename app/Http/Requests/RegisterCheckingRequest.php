<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterCheckingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
    
    public function validationData()
    {
        return $this->route()->parameters();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'mobile_number' => [
                'required',
                'regex:/^\+63\d{10}$/',
                'not_regex:/\//', 
            ],
            'receipt_number' => [
                'required',
                'string',
            ],
        ];
    }

    public function messages()
    {
        return [
            'mobile_number.required' => 'The mobile number is required.',
            'mobile_number.regex' => 'The mobile number must start with +63 and be followed by 10 digits.',
            'mobile_number.not_regex' => 'The mobile number cannot contain a forward slash (/).',
            'receipt_number.required' => 'The entry code is required.',
            'receipt_number.string' => 'The entry code must be a string.',
        ];
    }
}
