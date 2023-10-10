<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Http\Requests\DecreaseStockByRfidTagRequest;

class RfidTagMutationController extends Controller
{
    /**
     * Decrease stock in the selected freezer, based on the RFID tag.
     *
     * @param  DecreaseStockByRfidTagRequest  $request
     * @return JsonResponse
     */
    public function decrease(DecreaseStockByRfidTagRequest $request): JsonResponse
    {
        $freezerProductLink = $request->freezer->product($request->product);
        $currentStock = $freezerProductLink->current_stock;
        $newStock = $currentStock - 1;

        if ($currentStock <= 0) {
            return response()->json([
                'status'  => 'error',
                'message' => 'The current stock may not become negative.'
            ], 422);
        }

        $request->freezer->products()->updateExistingPivot($request->product->id, [
            'current_stock' => $newStock
        ]);

        $freezerProductLink->createMutation([
            'alteration'  => -1,
            'type'        => 'sales',
            'stock_after' => $newStock
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Product stock within freezer succesfully updated.',
        ]);
    }
}
