@extends('restaurant.layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Add Size - {{ $product->name }}</h4>
            <div class="page-title-right">
                <a href="{{ route('restaurant.products.edit', $product) }}" class="btn btn-secondary">
                    <i data-feather="arrow-left" class="me-1" style="width: 16px; height: 16px;"></i>
                    Back to Product
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Size Details</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('restaurant.products.sizes.store', $product) }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label">Size Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="e.g., Small, Medium, Large" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="price" class="form-label">Price <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price') }}" required>
                        </div>
                        <small class="text-muted">Price for this size variant</small>
                        @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="sort_order" class="form-label">Sort Order</label>
                        <input type="number" class="form-control @error('sort_order') is-invalid @enderror" id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}" min="0">
                        <small class="text-muted">Lower numbers appear first. 0 is typically the default.</small>
                        @error('sort_order')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_default" name="is_default" value="1" {{ old('is_default') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_default">
                                Set as default size
                            </label>
                        </div>
                        <small class="text-muted">This size will be pre-selected when customers view the product</small>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i data-feather="save" class="me-1" style="width: 16px; height: 16px;"></i>
                            Add Size
                        </button>
                        <a href="{{ route('restaurant.products.edit', $product) }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Current Sizes</h5>
            </div>
            <div class="card-body">
                @if($product->sizes->count() > 0)
                    <ul class="list-group">
                        @foreach($product->sizes as $size)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    {{ $size->name }}
                                    @if($size->is_default)
                                        <span class="badge bg-success ms-2">Default</span>
                                    @endif
                                </div>
                                <span class="badge bg-primary">${{ number_format($size->price, 2) }}</span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted mb-0">No sizes added yet.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
