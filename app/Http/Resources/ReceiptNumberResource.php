<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReceiptNumberResource extends JsonResource
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
            'contact_details' => $this->contact_details,
            'store_id' => [
                'id' => $this->store->id,
                'name' => $this->store->name
            ],
            'is_valid' => $this->is_valid,
            'is_used' => $this->is_used,
            'is_done' => $this->is_done,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at
        ];
    }
}
