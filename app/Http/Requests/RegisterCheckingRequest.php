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
            'entry_code' => [
                'required',
                'string',
                'max:10',
                'not_regex:/\//', // Ensures the input does not contain a forward slash
            ],
            'first_name' => [
                'required',
                'string',
                'max:50',
                'not_regex:/\//', // Ensures the input does not contain a forward slash
            ],
            'last_name' => [
                'nullable',
                'string',
                'max:50',
                'not_regex:/\//', // Ensures the input does not contain a forward slash
            ],
        ];
    }

    public function messages()
    {
        return [
            'mobile_number.required' => 'The mobile number is required.',
            'mobile_number.regex' => 'The mobile number must start with +63 and be followed by 10 digits.',
            'mobile_number.not_regex' => 'The mobile number cannot contain a forward slash (/).',
            'entry_code.required' => 'The entry code is required.',
            'entry_code.string' => 'The entry code must be a string.',
            'entry_code.not_regex' => 'The entry code cannot contain a forward slash (/).',
            'first_name.required' => 'The first name is required.',
            'first_name.string' => 'The first name must be a string.',
            'first_name.not_regex' => 'The first name cannot contain a forward slash (/).',
            'last_name.string' => 'The last name must be a string if provided.',
            'last_name.not_regex' => 'The last name cannot contain a forward slash (/).',
        ];
    }
}
