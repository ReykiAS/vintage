<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FavoriteResource extends JsonResource
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
            'product' => new ProductResource($this->whenLoaded('product')),
            'user' => $this->whenLoaded('user'),
        ];

        return $data;
    }

    public function with($request)
    {
        return parent::with($request);
    }
}
