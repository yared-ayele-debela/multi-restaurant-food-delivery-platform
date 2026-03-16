@extends('restaurant.layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Edit Stock Entry - {{ $product->name }}</h4>
            <div class="page-title-right">
                <a href="{{ route('restaurant.products.stock.index', $product) }}" class="btn btn-secondary">
                    <i data-feather="arrow-left" class="me-1" style="width: 16px; height: 16px;"></i>
                    Back to Stock
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Stock Details</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('restaurant.products.stock.update', [$product, $stock]) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Branch</label>
                        <input type="text" class="form-control" value="{{ $stock->branch?->name ?? 'All Branches' }}" disabled>
                        <input type="hidden" name="branch_id" value="{{ $stock->branch_id }}">
                    </div>

                    <div class="mb-3">
                        <label for="quantity" class="form-label">Current Quantity <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('quantity') is-invalid @enderror" id="quantity" name="quantity" value="{{ old('quantity', $stock->quantity) }}" min="0" required>
                        @error('quantity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="low_stock_threshold" class="form-label">Low Stock Threshold</label>
                        <input type="number" class="form-control @error('low_stock_threshold') is-invalid @enderror" id="low_stock_threshold" name="low_stock_threshold" value="{{ old('low_stock_threshold', $stock->low_stock_threshold) }}" min="0">
                        <small class="text-muted">Alert when stock falls below this number</small>
                        @error('low_stock_threshold')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="track_stock" name="track_stock" value="1" {{ old('track_stock', $stock->track_stock) ? 'checked' : '' }}>
                            <label class="form-check-label" for="track_stock">Track Stock</label>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i data-feather="save" class="me-1" style="width: 16px; height: 16px;"></i>
                            Update Stock Entry
                        </button>
                        <a href="{{ route('restaurant.products.stock.index', $product) }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
