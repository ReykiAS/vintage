<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $originalPrice = (float) $this->price;
        $discountedPrice = (float) $this->discountedPrice;
        $data = [
            'id' => $this->id,
            'image' => $this->image ? $this->image->url : null, // Tampilkan URL gambar terlebih dahulu
            'name' => $this->name,
            'price' => $this->price,
            'size' => $this->variants->isNotEmpty() ? $this->variants->pluck('size')->first() : null,
        ];
        if ($discountedPrice !== $originalPrice) {
            $data['discounted_price'] = $discountedPrice;
        }

        if ($this->isDetail) {
        $data['quality'] = $this->variants->isNotEmpty() ? $this->variants->pluck('quality')->first() : null;
        $data['item_description'] = $this->description;
        $data['store_name'] = $this->user->username;
        $data['category'] = $this->category->name;
        $data['brand'] = $this->brand->name;
        $data['uploaded'] = $this->created_at;
        $data['avg_rating'] = (int) $this->getAverageRatingAttribute();
      }

        return $data;
    }
    public function withDetail()
    {
        $this->resource->isDetail = true;
        return $this;
    }

    public function with($request)
    {
        return parent::with($request);
    }
}
