<?php

namespace App\Http\Resources\Category;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name'=>$this->name,
            'parent_id'=>$this->parent_id,
            'description'=>$this->description,
            'children'=>CategoryResource::collection($this->whenLoaded('children')),
            'parent'=>new CategoryResource($this->whenLoaded('parent')),
        ];
    }
}
