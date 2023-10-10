<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Http\Resources\ShopFreezerProductResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ShopFreezerProductController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  Shop $shop
     * @return ResourceCollection
     */
    public function __invoke(Shop $shop): ResourceCollection
    {
        $shop->load('freezers.products');

        return ShopFreezerProductResource::collection($shop->freezers);
    }
}
