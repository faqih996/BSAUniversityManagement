<?php

namespace App\Http\Resources\Operator;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudyPlanOperatorResource extends JsonResource
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
            'status' => $this->status,
            'notes' => $this->notes,
            'semester' => $this->semester,
            'created_at' => $this->created_at,
            'created_at' => $this->created_at,
        ];
    }
}
