<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\ShopResource;
use App\Http\Requests\Shop\StoreShopRequest;
use App\Http\Requests\Shop\UpdateShopRequest;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ShopController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return ResourceCollection
     */
    public function index(): ResourceCollection
    {
        return ShopResource::collection(Shop::with('products')->paginate(15));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreShopRequest  $request
     * @return JsonResponse
     */
    public function store(StoreShopRequest $request): JsonResponse
    {
        $shopData = $request->validated();

        $shop = Shop::create($shopData);

        return response()->json([
            'status'  => 'success',
            'message' => 'Shop succesfully created.',
            'data'    => new ShopResource($shop)
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  Shop  $shop
     * @return JsonResource
     */
    public function show(Shop $shop): JsonResource
    {
        return new ShopResource($shop);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Shop  $shop
     * @return JsonResponse
     */
    public function update(UpdateShopRequest $request, Shop $shop): JsonResponse
    {
        $shopData = $request->validated();

        $shop->update($shopData);
        $shop->refresh();

        return response()->json([
            'status'  => 'success',
            'message' => 'Shop succesfully updated.',
            'data'    => new ShopResource($shop)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Shop  $shop
     * @return JsonResponse
     */
    public function destroy(Shop $shop): JsonResponse
    {
        $shop->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Shop succesfully deleted.',
        ]);
    }
}
