<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'id'            => $this->id,
            'name'          => $this->name,
            'ean'           => $this->ean,
            'chip_id'       => $this->chip_id,
            'logo_url'      => $this->logo_url,
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
            'current_stock' => $this->whenPivotLoaded('freezer_product', function () {
                return $this->pivot->current_stock;
            }),
            'max_stock'     => $this->whenPivotLoaded('freezer_product', function () {
                return $this->pivot->max_stock;
            }),
        ];
    }
}
