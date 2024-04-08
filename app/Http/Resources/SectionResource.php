<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SectionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return[
            'id' => $this->sections->id,
            'section' => $this->sections->section,
            'name' => $this->sections->name,
            'description' => $this->section->description,
            'next_section' => $this->section->next_section,
            'is_active' => $this->section->is_active,
        ];
    }
}
