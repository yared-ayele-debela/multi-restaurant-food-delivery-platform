@extends('restaurant.layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Order #{{ $order->order_number }}</h4>
            <div class="page-title-right">
                <a href="{{ route('restaurant.orders.index') }}" class="btn btn-secondary">
                    <i data-feather="arrow-left" class="me-1" style="width: 16px; height: 16px;"></i>
                    Back to Orders
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- Order Details -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Order Items</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Size</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->orderItems as $item)
                                <tr>
                                    <td>
                                        <strong>{{ $item->product_name }}</strong>
                                        @if($item->addons)
                                            <br><small class="text-muted">Addons: {{ $item->addons }}</small>
                                        @endif
                                        @if($item->special_instructions)
                                            <br><small class="text-warning">Note: {{ $item->special_instructions }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $item->size_name ?? '-' }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>${{ number_format($item->unit_price, 2) }}</td>
                                    <td>${{ number_format($item->total_price, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" class="text-end"><strong>Subtotal:</strong></td>
                                <td>${{ number_format($order->subtotal, 2) }}</td>
                            </tr>
                            @if($order->discount_amount > 0)
                                <tr>
                                    <td colspan="4" class="text-end"><strong>Discount:</strong></td>
                                    <td>-${{ number_format($order->discount_amount, 2) }}</td>
                                </tr>
                            @endif
                            <tr>
                                <td colspan="4" class="text-end"><strong>Delivery Fee:</strong></td>
                                <td>${{ number_format($order->delivery_fee, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="4" class="text-end"><strong>Tax:</strong></td>
                                <td>${{ number_format($order->tax_amount, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="4" class="text-end"><h5>Total:</h5></td>
                                <td><h5>${{ number_format($order->total, 2) }}</h5></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Status History -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Status History</h5>
            </div>
            <div class="card-body">
                <div class="timeline">
                    @foreach($order->statusHistory as $history)
                        <div class="timeline-item pb-3 mb-3 border-bottom">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <span class="badge bg-primary">{{ ucfirst(str_replace('_', ' ', $history->status->value)) }}</span>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="mb-0">Changed by {{ $history->user?->name ?? 'System' }}</p>
                                    <small class="text-muted">{{ $history->created_at->format('M d, Y H:i:s') }}</small>
                                    @if($history->note)
                                        <p class="mb-0 mt-1 text-muted">{{ $history->note }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Customer Info -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Customer</h5>
            </div>
            <div class="card-body">
                <p><strong>{{ $order->user?->name ?? 'Guest' }}</strong></p>
                <p><i data-feather="phone" style="width: 14px; height: 14px;"></i> {{ $order->user?->phone ?? 'N/A' }}</p>
                <p><i data-feather="mail" style="width: 14px; height: 14px;"></i> {{ $order->user?->email ?? 'N/A' }}</p>
            </div>
        </div>

        <!-- Delivery Address -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Delivery Address</h5>
            </div>
            <div class="card-body">
                @if($order->delivery_address)
                    <p>{{ $order->delivery_address['address_line'] ?? '' }}</p>
                    <p>{{ $order->delivery_address['city'] ?? '' }}, {{ $order->delivery_address['postal_code'] ?? '' }}</p>
                    @if($order->delivery_notes)
                        <p class="text-warning"><i data-feather="alert-circle" style="width: 14px; height: 14px;"></i> {{ $order->delivery_notes }}</p>
                    @endif
                @else
                    <p class="text-muted">No address on file</p>
                @endif
            </div>
        </div>

        <!-- Actions -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    @if($order->status->value === 'pending')
                        <form action="{{ route('restaurant.orders.accept', $order) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success w-100">Accept Order</button>
                        </form>
                        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#cancelModal">
                            Cancel Order
                        </button>
                    @elseif($order->status->value === 'accepted')
                        <form action="{{ route('restaurant.orders.prepare', $order) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary w-100">Start Preparing</button>
                        </form>
                    @elseif($order->status->value === 'preparing')
                        <form action="{{ route('restaurant.orders.ready', $order) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success w-100">Mark Ready</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Modal -->
@if($order->status->value === 'pending')
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cancel Order #{{ $order->order_number }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('restaurant.orders.cancel', $order) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Reason for cancellation</label>
                        <textarea name="reason" class="form-control" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger">Cancel Order</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
