<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FreezerProductResource extends JsonResource
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
            'freezer_id'    => $this->freezer_id,
            'product_id'    => $this->product_id,
            'current_stock' => $this->current_stock,
            'max_stock'     => $this->max_stock,
        ];
    }
}
