<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\Freezer;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\FreezerResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Requests\Freezer\StoreFreezerRequest;
use App\Http\Requests\Freezer\UpdateFreezerRequest;
use Illuminate\Http\Resources\Json\ResourceCollection;

class FreezerController extends Controller
{
    /**
     * Display a listing of all the freezers.
     *
     * @return ResourceCollection
     */
    public function all(): ResourceCollection
    {
        return FreezerResource::collection(Freezer::paginate(15));
    }

    /**
     * Display a listing of the freezers, regardless of the linked shop.
     *
     * @return ResourceCollection
     */
    public function index(Shop $shop): ResourceCollection
    {
        return FreezerResource::collection($shop->freezers()->paginate(15));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreFreezerRequest  $request
     * @return JsonResponse
     */
    public function store(StoreFreezerRequest $request, Shop $shop): JsonResponse
    {
        $freezerData = $request->validated();

        $freezer = $shop->freezers()->create($freezerData);

        return response()->json([
            'status'  => 'success',
            'message' => 'Freezer succesfully created.',
            'data'    => new FreezerResource($freezer)
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  Freezer  $freezer
     * @return JsonResource
     */
    public function show(Freezer $freezer): JsonResource
    {
        return new FreezerResource($freezer);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateFreezerRequest  $request
     * @param  Freezer  $freezer
     * @return JsonResponse
     */
    public function update(UpdateFreezerRequest $request, Freezer $freezer): JsonResponse
    {
        $freezerData = $request->validated();

        $freezer->update($freezerData);
        $freezer->refresh();

        return response()->json([
            'status'  => 'success',
            'message' => 'Freezer succesfully updated.',
            'data'    => new FreezerResource($freezer)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Freezer  $freezer
     * @return JsonResponse
     */
    public function destroy(Freezer $freezer): JsonResponse
    {
        $freezer->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Freezer succesfully deleted.',
        ]);
    }
}
