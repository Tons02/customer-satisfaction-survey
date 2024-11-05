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
            "personal_info.contact_details" => [
                "unique:users,contact_details," . $this->route()->user,
                "regex:/^\+63\d{10}$/",
            ],
            "personal_info.sex" => "sometimes:required",
            "personal_info.company" => "required",
            "personal_info.business_unit" => "required",
            "personal_info.department" => "required",
            "personal_info.unit" => "required",
            "personal_info.sub_unit" => "required",
            "personal_info.location" => "required",
            "personal_info.province_id" =>  ["required","exists:provinces,id"],
            "personal_info.store_id" =>  ["required","exists:store_names,id"],

            "username" => [
                "required",
                "unique:users,username," . $this->route()->user,
            ],

            "role_id" => ["required","exists:roles,id"]
        ];
    }

    public function messages()
    {
        return [
            "personal_info.id_no.unique" => "The employee ID has already been taken",
            "personal_info.contact_details.regex" => "The mobile number field format is invalid.",
            "personal_info.contact_details.unique" => "The contact number has already been taken.",
            "personal_info.company.required" => "The company field is required.",
            "personal_info.business_unit.required" => "The business unit field is required.",
            "personal_info.department.required" => "The department field is required.",
            "personal_info.unit.required" => "The unit field is required.",
            "personal_info.sub_unit.required" => "The sub_unit field is required.",
            "personal_info.location.required" => "The location field is required.",
            "personal_info.province_id.required" => "The province field is required.",
            "personal_info.store_id.required" => "The store field is required.",
            "personal_info.province_id.exists" => "The selected province is invalid.",
            "personal_info.store_id.exists" => "The selected store is invalid.",
        ];
    }

}
