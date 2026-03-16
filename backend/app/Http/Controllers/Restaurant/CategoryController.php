<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        $restaurant = $request->attributes->get('restaurant');

        // Authorize using policy
        $this->authorize('manageMenu', $restaurant);

        $categories = Category::where('restaurant_id', $restaurant->id)
            ->withCount('products')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(20);

        return view('restaurant.categories.index', [
            'categories' => $categories,
            'restaurant' => $restaurant,
        ]);
    }

    public function create(Request $request): View
    {
        $restaurant = $request->attributes->get('restaurant');

        // Authorize using policy
        $this->authorize('manageMenu', $restaurant);

        return view('restaurant.categories.create', [
            'restaurant' => $restaurant,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $restaurant = $request->attributes->get('restaurant');

        // Authorize using policy
        $this->authorize('manageMenu', $restaurant);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:categories,slug'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:2048'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ]);

        $validated['restaurant_id'] = $restaurant->id;
        $validated['is_active'] = $request->boolean('is_active', true);

        if (empty($validated['slug'])) {
            $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']);
        }

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('categories', 'public');
        }

        Category::create($validated);

        return redirect()->route('restaurant.categories.index')
            ->with('success', 'Category created successfully.');
    }

    public function edit(Request $request, Category $category): View
    {
        $restaurant = $request->attributes->get('restaurant');
        
        // Authorize using policy
        $this->authorize('manageMenu', $restaurant);

        return view('restaurant.categories.edit', [
            'category' => $category,
            'restaurant' => $restaurant,
        ]);
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $restaurant = $request->attributes->get('restaurant');
        
        // Authorize using policy
        $this->authorize('manageMenu', $restaurant);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:categories,slug,'.$category->id],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:2048'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        if (empty($validated['slug'])) {
            $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']);
        }

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($category->image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($category->image);
            }
            $validated['image'] = $request->file('image')->store('categories', 'public');
        }

        $category->update($validated);

        return redirect()->route('restaurant.categories.index')
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(Request $request, Category $category): RedirectResponse
    {
        $restaurant = $request->attributes->get('restaurant');
        
        // Authorize using policy
        $this->authorize('manageMenu', $restaurant);

        // Check if category has products
        if ($category->products()->count() > 0) {
            return back()->with('error', 'Cannot delete category with products. Move or delete products first.');
        }

        // Delete image if exists
        if ($category->image) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($category->image);
        }

        $category->delete();

        return redirect()->route('restaurant.categories.index')
            ->with('success', 'Category deleted successfully.');
    }

    private function authorizeAccess($restaurant, Category $category): void
    {
        if ($category->restaurant_id !== $restaurant->id) {
            abort(403, 'Unauthorized access to this category.');
        }
    }
}
