<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $categories = Category::query()
            ->where('is_active', true)
            ->whereHas('restaurant', fn ($q) => $q->forPublicCatalog())
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'image', 'description'])
            ->unique(fn (Category $category) => $category->slug ?: $category->name)
            ->values()
            ->map(fn (Category $category) => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'image' => $category->image,
                'description' => $category->description,
            ]);

        return response()->json([
            'data' => $categories,
        ]);
    }
}
