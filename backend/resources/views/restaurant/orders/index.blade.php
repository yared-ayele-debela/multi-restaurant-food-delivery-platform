@extends('restaurant.layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">All Orders</h4>
            <div class="page-title-right d-flex gap-2">
                <a href="{{ route('restaurant.orders.board') }}" class="btn btn-primary">
                    <i data-feather="layout" class="me-1" style="width: 16px; height: 16px;"></i>
                    Board View
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <!-- Status Counts -->
                <div class="row mb-4">
                    @foreach(['pending' => 'warning', 'accepted' => 'info', 'preparing' => 'primary', 'ready' => 'success', 'picked_up' => 'secondary', 'delivered' => 'success', 'completed' => 'dark'] as $status => $color)
                        <div class="col-md-2 col-4 mb-2">
                            <a href="{{ route('restaurant.orders.index', ['status' => $status]) }}" class="text-decoration-none">
                                <div class="card bg-{{ $color }} bg-opacity-10">
                                    <div class="card-body p-2 text-center">
                                        <h5 class="mb-0">{{ $statusCounts[$status] ?? 0 }}</h5>
                                        <small class="text-muted text-capitalize">{{ str_replace('_', ' ', $status) }}</small>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>

                <!-- Filters -->
                <form method="GET" class="row g-3 mb-4">
                    <div class="col-md-2">
                        <input type="date" name="date" class="form-control" value="{{ request('date', today()->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            @foreach($statuses as $status)
                                <option value="{{ $status->value }}" {{ request('status') == $status->value ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $status->value)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="q" class="form-control" placeholder="Search order number..." value="{{ request('q') }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-secondary w-100">Filter</button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('restaurant.orders.index') }}" class="btn btn-outline-secondary w-100">Clear</a>
                    </div>
                </form>

                <!-- Orders Table -->
                <div class="table-responsive">
                    <table class="table table-centered table-nowrap mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Order #</th>
                                <th>Customer</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Time</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                                <tr>
                                    <td>
                                        <strong>#{{ $order->order_number }}</strong>
                                        <br><small class="text-muted">{{ $order->orderItems->count() }} items</small>
                                    </td>
                                    <td>{{ $order->user?->name ?? 'Guest' }}</td>
                                    <td>${{ number_format($order->total, 2) }}</td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'pending' => 'warning',
                                                'accepted' => 'info',
                                                'preparing' => 'primary',
                                                'ready' => 'success',
                                                'picked_up' => 'secondary',
                                                'on_the_way' => 'info',
                                                'delivered' => 'success',
                                                'completed' => 'dark',
                                                'cancelled' => 'danger',
                                                'refunded' => 'danger',
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $statusColors[$order->status->value] ?? 'secondary' }}">
                                            {{ ucfirst(str_replace('_', ' ', $order->status->value)) }}
                                        </span>
                                    </td>
                                    <td>{{ $order->created_at->format('M d, H:i') }}</td>
                                    <td>
                                        <a href="{{ route('restaurant.orders.show', $order) }}" class="btn btn-sm btn-primary">
                                            <i data-feather="eye" style="width: 14px; height: 14px;"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <p class="text-muted mb-0">No orders found</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $orders->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
