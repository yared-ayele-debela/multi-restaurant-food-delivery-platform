@extends('restaurant.layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Stock Management - {{ $product->name }}</h4>
            <div class="page-title-right">
                <a href="{{ route('restaurant.products.stock.create', $product) }}" class="btn btn-primary">
                    <i data-feather="plus" class="me-1" style="width: 16px; height: 16px;"></i>
                    Add Stock Entry
                </a>
                <a href="{{ route('restaurant.products.edit', $product) }}" class="btn btn-secondary ms-2">
                    <i data-feather="arrow-left" class="me-1" style="width: 16px; height: 16px;"></i>
                    Back to Product
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Stock Entries</h5>
            </div>
            <div class="card-body">
                @if($stock->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Branch</th>
                                    <th>Quantity</th>
                                    <th>Low Stock Threshold</th>
                                    <th>Tracking</th>
                                    <th>Status</th>
                                    <th style="width: 200px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stock as $stockItem)
                                    <tr>
                                        <td>{{ $stockItem->branch?->name ?? 'All Branches' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $stockItem->quantity <= ($stockItem->low_stock_threshold ?? 0) ? 'danger' : 'success' }} fs-6">
                                                {{ $stockItem->quantity }}
                                            </span>
                                        </td>
                                        <td>{{ $stockItem->low_stock_threshold ?? '-' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $stockItem->track_stock ? 'success' : 'secondary' }}">
                                                {{ $stockItem->track_stock ? 'Enabled' : 'Disabled' }}
                                            </span>
                                        </td>
                                        <td>
                                            @if(!$stockItem->track_stock)
                                                <span class="badge bg-secondary">Not Tracked</span>
                                            @elseif($stockItem->quantity <= ($stockItem->low_stock_threshold ?? 0))
                                                <span class="badge bg-danger">Low Stock</span>
                                            @else
                                                <span class="badge bg-success">In Stock</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#adjustModal{{ $stockItem->id }}">
                                                    <i data-feather="sliders" style="width: 14px; height: 14px;"></i>
                                                    Adjust
                                                </button>
                                                <a href="{{ route('restaurant.products.stock.edit', [$product, $stockItem]) }}" class="btn btn-sm btn-primary">
                                                    <i data-feather="edit-2" style="width: 14px; height: 14px;"></i>
                                                </a>
                                                <form action="{{ route('restaurant.products.stock.destroy', [$product, $stockItem]) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this stock entry?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i data-feather="trash-2" style="width: 14px; height: 14px;"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <p class="text-muted mb-0">No stock entries found for this product.</p>
                        <a href="{{ route('restaurant.products.stock.create', $product) }}" class="btn btn-primary mt-2">Add Stock Entry</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Adjust Stock Modals -->
@foreach($stock as $stockItem)
<div class="modal fade" id="adjustModal{{ $stockItem->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Adjust Stock - {{ $stockItem->branch?->name ?? 'All Branches' }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('restaurant.products.stock.adjust', [$product, $stockItem]) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Current Quantity: <strong>{{ $stockItem->quantity }}</strong></p>
                    <div class="mb-3">
                        <label for="adjustment_{{ $stockItem->id }}" class="form-label">Adjustment</label>
                        <input type="number" class="form-control" id="adjustment_{{ $stockItem->id }}" name="adjustment" required>
                        <small class="text-muted">Use positive numbers to add stock, negative to remove.</small>
                    </div>
                    <div class="mb-3">
                        <label for="reason_{{ $stockItem->id }}" class="form-label">Reason (optional)</label>
                        <input type="text" class="form-control" id="reason_{{ $stockItem->id }}" name="reason" placeholder="e.g., New delivery, Damaged goods">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Apply Adjustment</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

@endsection
