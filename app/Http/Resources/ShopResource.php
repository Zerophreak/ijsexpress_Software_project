<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ShopResource extends JsonResource
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
            'id'              => $this->id,
            'customer_number' => $this->customer_number,
            'name'            => $this->name,
            'type'            => $this->type,
            'street'          => $this->street,
            'house_number'    => $this->house_number,
            'postal_code'     => $this->postal_code,
            'city'            => $this->city,
            'logo_url'        => $this->logo_url,
            'stock_level'     => $this->when(array_key_exists('with_stock_level', $request->query()), $this->stock_level),
            'created_at'      => $this->created_at,
            'updated_at'      => $this->updated_at,
        ];
    }
}
