<?php

namespace App\Http\Resources;

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
        $data = [
            'id' => $this->id,
            'name' => $this->name,
        ];
        if ($this->isDetail) {
            $image = $this->image; 
            if ($image) {
                $data['image_url'] = $image->url;
            } else {
                $data['image_url'] = null;
            }
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
