<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductSize;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductSizeController extends Controller
{
    public function create(Request $request, Product $product): View
    {
        $restaurant = $request->attributes->get('restaurant');
        $this->authorizeAccess($restaurant, $product);

        return view('restaurant.products.sizes.create', [
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
            'is_default' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $validated['product_id'] = $product->id;
        $validated['is_default'] = $request->boolean('is_default', false);

        // If this is set as default, unset other defaults
        if ($validated['is_default']) {
            $product->sizes()->update(['is_default' => false]);
        }

        ProductSize::create($validated);

        return redirect()->route('restaurant.products.edit', $product)
            ->with('success', 'Size added successfully.');
    }

    public function edit(Request $request, Product $product, ProductSize $size): View
    {
        $restaurant = $request->attributes->get('restaurant');
        $this->authorizeAccess($restaurant, $product);

        if ($size->product_id !== $product->id) {
            abort(404);
        }

        return view('restaurant.products.sizes.edit', [
            'product' => $product,
            'size' => $size,
            'restaurant' => $restaurant,
        ]);
    }

    public function update(Request $request, Product $product, ProductSize $size): RedirectResponse
    {
        $restaurant = $request->attributes->get('restaurant');
        $this->authorizeAccess($restaurant, $product);

        if ($size->product_id !== $product->id) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'is_default' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $validated['is_default'] = $request->boolean('is_default', false);

        // If this is set as default, unset other defaults
        if ($validated['is_default'] && ! $size->is_default) {
            $product->sizes()->update(['is_default' => false]);
        }

        $size->update($validated);

        return redirect()->route('restaurant.products.edit', $product)
            ->with('success', 'Size updated successfully.');
    }

    public function destroy(Request $request, Product $product, ProductSize $size): RedirectResponse
    {
        $restaurant = $request->attributes->get('restaurant');
        $this->authorizeAccess($restaurant, $product);

        if ($size->product_id !== $product->id) {
            abort(404);
        }

        // Don't allow deleting the only default size
        if ($size->is_default && $product->sizes()->count() <= 1) {
            return back()->with('error', 'Cannot delete the only size for this product.');
        }

        $size->delete();

        // If we deleted the default, set a new default
        if ($size->is_default) {
            $newDefault = $product->sizes()->orderBy('sort_order')->first();
            if ($newDefault) {
                $newDefault->update(['is_default' => true]);
            }
        }

        return redirect()->route('restaurant.products.edit', $product)
            ->with('success', 'Size deleted successfully.');
    }

    private function authorizeAccess($restaurant, Product $product): void
    {
        if ($product->restaurant_id !== $restaurant->id) {
            abort(403, 'Unauthorized access to this product.');
        }
    }
}
