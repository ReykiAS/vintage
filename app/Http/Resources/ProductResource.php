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
            'Id' => $this->id,
            'Image' => $this->image ? $this->image->url : null, // Tampilkan URL gambar terlebih dahulu
            'Name' => $this->name,
            'Price' => $this->price,
            'size' => $this->variants->isNotEmpty() ? $this->variants->pluck('size')->first() : null,
        ];
        if ($discountedPrice !== $originalPrice) {
            $data['Discounted Price'] = $discountedPrice;
        }

        if ($this->isDetail) {
        $data['quality'] = $this->variants->isNotEmpty() ? $this->variants->pluck('quality')->first() : null;
        $data['Item Description'] = $this->description;
        $data['Store Name'] = $this->user->username;
        $data['Category'] = $this->category->name;
        $data['Brand'] = $this->brand->name;
        $data['Uploaded'] = $this->created_at;
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
