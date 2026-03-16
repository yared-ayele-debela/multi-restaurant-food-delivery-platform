<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\RestaurantBranch;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductStockController extends Controller
{
    public function index(Request $request, Product $product): View
    {
        $restaurant = $request->attributes->get('restaurant');
        $this->authorizeAccess($restaurant, $product);

        $stock = $product->stock()
            ->with('branch')
            ->orderBy('branch_id')
            ->get();

        $branches = RestaurantBranch::where('restaurant_id', $restaurant->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('restaurant.products.stock.index', [
            'product' => $product,
            'stock' => $stock,
            'branches' => $branches,
            'restaurant' => $restaurant,
        ]);
    }

    public function create(Request $request, Product $product): View
    {
        $restaurant = $request->attributes->get('restaurant');
        $this->authorizeAccess($restaurant, $product);

        $branches = RestaurantBranch::where('restaurant_id', $restaurant->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Get branches that don't have stock entries yet for this product
        $existingBranchIds = $product->stock()->pluck('branch_id')->toArray();

        return view('restaurant.products.stock.create', [
            'product' => $product,
            'branches' => $branches,
            'existingBranchIds' => $existingBranchIds,
            'restaurant' => $restaurant,
        ]);
    }

    public function store(Request $request, Product $product): RedirectResponse
    {
        $restaurant = $request->attributes->get('restaurant');
        $this->authorizeAccess($restaurant, $product);

        $validated = $request->validate([
            'branch_id' => ['nullable', 'exists:restaurant_branches,id'],
            'quantity' => ['required', 'integer', 'min:0'],
            'low_stock_threshold' => ['nullable', 'integer', 'min:0'],
            'track_stock' => ['boolean'],
        ]);

        // Verify branch belongs to restaurant if specified
        if (! empty($validated['branch_id'])) {
            $branch = RestaurantBranch::findOrFail($validated['branch_id']);
            if ($branch->restaurant_id !== $restaurant->id) {
                return back()->with('error', 'Invalid branch selected.');
            }
        }

        $validated['product_id'] = $product->id;
        $validated['track_stock'] = $request->boolean('track_stock', true);

        // Check if stock entry already exists for this product/branch combination
        $existingQuery = ProductStock::where('product_id', $product->id);
        if (empty($validated['branch_id'])) {
            $existingQuery->whereNull('branch_id');
        } else {
            $existingQuery->where('branch_id', $validated['branch_id']);
        }

        if ($existingQuery->exists()) {
            return back()->with('error', 'Stock entry already exists for this branch. Use edit instead.');
        }

        ProductStock::create($validated);

        return redirect()->route('restaurant.products.stock.index', $product)
            ->with('success', 'Stock entry created successfully.');
    }

    public function edit(Request $request, Product $product, ProductStock $stock): View
    {
        $restaurant = $request->attributes->get('restaurant');
        $this->authorizeAccess($restaurant, $product);

        if ($stock->product_id !== $product->id) {
            abort(404);
        }

        $branches = RestaurantBranch::where('restaurant_id', $restaurant->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('restaurant.products.stock.edit', [
            'product' => $product,
            'stock' => $stock,
            'branches' => $branches,
            'restaurant' => $restaurant,
        ]);
    }

    public function update(Request $request, Product $product, ProductStock $stock): RedirectResponse
    {
        $restaurant = $request->attributes->get('restaurant');
        $this->authorizeAccess($restaurant, $product);

        if ($stock->product_id !== $product->id) {
            abort(404);
        }

        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:0'],
            'low_stock_threshold' => ['nullable', 'integer', 'min:0'],
            'track_stock' => ['boolean'],
        ]);

        $validated['track_stock'] = $request->boolean('track_stock', true);

        $stock->update($validated);

        return redirect()->route('restaurant.products.stock.index', $product)
            ->with('success', 'Stock updated successfully.');
    }

    public function adjustStock(Request $request, Product $product, ProductStock $stock): RedirectResponse
    {
        $restaurant = $request->attributes->get('restaurant');
        $this->authorizeAccess($restaurant, $product);

        if ($stock->product_id !== $product->id) {
            abort(404);
        }

        $validated = $request->validate([
            'adjustment' => ['required', 'integer'],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        $newQuantity = $stock->quantity + $validated['adjustment'];

        if ($newQuantity < 0) {
            return back()->with('error', 'Adjustment would result in negative stock.');
        }

        $stock->update(['quantity' => $newQuantity]);

        // TODO: Log stock adjustment with reason for audit trail

        return redirect()->route('restaurant.products.stock.index', $product)
            ->with('success', 'Stock adjusted successfully. New quantity: '.$newQuantity);
    }

    public function destroy(Request $request, Product $product, ProductStock $stock): RedirectResponse
    {
        $restaurant = $request->attributes->get('restaurant');
        $this->authorizeAccess($restaurant, $product);

        if ($stock->product_id !== $product->id) {
            abort(404);
        }

        $stock->delete();

        return redirect()->route('restaurant.products.stock.index', $product)
            ->with('success', 'Stock entry deleted successfully.');
    }

    private function authorizeAccess($restaurant, Product $product): void
    {
        if ($product->restaurant_id !== $restaurant->id) {
            abort(403, 'Unauthorized access to this product.');
        }
    }
}
