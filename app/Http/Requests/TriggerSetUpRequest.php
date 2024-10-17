<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TriggerSetUpRequest extends FormRequest
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
            'trigger_point' => [
                'required',
                'numeric',
                'min:1',
                'lte:limit',  
            ],
            'limit' => 'required|numeric|min:1',
            'total' => 'required|numeric',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Calculate the total based on trigger_point and limit
            if ($this->trigger_point && $this->limit) {
                $calculated_total = ($this->limit / $this->trigger_point);

                // Check if the total provided matches the calculated total
                if ($this->total != $calculated_total) {
                    // Add a validation error if the totals do not match
                    $validator->errors()->add('total', 'The total is incorrect. It should be: ' . $calculated_total);
                }
            }
        });
    }
}
