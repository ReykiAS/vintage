<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentDetailResource extends JsonResource
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
            'cart_id' => $this->cart_id,
            'product' => new ProductResource($this->product),
            'Address' => $this->address,
            'Details' => $this->delivery_details, 
            'Shipping' => $this->shipping_fee,
            'Order' => $this->order,
            'Protection Fee' => $this->protection_fee,

        ];

        if ($this->relationLoaded('product')) {
            $product = $this->product;
            $data['product']['image_url'] = $product->image ? $product->image->url : null; // Assuming 'image' is the relationship name and 'url' is the image attribute
        }    

        return $data;
    }
}
