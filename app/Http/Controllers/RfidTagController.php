<?php

namespace App\Http\Controllers;

use App\Models\RfidTag;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\RfidTagResource;
use App\Http\Requests\RfidTags\StoreRfidTagRequest;
use Illuminate\Http\Resources\Json\ResourceCollection;

class RfidTagController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return ResourceCollection
     */
    public function index(): ResourceCollection
    {
        return RfidTagResource::collection(RfidTag::paginate(15));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreRfidTagRequest  $request
     * @return JsonResponse
     */
    public function store(StoreRfidTagRequest $request): JsonResponse
    {
        $rfidTagData = $request->validated();

        $rfidTag = RfidTag::create($rfidTagData)->load('product');

        return response()->json([
            'status'  => 'success',
            'message' => 'RFID tag succesfully linked to the product.',
            'data'    => new RfidTagResource($rfidTag)
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  RfidTag  $tag
     * @return RfidTagResource
     */
    public function show(RfidTag $tag): RfidTagResource
    {
        return new RfidTagResource($tag->load('product'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  RfidTag  $tag
     * @return JsonResponse
     */
    public function destroy(RfidTag $tag): JsonResponse
    {
        $tag->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'RFID tag succesfully deleted.',
        ]);
    }
}
