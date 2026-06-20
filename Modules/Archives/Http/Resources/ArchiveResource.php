<?php

declare(strict_types=1);

namespace Modules\Archives\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArchiveResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'type' => $this->type,
            'title' => $this->title,
            'description' => $this->description,
            'is_favorite' => (bool) $this->is_favorite,
            'tags' => $this->whenLoaded('tags', fn () => $this->tags->map(fn ($tag) => [
                'id' => $tag->id,
                'name' => $tag->name,
                'color' => $tag->color,
            ])),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];

        // Add type-specific extension data
        // Note: extension() returns null for types without extension tables (e.g., note, article, idea, bookmark, prompt)
        $relation = $this->extension();
        if ($relation !== null) {
            $extension = $relation->first();
            if ($extension !== null) {
                $data['type_specific'] = $extension->toArray();
            }
        }

        return $data;
    }
}
