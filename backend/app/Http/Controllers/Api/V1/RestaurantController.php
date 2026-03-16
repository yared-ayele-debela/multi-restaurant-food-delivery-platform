<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\RestaurantResource;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RestaurantController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = min(max((int) $request->query('per_page', 15), 1), 50);

        $restaurants = Restaurant::query()
            ->forPublicCatalog()
            ->with([
                'images' => fn ($q) => $q->orderBy('sort_order'),
                'hours' => fn ($q) => $q->orderBy('day_of_week'),
                'branches' => fn ($q) => $q->where('is_active', true)->orderBy('name'),
            ])
            ->orderBy('name')
            ->paginate($perPage);

        return RestaurantResource::collection($restaurants);
    }

    public function show(Request $request, Restaurant $restaurant): RestaurantResource
    {
        if (! $restaurant->isPublicInCatalog()) {
            abort(404);
        }

        $branchId = $request->query('branch_id');

        $stockQuery = function ($q) use ($branchId) {
            if ($branchId !== null && $branchId !== '') {
                $bid = (int) $branchId;
                $q->where(function ($q2) use ($bid) {
                    $q2->whereNull('branch_id')->orWhere('branch_id', $bid);
                });
            }
        };

        $restaurant->load([
            'images' => fn ($q) => $q->orderBy('sort_order'),
            'hours' => fn ($q) => $q->orderBy('day_of_week'),
            'branches' => fn ($q) => $q->where('is_active', true)->orderBy('name'),
            'categories' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order'),
            'categories.products' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order'),
            'categories.products.sizes' => fn ($q) => $q->orderBy('sort_order'),
            'categories.products.addons' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order'),
            'categories.products.stock' => $stockQuery,
        ]);

        return new RestaurantResource($restaurant);
    }
}
