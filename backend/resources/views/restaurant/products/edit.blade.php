@extends('restaurant.layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Edit Product</h4>
            <div class="page-title-right">
                <a href="{{ route('restaurant.products.index') }}" class="btn btn-secondary">
                    <i data-feather="arrow-left" class="me-1" style="width: 16px; height: 16px;"></i>
                    Back to Products
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- Product Information -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Product Information</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('restaurant.products.update', $product) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                        <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $product->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="slug" class="form-label">Slug</label>
                        <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" value="{{ old('slug', $product->slug) }}">
                        <small class="text-muted">Leave empty to auto-generate from name</small>
                        @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4">{{ old('description', $product->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Current Image</label>
                        <div class="d-flex align-items-center gap-3 mb-2">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="rounded" style="width: 100px; height: 100px; object-fit: cover;">
                            @else
                                <div class="rounded bg-light d-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                                    <i data-feather="package" style="width: 32px; height: 32px;"></i>
                                </div>
                            @endif
                        </div>
                        <label for="image" class="form-label">Upload New Image</label>
                        <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" accept="image/*">
                        <small class="text-muted">Max 2MB. Recommended size: 800x600px</small>
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="base_price" class="form-label">Base Price <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" class="form-control @error('base_price') is-invalid @enderror" id="base_price" name="base_price" value="{{ old('base_price', $product->base_price) }}" required>
                                </div>
                                @error('base_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="discount_price" class="form-label">Discount Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" class="form-control @error('discount_price') is-invalid @enderror" id="discount_price" name="discount_price" value="{{ old('discount_price', $product->discount_price) }}">
                                </div>
                                @error('discount_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="preparation_time" class="form-label">Prep Time (min)</label>
                                <input type="number" class="form-control @error('preparation_time') is-invalid @enderror" id="preparation_time" name="preparation_time" value="{{ old('preparation_time', $product->preparation_time) }}" min="1">
                                @error('preparation_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="sort_order" class="form-label">Sort Order</label>
                                <input type="number" class="form-control @error('sort_order') is-invalid @enderror" id="sort_order" name="sort_order" value="{{ old('sort_order', $product->sort_order) }}" min="0">
                                @error('sort_order')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="calories" class="form-label">Calories</label>
                                <input type="number" class="form-control @error('calories') is-invalid @enderror" id="calories" name="calories" value="{{ old('calories', $product->calories) }}" min="0">
                                @error('calories')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label d-block">Status</label>
                                <div class="form-check form-switch form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">Active</label>
                                </div>
                                <div class="form-check form-switch form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_featured">Featured</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Dietary Information</label>
                        <div class="row">
                            @foreach(['vegetarian', 'vegan', 'gluten_free', 'halal', 'kosher'] as $diet)
                                <div class="col-6 col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="dietary_{{ $diet }}" name="dietary_info[]" value="{{ $diet }}" {{ is_array(old('dietary_info', $product->dietary_info ?? [])) && in_array($diet, old('dietary_info', $product->dietary_info ?? [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="dietary_{{ $diet }}">
                                            {{ ucwords(str_replace('_', ' ', $diet)) }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Allergens</label>
                        <div class="row">
                            @foreach(['nuts', 'dairy', 'eggs', 'soy', 'wheat', 'shellfish', 'fish'] as $allergen)
                                <div class="col-6 col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="allergen_{{ $allergen }}" name="allergens[]" value="{{ $allergen }}" {{ is_array(old('allergens', $product->allergens ?? [])) && in_array($allergen, old('allergens', $product->allergens ?? [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="allergen_{{ $allergen }}">
                                            {{ ucwords($allergen) }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i data-feather="save" class="me-1" style="width: 16px; height: 16px;"></i>
                            Update Product
                        </button>
                        <a href="{{ route('restaurant.products.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Product Sizes -->
        <div class="card mt-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Product Sizes</h5>
                <a href="{{ route('restaurant.products.sizes.create', $product) }}" class="btn btn-sm btn-primary">
                    <i data-feather="plus" class="me-1" style="width: 14px; height: 14px;"></i>
                    Add Size
                </a>
            </div>
            <div class="card-body">
                @if($product->sizes->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Price</th>
                                    <th>Default</th>
                                    <th>Order</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($product->sizes as $size)
                                    <tr>
                                        <td>{{ $size->name }}</td>
                                        <td>${{ number_format($size->price, 2) }}</td>
                                        <td>
                                            @if($size->is_default)
                                                <span class="badge bg-success">Default</span>
                                            @endif
                                        </td>
                                        <td>{{ $size->sort_order }}</td>
                                        <td>
                                            <a href="{{ route('restaurant.products.sizes.edit', [$product, $size]) }}" class="btn btn-sm btn-primary">
                                                <i data-feather="edit-2" style="width: 14px; height: 14px;"></i>
                                            </a>
                                            <form action="{{ route('restaurant.products.sizes.destroy', [$product, $size]) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this size?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i data-feather="trash-2" style="width: 14px; height: 14px;"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted mb-0">No sizes added. Add sizes for different portion options.</p>
                @endif
            </div>
        </div>

        <!-- Product Addons -->
        <div class="card mt-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Product Addons</h5>
                <a href="{{ route('restaurant.products.addons.create', $product) }}" class="btn btn-sm btn-primary">
                    <i data-feather="plus" class="me-1" style="width: 14px; height: 14px;"></i>
                    Add Addon
                </a>
            </div>
            <div class="card-body">
                @if($product->addons->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Group</th>
                                    <th>Price</th>
                                    <th>Max Qty</th>
                                    <th>Active</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($product->addons as $addon)
                                    <tr>
                                        <td>{{ $addon->name }}</td>
                                        <td>{{ $addon->group_name ?? '-' }}</td>
                                        <td>${{ number_format($addon->price, 2) }}</td>
                                        <td>{{ $addon->max_quantity ?? 'Unlimited' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $addon->is_active ? 'success' : 'secondary' }}">
                                                {{ $addon->is_active ? 'Yes' : 'No' }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('restaurant.products.addons.edit', [$product, $addon]) }}" class="btn btn-sm btn-primary">
                                                <i data-feather="edit-2" style="width: 14px; height: 14px;"></i>
                                            </a>
                                            <form action="{{ route('restaurant.products.addons.destroy', [$product, $addon]) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this addon?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i data-feather="trash-2" style="width: 14px; height: 14px;"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted mb-0">No addons added. Add extra options customers can choose.</p>
                @endif
            </div>
        </div>

        <!-- Product Stock -->
        <div class="card mt-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Stock Management</h5>
                <a href="{{ route('restaurant.products.stock.index', $product) }}" class="btn btn-sm btn-info">
                    <i data-feather="list" class="me-1" style="width: 14px; height: 14px;"></i>
                    View All
                </a>
            </div>
            <div class="card-body">
                @if($product->stock->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Branch</th>
                                    <th>Quantity</th>
                                    <th>Low Stock Threshold</th>
                                    <th>Tracking</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($product->stock as $stock)
                                    <tr>
                                        <td>{{ $stock->branch?->name ?? 'All Branches' }}</td>
                                        <td>{{ $stock->quantity }}</td>
                                        <td>{{ $stock->low_stock_threshold ?? '-' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $stock->track_stock ? 'success' : 'secondary' }}">
                                                {{ $stock->track_stock ? 'Yes' : 'No' }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($stock->track_stock && $stock->quantity <= ($stock->low_stock_threshold ?? 0))
                                                <span class="badge bg-danger">Low Stock</span>
                                            @else
                                                <span class="badge bg-success">OK</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted mb-0">
                        No stock entries. <a href="{{ route('restaurant.products.stock.index', $product) }}">Set up stock tracking</a>
                    </p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
