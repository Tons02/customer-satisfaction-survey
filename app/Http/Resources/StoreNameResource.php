<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreNameResource extends JsonResource
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
            'province' => [
                'id' => $this->province->id,
                'name' => $this->province->name
            ],
            'store_name' => $this->name,
            'address' => $this->address,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at
        ];
    }
}
