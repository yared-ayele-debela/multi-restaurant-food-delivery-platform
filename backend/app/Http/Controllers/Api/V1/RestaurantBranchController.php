<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\RestaurantBranchResource;
use App\Models\Restaurant;
use Illuminate\Http\JsonResponse;

class RestaurantBranchController extends Controller
{
    /**
     * Active delivery branches for a publicly visible restaurant.
     */
    public function index(Restaurant $restaurant): JsonResponse
    {
        if (! $restaurant->isPublicInCatalog()) {
            abort(404);
        }

        $branches = $restaurant->branches()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return response()->json([
            'data' => RestaurantBranchResource::collection($branches)->resolve(),
        ]);
    }
}
