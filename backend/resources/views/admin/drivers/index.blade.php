@extends('admin.layouts.app')
@section('title')
    Drivers
@endsection
@section('content')
<div class="container-fluid">
    <x-page-title
        title="Drivers"
        :breadcrumbs="[
            ['label' => 'Admin', 'url' => route('admin.dashboard')],
            ['label' => 'Drivers'],
        ]"
    />

    <x-alert />

    <div class="card">
        <div class="card-body">
            <form method="get" action="{{ route('admin.drivers.index') }}" class="row g-3 align-items-end mb-4">
                <div class="col-md-4">
                    <label class="form-label">Search</label>
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Driver name or email">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Approval</label>
                    <select name="approval" class="form-select">
                        <option value="">All</option>
                        <option value="pending" @selected(request('approval') === 'pending')>Pending</option>
                        <option value="approved" @selected(request('approval') === 'approved')>Approved</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('admin.drivers.index') }}" class="btn btn-soft-secondary">Reset</a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                    <tr>
                        <th>Driver</th>
                        <th>Vehicle</th>
                        <th>Approved</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($drivers as $driver)
                        <tr>
                            <td>
                                <strong>{{ $driver->user?->name ?? '—' }}</strong>
                                <div class="text-muted small">{{ $driver->user?->email }}</div>
                            </td>
                            <td>{{ $driver->vehicle_type }} @if($driver->vehicle_number) · {{ $driver->vehicle_number }} @endif</td>
                            <td>{{ $driver->is_approved ? 'Yes' : 'No' }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.drivers.show', $driver) }}" class="btn btn-sm btn-primary">Review</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">No drivers found.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $drivers->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
