<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SurveyPeriodRequest extends FormRequest
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
        'valid_from' => [
            'required',
            'date',
        ],
        'valid_to' => [
            'required',
            'date',
            'after_or_equal:valid_from', // ensures valid_to is not before valid_from
        ],

        ];
    }
}
