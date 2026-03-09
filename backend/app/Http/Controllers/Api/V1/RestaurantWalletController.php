<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\RestaurantWalletResource;
use App\Models\Restaurant;
use App\Models\Wallet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RestaurantWalletController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $request->validate([
            'restaurant_id' => ['required', 'integer', 'exists:restaurants,id'],
        ]);

        $restaurant = Restaurant::query()
            ->whereKey($request->query('restaurant_id'))
            ->where('owner_id', $request->user()->id)
            ->firstOrFail();

        $wallet = Wallet::query()->firstOrCreate(
            [
                'holder_type' => Restaurant::class,
                'holder_id' => $restaurant->id,
            ],
            [
                'balance' => 0,
                'total_earned' => 0,
                'total_withdrawn' => 0,
                'total_commission_paid' => 0,
                'currency' => config('food-delivery.currency', 'USD'),
                'is_active' => true,
            ]
        );

        return (new RestaurantWalletResource($wallet))->response();
    }
}
