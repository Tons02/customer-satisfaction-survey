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
            "personal_info.id_prefix" => "sometimes:required|min:1|regex:/[^\s]/",
            "personal_info.id_no" => [
                "sometimes:required",
                "unique:users,id_no",
            ],
            "personal_info.first_name" => "sometimes:required", 
            "personal_info.last_name" => "sometimes:required",
            "personal_info.sex" => "sometimes:required",
            "username" => [
                "required",
                "unique:users,username," . $this->route()->user,
            ],

            "role_id" => ["required","exists:roles,id"]
        ];
    }

}
