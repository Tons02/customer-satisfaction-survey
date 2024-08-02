<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionAnswerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'survey_id' => $this->survey_id,
            'name' => $this->survey ? [
            'id' => $this->survey->id,
            'name' => $this->survey->first_name . ' ' . $this->survey->last_name,
        ] : 'deleted survey',
            'question' => $this->question,
            'answer' => $this->answer,
        ];
    }
}
