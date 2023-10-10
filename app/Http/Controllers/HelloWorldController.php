<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class HelloWorldController extends Controller
{
    /**
     * Respond with a test message.
     *
     * @return JsonResponse
     */
    public function test(): JsonResponse
    {
        return response()->json([
            'band' => 'K3',
            'song' => 'OYA LELE'
        ]);
    }
}
