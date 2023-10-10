<?php

namespace App\Http\Controllers;

use App\Models\Freezer;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\ProductResource;
use App\Http\Resources\FreezerProductResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Requests\FreezerProductLink\StoreFreezerProductConnectionRequest;
use App\Http\Requests\FreezerProductLink\UpdateFreezerProductConnectionRequest;

class FreezerProductController extends Controller
{
    /**
     * Display a list of all the products associated with the freezer, among with the stock information.
     *
     * @return ResourceCollection
     */
    public function index(Freezer $freezer): ResourceCollection
    {
        return ProductResource::collection($freezer->products()->paginate(15));
    }

    /**
     * Store a new link between freezer and product.
     *
     * @param  StoreFreezerProductConnectionRequest  $request
     * @param  Freezer  $freezer
     * @return JsonResponse
     */
    public function store(StoreFreezerProductConnectionRequest $request, Freezer $freezer): JsonResponse
    {
        $product = Product::find($request->product_id);

        $freezer
            ->products()
            ->attach($product, [
                'current_stock' => $request->current_stock ?? 0,
                'max_stock'     => $request->max_stock,
            ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Product succesfully linked to the freezer.',
            'data'    => new FreezerProductResource($freezer->refresh()->product($product))
        ]);
    }

    /**
     * Display the stock information of a specific product in a specific freezer.
     *
     * @param  Freezer  $freezer
     * @param  Product  $product
     * @return FreezerProductResource
     */
    public function show(Freezer $freezer, Product $product): FreezerProductResource
    {
        if (!$freezer->products()->where('id', $product->id)->exists()) {
            abort(404, "No link found between freezer {$freezer->id} and product {$product->id}.");
        }

        return new FreezerProductResource($freezer->products()->find($product->id)->pivot);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateFreezerProductConnectionRequest  $request
     * @param  Freezer  $freezer
     * @param  Product  $product
     * @return JsonResponse
     */
    public function update(UpdateFreezerProductConnectionRequest $request, Freezer $freezer, Product $product): JsonResponse
    {
        $updateData = $request->validated();
        $freezerProductLink = $freezer->product($product);

        $newStock = data_get($updateData, 'alteration')
            ? $freezerProductLink->current_stock + data_get($updateData, 'alteration')
            : data_get($updateData, 'current_stock') ?? $freezerProductLink->current_stock;

        $freezer->products()->updateExistingPivot($product->id, [
            'max_stock'     => $updateData['max_stock'] ?? $freezerProductLink->max_stock,
            'current_stock' => $newStock
        ]);

        if (!empty(array_intersect_key(array_flip(['alteration', 'current_stock']), $updateData))) {
            $alteration = data_get($updateData, 'alteration') ?? $newStock - $freezerProductLink->current_stock;

            $freezerProductLink->createMutation([
                'alteration'  => $alteration,
                'type'        => data_get($updateData, 'mutation_type') ?? 'correction',
                'stock_after' => $newStock
            ]);
        }

        $freezerProductLink = $freezer->refresh()->product($product);

        return response()->json([
            'status'  => 'success',
            'message' => 'Product info within freezer succesfully updated.',
            'data'    => new FreezerProductResource($freezerProductLink)
        ]);
    }

    /**
     * Remove the connection between a freezer and a product.
     *
     * @param  StoreFreezerProductConnectionRequest  $request
     * @param  Freezer  $freezer
     * @return JsonResponse
     */
    public function destroy(Freezer $freezer, Product $product): JsonResponse
    {
        if (!($freezerProductLink = $freezer->product($product))) {
            abort(404, 'A connection between this product and freezer does not exist.');
        }

        $freezerProductLink->mutations()->delete();
        $freezerProductLink->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Product successfully deleted from freezer.'
        ]);
    }
}
