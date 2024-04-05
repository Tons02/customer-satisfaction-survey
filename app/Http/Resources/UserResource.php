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
            'id' => $this->id,
            'id_prefix' => $this->id_prefix,
            'id_no' => $this->id_no,
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'contact_details' => $this->contact_details,
            'sex' => $this->sex,
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
