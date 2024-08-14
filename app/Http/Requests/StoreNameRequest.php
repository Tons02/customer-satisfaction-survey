<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreNameRequest extends FormRequest
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
            "province_id" => ["required","exists:provinces,id"],
            "name" => [
                "required",
                "string",
                $this->route()->store_name
                    ? "unique:store_names,name," . $this->route()->store_name
                    : "unique:store_names,name",
            ],
            "address" => ["required"]
        ];
    }
}
