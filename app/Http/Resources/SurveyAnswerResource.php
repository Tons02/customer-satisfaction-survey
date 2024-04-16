<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SurveyAnswerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'entry_code' => $this->entry_code,
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'suffix' => $this->suffix,
            'mobile_number' => $this->mobile_number,
            'mobile_number_verified' => $this->mobile_number_verified,
            'gender' => $this->gender,
            'age' => $this->age,
            'voucher_code' => $this->voucher_code,
            'next_voucher_date' => $this->next_voucher_date,
            'claim' => $this->claim,
            'questionnaire_answer' => $this->questionnaire_answer,
            'is_active' => $this->is_active
        ];
    }
}
