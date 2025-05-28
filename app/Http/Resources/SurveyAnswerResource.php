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
            'receipt_number' => $this->receipt_number,
            'store' => [
                'id' => $this->store->id,
                'name' => $this->store->name
            ],
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'suffix' => $this->suffix,
            'mobile_number' => $this->mobile_number,
            'mobile_number_verified' => $this->mobile_number_verified,
            'gender' => $this->gender,
            'birthday' => $this->birthday,
            'voucher_code' => $this->voucher_code,
            'valid_until' => $this->valid_until,
            'next_voucher_date' => $this->next_voucher_date,
            'claim' => $this->claim,
            'claim_by' => $this->user ? [
                'id' => $this->user->id,
                'name' => $this->user->first_name . ' ' . $this->user->last_name,
                'province' => $this->user->province->name,
                'store' => $this->user->store->name,
            ] : 'unprocessed',
            'questionnaire_answer' => $this->questionnaire_answer,
            'is_active' => $this->is_active,
            'submit_date' => $this->submit_date,
            'claim_date' => ($this->claim == 'not_yet' || $this->claim == 'expired') ? 'unprocessed' : $this->claimed_date,
        ];
    }
}
