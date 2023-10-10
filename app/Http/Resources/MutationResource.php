<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MutationResource extends JsonResource
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
            'product_id'   => $this->product_id,
            'freezer_id'   => $this->freezer_id,
            'alteration'   => $this->alteration,
            'type'         => $this->type,
            'stock_before' => $this->stock_before,
            'stock_after'  => $this->stock_after,
            $this->mergeWhen(
                $this->resource->relationLoaded('freezer') && $this->freezer->relationLoaded('shop'),
                function () {
                    return [
                        'shop_id'   => $this->freezer->shop->id,
                        'shop_name' => $this->freezer->shop->name
                    ];
                }
            ),
            'freezer_name' => $this->whenLoaded('freezer', function () {
                return $this->freezer->name;
            }),
            'product_name' => $this->whenLoaded('product', function () {
                return $this->product->name;
            }),
            'created_at'   => $this->created_at
        ];
    }
}
