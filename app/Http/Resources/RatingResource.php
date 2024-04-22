<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RatingResource extends JsonResource
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
            'rating' => $this->rating,
            'username'=> $this->user->username,
            'product' => new ProductResource($this->product),
        ];
        return $data;
    }

    public function with($request)
    {
        return parent::with($request);
    }
}
