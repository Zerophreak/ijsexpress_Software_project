<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ShopProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'product_id'    => $this->id,
            'shop_id'       => $request->shop->id,
            'current_stock' => $this->freezer_product->current_stock,
            'max_stock'     => $this->freezer_product->max_stock
        ];
    }
}
