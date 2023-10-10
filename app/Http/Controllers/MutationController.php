<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\Freezer;
use App\Models\Product;
use App\Http\Resources\MutationResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Requests\Mutations\GetMutationsPerShopPerProductRequest;
use App\Http\Requests\Mutations\GetMutationsPerFreezerPerProductRequest;

class MutationController extends Controller
{
    /**
     * Retrieve all the mutations within a certain shop.
     *
     * @param  Shop  $shop
     * @return ResourceCollection
     */
    public function perShop(Shop $shop): ResourceCollection
    {
        return MutationResource::collection(
            $shop->mutations()
                ->with(['freezer', 'product'])
                ->paginate(15)
        );
    }

    /**
     * Retrieve all the mutations within a certain freezer.
     *
     * @param  Freezer  $freezer
     * @return ResourceCollection
     */
    public function perFreezer(Freezer $freezer): ResourceCollection
    {
        return MutationResource::collection(
            $freezer->mutations()
                ->with('product')
                ->paginate(15)
        );
    }

    /**
     * Retrieve all the mutations of a certain product.
     *
     * @param  Product  $product
     * @return ResourceCollection
     */
    public function perProduct(Product $product): ResourceCollection
    {
        return MutationResource::collection(
            $product->mutations()
                ->with('freezer.shop')
                ->paginate(15)
        );
    }

    /**
     * Retrieve all the mutations of a certain product within a certain freezer.
     *
     * @param  GetMutationsPerFreezerPerProductRequest  $request,
     * @param  Freezer  $freezer
     * @param  Product  $product
     * @return ResourceCollection
     */
    public function perFreezerPerProduct(
        GetMutationsPerFreezerPerProductRequest $request,
        Freezer $freezer,
        Product $product
    ): ResourceCollection {
        return MutationResource::collection(
            $freezer->mutations()
                ->whereProduct($product)
                ->paginate(15)
        );
    }

    /**
     * Retrieve all the mutations of a certain product within a certain shop.
     *
     * @param  GetMutationsPerShopPerProductRequest  $request
     * @param  Shop  $shop
     * @param  Product  $product
     * @return ResourceCollection
     */
    public function perShopPerProduct(
        GetMutationsPerShopPerProductRequest $request,
        Shop $shop,
        Product $product
    ): ResourceCollection {
        return MutationResource::collection(
            $shop->mutations()
                ->with('freezer')
                ->whereProduct($product)
                ->paginate(15)
        );
    }
}
