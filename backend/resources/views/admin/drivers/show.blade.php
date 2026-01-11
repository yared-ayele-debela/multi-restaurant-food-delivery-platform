@extends('admin.layouts.app')
@section('title')
    Driver — {{ $driver->user?->name }}
@endsection
@section('content')
@php
    $docUrl = function (?string $path) {
        if (empty($path)) {
            return null;
        }
        if (\Illuminate\Support\Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }
        return \Illuminate\Support\Facades\Storage::disk('public')->url($path);
    };
@endphp
<div class="container-fluid">
    <x-page-title
        title="Driver Profile"
        :breadcrumbs="[
            ['label' => 'Admin', 'url' => route('admin.dashboard')],
            ['label' => 'Drivers', 'url' => route('admin.drivers.index')],
            ['label' => $driver->user?->name ?? 'Driver'],
        ]"
    />

    <x-alert />

    <!-- Driver Header Card -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="d-flex align-items-center mb-2">
                                <div class="avatar-lg me-3">
                                    <span class="avatar-title rounded-circle bg-primary text-white font-size-24">
                                        {{ strtoupper(substr($driver->user?->name ?? 'D', 0, 1)) }}
                                    </span>
                                </div>
                                <div>
                                    <h2 class="mb-1">{{ $driver->user?->name ?? 'Unknown Driver' }}</h2>
                                    <p class="text-muted mb-0">{{ $driver->user?->email }}</p>
                                </div>
                            </div>
                            <div class="d-flex gap-2 mt-3">
                                @if($driver->is_approved)
                                    <span class="badge bg-success">Approved</span>
                                @else
                                    <span class="badge bg-warning">Pending Approval</span>
                                @endif
                                @if($driver->is_available)
                                    <span class="badge bg-info">Available</span>
                                @else
                                    <span class="badge bg-secondary">Unavailable</span>
                                @endif
                                @if($driver->is_on_delivery)
                                    <span class="badge bg-primary">On Delivery</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <p class="mb-1"><strong>Phone:</strong> {{ $driver->user?->phone ?? 'N/A' }}</p>
                            <p class="mb-1"><strong>Joined:</strong> {{ $driver->created_at->format('M d, Y') }}</p>
                            <p class="mb-0"><strong>Approved:</strong> {{ $driver->approved_at?->format('M d, Y') ?? 'Not yet' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card card-h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <span class="text-muted mb-3 lh-1 d-block">Total Deliveries</span>
                            <h4 class="mb-3">{{ number_format($stats['total_deliveries']) }}</h4>
                        </div>
                        <div class="col-4 text-end">
                            <div class="avatar-md">
                                <span class="avatar-title rounded-circle bg-primary-subtle text-primary font-size-24">
                                    <i class="mdi mdi-truck-delivery"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card card-h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <span class="text-muted mb-3 lh-1 d-block">Completed</span>
                            <h4 class="mb-3">{{ number_format($stats['completed_deliveries']) }}</h4>
                        </div>
                        <div class="col-4 text-end">
                            <div class="avatar-md">
                                <span class="avatar-title rounded-circle bg-success-subtle text-success font-size-24">
                                    <i class="mdi mdi-check-circle"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card card-h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <span class="text-muted mb-3 lh-1 d-block">Pending</span>
                            <h4 class="mb-3">{{ number_format($stats['pending_deliveries']) }}</h4>
                        </div>
                        <div class="col-4 text-end">
                            <div class="avatar-md">
                                <span class="avatar-title rounded-circle bg-warning-subtle text-warning font-size-24">
                                    <i class="mdi mdi-clock-outline"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card card-h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <span class="text-muted mb-3 lh-1 d-block">Average Rating</span>
                            <h4 class="mb-3">
                                {{ $stats['average_rating'] > 0 ? number_format($stats['average_rating'], 1) : 'N/A' }}
                                @if($stats['average_rating'] > 0)
                                    <small class="text-warning"><i class="mdi mdi-star"></i></small>
                                @endif
                            </h4>
                        </div>
                        <div class="col-4 text-end">
                            <div class="avatar-md">
                                <span class="avatar-title rounded-circle bg-info-subtle text-info font-size-24">
                                    <i class="mdi mdi-star"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Details Row -->
    <div class="row">
        <!-- Vehicle & License Info -->
        <div class="col-xl-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Vehicle Information</h4>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td class="text-muted">Vehicle Type</td>
                            <td>{{ $driver->vehicle_type ?? 'Not specified' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Vehicle Number</td>
                            <td>{{ $driver->vehicle_number ?? 'Not specified' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">License Number</td>
                            <td>{{ $driver->license_number ?? 'Not specified' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Total Ratings</td>
                            <td>{{ $driver->total_ratings }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Current Location -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Current Location</h4>
                </div>
                <div class="card-body">
                    @if($driver->current_latitude && $driver->current_longitude)
                        <p class="mb-2"><i class="mdi mdi-map-marker me-1 text-primary"></i> 
                            Lat: {{ $driver->current_latitude }}, Lng: {{ $driver->current_longitude }}
                        </p>
                        <a href="https://www.google.com/maps?q={{ $driver->current_latitude }},{{ $driver->current_longitude }}" 
                           target="_blank" class="btn btn-sm btn-soft-primary">
                            <i class="mdi mdi-map me-1"></i> View on Map
                        </a>
                    @else
                        <div class="text-center py-3">
                            <i class="mdi mdi-crosshairs-gps font-size-24 text-muted"></i>
                            <p class="text-muted mb-0 mt-2">Location not available</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Documents -->
        <div class="col-xl-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Documents</h4>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="text-muted">License Image</h6>
                        @if($url = $docUrl($driver->license_image))
                            <div class="border rounded p-2 mb-2">
                                <img src="{{ $url }}" alt="License" class="img-fluid rounded" style="max-height: 150px;">
                            </div>
                            <a href="{{ $url }}" target="_blank" rel="noopener" class="btn btn-sm btn-soft-primary">
                                <i class="mdi mdi-open-in-new me-1"></i> Open Full Size
                            </a>
                        @else
                            <div class="border rounded p-3 text-center">
                                <i class="mdi mdi-file-image-outline font-size-24 text-muted"></i>
                                <p class="text-muted mb-0 mt-1">Not uploaded</p>
                            </div>
                        @endif
                    </div>

                    <div class="mt-4">
                        <h6 class="text-muted">Insurance Document</h6>
                        @if($url = $docUrl($driver->insurance_document))
                            <div class="border rounded p-2 mb-2">
                                <img src="{{ $url }}" alt="Insurance" class="img-fluid rounded" style="max-height: 150px;">
                            </div>
                            <a href="{{ $url }}" target="_blank" rel="noopener" class="btn btn-sm btn-soft-primary">
                                <i class="mdi mdi-open-in-new me-1"></i> Open Full Size
                            </a>
                        @else
                            <div class="border rounded p-3 text-center">
                                <i class="mdi mdi-file-document-outline font-size-24 text-muted"></i>
                                <p class="text-muted mb-0 mt-1">Not uploaded</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Deliveries -->
        <div class="col-xl-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Recent Deliveries</h4>
                    <span class="badge bg-primary">{{ $stats['total_deliveries'] }} total</span>
                </div>
                <div class="card-body">
                    @if($driver->deliveries->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>Order #</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($driver->deliveries as $delivery)
                                        <tr>
                                            <td><strong>#{{ $delivery->order?->order_number ?? 'N/A' }}</strong></td>
                                            <td>
                                                @php
                                                $statusColors = [
                                                    'pending' => 'warning',
                                                    'assigned' => 'info',
                                                    'picked_up' => 'primary',
                                                    'in_transit' => 'info',
                                                    'delivered' => 'success',
                                                    'cancelled' => 'danger',
                                                ];
                                                @endphp
                                                <span class="badge bg-{{ $statusColors[$delivery->status] ?? 'secondary' }}">
                                                    {{ ucfirst(str_replace('_', ' ', $delivery->status)) }}
                                                </span>
                                            </td>
                                            <td>{{ $delivery->created_at->format('M d, H:i') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="mdi mdi-truck-off font-size-48 text-muted"></i>
                            <p class="text-muted mb-0 mt-2">No deliveries yet</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.drivers.index') }}" class="btn btn-secondary">
                            <i class="mdi mdi-arrow-left me-1"></i> Back to List
                        </a>
                        @if(! $driver->is_approved)
                            <form action="{{ route('admin.drivers.approve', $driver) }}" method="post">
                                @csrf
                                <button type="submit" class="btn btn-success">
                                    <i class="mdi mdi-check me-1"></i> Approve Driver
                                </button>
                            </form>
                        @else
                            <form action="{{ route('admin.drivers.reject', $driver) }}" method="post" onsubmit="return confirm('Remove approval for this driver?');">
                                @csrf
                                <button type="submit" class="btn btn-outline-warning">
                                    <i class="mdi mdi-close me-1"></i> Remove Approval
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
