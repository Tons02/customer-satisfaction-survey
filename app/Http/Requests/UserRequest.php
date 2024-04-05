<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
            "first_name" => [
                "required"
            ], 
            "last_name" => [
                "required"
            ],
            "mobile_number" => [
                "required",
                "string",            
                "regex:/^\+63\d+$/",
                "min:13",
                "max:13",
                "unique:users,mobile_number," . $this->route()->user,
            ],
            "gender" => [
                "required",
                "in:male,female"
            ],
            "age" => [
                "required",
                "numeric",
            ],

            "username" => 
             [
                "required",
                "unique:users,username," . $this->route()->user,
            ],
            "role_id" => [
                "required",
                "exists:roles,id"
            ]
        ];
    }

}
