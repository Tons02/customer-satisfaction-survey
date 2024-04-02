<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'suffix' => $this->suffix,
            'mobile_number' => $this->mobile_number,
            'gender' => $this->gender,
            'age' => $this->age,
            'username' => $this->username,
            'role' => [
                'id' => $this->role->id,
                'name' => $this->role->name
            ],
            'is_active' => $this->is_active,
            'created_at' => $this->created_at
        ];
    }
}
