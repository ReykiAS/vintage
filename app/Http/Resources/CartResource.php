<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
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
            'fullname' => $this->user->fullname, 
            'product' => new ProductResource($this->product), 
            'qty' => $this->qty,
        ];

        if ($this->relationLoaded('product')) {
            $product = $this->product;
            $data['product']['image_url'] = $product->image ? $product->image->url : null; // Assuming 'image' is the relationship name and 'url' is the image attribute
        }    

        return $data;
    }

    public function with($request)
    {
        return parent::with($request);
    }
}
