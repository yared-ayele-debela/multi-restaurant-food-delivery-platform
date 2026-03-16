<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductAddon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductAddonController extends Controller
{
    public function create(Request $request, Product $product): View
    {
        $restaurant = $request->attributes->get('restaurant');
        $this->authorizeAccess($restaurant, $product);

        return view('restaurant.products.addons.create', [
            'product' => $product,
            'restaurant' => $restaurant,
        ]);
    }

    public function store(Request $request, Product $product): RedirectResponse
    {
        $restaurant = $request->attributes->get('restaurant');
        $this->authorizeAccess($restaurant, $product);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'group_name' => ['nullable', 'string', 'max:255'],
            'max_quantity' => ['nullable', 'integer', 'min:1'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ]);

        $validated['product_id'] = $product->id;
        $validated['is_active'] = $request->boolean('is_active', true);

        ProductAddon::create($validated);

        return redirect()->route('restaurant.products.edit', $product)
            ->with('success', 'Addon added successfully.');
    }

    public function edit(Request $request, Product $product, ProductAddon $addon): View
    {
        $restaurant = $request->attributes->get('restaurant');
        $this->authorizeAccess($restaurant, $product);

        if ($addon->product_id !== $product->id) {
            abort(404);
        }

        return view('restaurant.products.addons.edit', [
            'product' => $product,
            'addon' => $addon,
            'restaurant' => $restaurant,
        ]);
    }

    public function update(Request $request, Product $product, ProductAddon $addon): RedirectResponse
    {
        $restaurant = $request->attributes->get('restaurant');
        $this->authorizeAccess($restaurant, $product);

        if ($addon->product_id !== $product->id) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'group_name' => ['nullable', 'string', 'max:255'],
            'max_quantity' => ['nullable', 'integer', 'min:1'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $addon->update($validated);

        return redirect()->route('restaurant.products.edit', $product)
            ->with('success', 'Addon updated successfully.');
    }

    public function destroy(Request $request, Product $product, ProductAddon $addon): RedirectResponse
    {
        $restaurant = $request->attributes->get('restaurant');
        $this->authorizeAccess($restaurant, $product);

        if ($addon->product_id !== $product->id) {
            abort(404);
        }

        $addon->delete();

        return redirect()->route('restaurant.products.edit', $product)
            ->with('success', 'Addon deleted successfully.');
    }

    private function authorizeAccess($restaurant, Product $product): void
    {
        if ($product->restaurant_id !== $restaurant->id) {
            abort(403, 'Unauthorized access to this product.');
        }
    }
}
