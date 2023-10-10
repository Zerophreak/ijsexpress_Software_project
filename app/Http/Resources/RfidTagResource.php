<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RfidTagResource extends JsonResource
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
            'tag'        => $this->tag,
            'product_id' => $this->product_id,
            'created_at' => $this->created_at,
            'product'    => $this->whenLoaded('product', function () {
                return new ProductResource($this->product);
            })
        ];
    }
}
