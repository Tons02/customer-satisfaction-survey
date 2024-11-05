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
            
            'company_id' => $this->company_id,
            'company' => $this->company,
            
            'business_unit_id' => $this->business_unit_id,
            'business_unit' => $this->business_unit,

            'department_id' => $this->department_id,
            'department' => $this->department,

            'unit_id' => $this->unit_id,
            'unit' => $this->unit,

            'sub_unit_id' => $this->sub_unit_id,
            'sub_unit' => $this->sub_unit,

            'location_id' => $this->location_id,
            'location' => $this->location,
            'province' => $this->province ? [
                'id' => $this->province->id,
                'name' => $this->province->name
            ] : null,
            'store' => $this->store ? [
                'id' => $this->store->id,
                'name' => $this->store->name
            ] : null,


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
