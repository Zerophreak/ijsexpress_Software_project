<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Http\Resources\ShopProductResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ShopProductController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  Shop  $shop
     * @return ResourceCollection
     */
    public function __invoke(Shop $shop): ResourceCollection
    {
        $shop->load('freezers.products');

        return ShopProductResource::collection($shop->summed_products);
    }
}
