<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return ResourceCollection
     */
    public function index(): ResourceCollection
    {
        return ProductResource::collection(Product::paginate(15));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreProductRequest  $request
     * @return JsonResponse
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        $productData = $request->validated();

        $product = Product::create($productData);

        return response()->json([
            'status'  => 'success',
            'message' => 'Product succesfully created.',
            'data'    => new ProductResource($product)
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  Product  $product
     * @return JsonResource
     */
    public function show(Product $product): JsonResource
    {
        return new ProductResource($product);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateProductRequest  $request
     * @param  Product  $product
     * @return JsonResponse
     */
    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $productData = $request->validated();

        $product->update($productData);
        $product->refresh();

        return response()->json([
            'status'  => 'success',
            'message' => 'Product succesfully updated.',
            'data'    => new ProductResource($product)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Product  $product
     * @return JsonResponse
     */
    public function destroy(Product $product): JsonResponse
    {
        $product->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Product succesfully deleted.',
        ]);
    }
}
