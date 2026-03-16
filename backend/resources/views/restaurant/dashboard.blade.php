@extends('restaurant.layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Dashboard</h4>
        </div>
    </div>
</div>

<!-- Welcome Banner -->
<div class="row">
    <div class="col-12">
        <div class="alert alert-info">
            <h5 class="alert-heading">Welcome to {{ $restaurant->name }}!</h5>
            <p class="mb-0">Manage your restaurant menu, track orders, and view business analytics from this dashboard.</p>
        </div>
    </div>
</div>

<!-- Today's Stats -->
<div class="row">
    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h4 class="mb-2">{{ $todayStats['orders_count'] }}</h4>
                        <p class="text-muted mb-0">Today's Orders</p>
                    </div>
                    <div class="flex-shrink-0 align-self-center">
                        <div class="avatar-sm rounded-circle bg-primary mini-stat-icon">
                            <span class="avatar-title rounded-circle bg-primary">
                                <i data-feather="shopping-bag"></i>
                            </span>
                        </div>
                    </div>
                </div>
                @if($todayStats['orders_change'] != 0)
                    <div class="mt-3">
                        <span class="badge {{ $todayStats['orders_change'] > 0 ? 'bg-success' : 'bg-danger' }}">
                            {{ $todayStats['orders_change'] > 0 ? '+' : '' }}{{ $todayStats['orders_change'] }}
                        </span>
                        <span class="text-muted"> vs yesterday</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h4 class="mb-2">${{ number_format($todayStats['revenue'], 2) }}</h4>
                        <p class="text-muted mb-0">Today's Revenue</p>
                    </div>
                    <div class="flex-shrink-0 align-self-center">
                        <div class="avatar-sm rounded-circle bg-success mini-stat-icon">
                            <span class="avatar-title rounded-circle bg-success">
                                <i data-feather="dollar-sign"></i>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="badge {{ $todayStats['revenue_change'] >= 0 ? 'bg-success' : 'bg-danger' }}">
                        {{ $todayStats['revenue_change'] >= 0 ? '+' : '' }}{{ $todayStats['revenue_change'] }}%
                    </span>
                    <span class="text-muted"> vs yesterday</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h4 class="mb-2">{{ $todayStats['pending_orders'] }}</h4>
                        <p class="text-muted mb-0">Pending Orders</p>
                    </div>
                    <div class="flex-shrink-0 align-self-center">
                        <div class="avatar-sm rounded-circle bg-warning mini-stat-icon">
                            <span class="avatar-title rounded-circle bg-warning">
                                <i data-feather="clock"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h4 class="mb-2">{{ $menuStats['active_products'] }}</h4>
                        <p class="text-muted mb-0">Active Products</p>
                    </div>
                    <div class="flex-shrink-0 align-self-center">
                        <div class="avatar-sm rounded-circle bg-info mini-stat-icon">
                            <span class="avatar-title rounded-circle bg-info">
                                <i data-feather="package"></i>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-muted">{{ $menuStats['categories'] }} categories</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Menu Stats -->
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Menu Overview</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="border-end">
                            <h5>{{ $menuStats['categories'] }}</h5>
                            <p class="text-muted mb-0">Categories</p>
                            <a href="{{ route('restaurant.categories.index') }}" class="btn btn-sm btn-outline-primary mt-2">Manage</a>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border-end">
                            <h5>{{ $menuStats['products'] }}</h5>
                            <p class="text-muted mb-0">Total Products</p>
                            <a href="{{ route('restaurant.products.index') }}" class="btn btn-sm btn-outline-primary mt-2">Manage</a>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div>
                            <h5>{{ $menuStats['active_products'] }}</h5>
                            <p class="text-muted mb-0">Active Products</p>
                            <span class="text-success">
                                {{ $menuStats['products'] > 0 ? round(($menuStats['active_products'] / $menuStats['products']) * 100, 1) : 0 }}% active
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Recent Orders</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-centered table-nowrap mb-0">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $order)
                                <tr>
                                    <td>#{{ $order->id }}</td>
                                    <td>{{ $order->user->name ?? 'Guest' }}</td>
                                    <td>${{ number_format($order->total_amount, 2) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $order->status->value === 'completed' ? 'success' : ($order->status->value === 'pending' ? 'warning' : 'info') }}">
                                            {{ ucfirst($order->status->value) }}
                                        </span>
                                    </td>
                                    <td>{{ $order->created_at->format('M d, H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">No orders yet</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Quick Actions</h4>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('restaurant.categories.create') }}" class="btn btn-primary">
                        <i data-feather="folder-plus" class="me-1"></i> Add New Category
                    </a>
                    <a href="{{ route('restaurant.products.create') }}" class="btn btn-success">
                        <i data-feather="plus-circle" class="me-1"></i> Add New Product
                    </a>
                    <a href="#" class="btn btn-outline-secondary">
                        <i data-feather="shopping-cart" class="me-1"></i> View All Orders
                    </a>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Restaurant Info</h4>
            </div>
            <div class="card-body">
                <p><strong>Name:</strong> {{ $restaurant->name }}</p>
                <p><strong>Status:</strong>
                    <span class="badge bg-{{ $restaurant->is_active && $restaurant->status === 'approved' ? 'success' : 'warning' }}">
                        {{ ucfirst($restaurant->status) }}
                    </span>
                </p>
                <p><strong>Address:</strong> {{ $restaurant->address_line }}, {{ $restaurant->city }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
