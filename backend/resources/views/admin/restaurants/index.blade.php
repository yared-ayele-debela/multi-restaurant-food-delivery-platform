@extends('admin.layouts.app')
@section('title')
    Restaurants
@endsection
@section('content')
<div class="container-fluid">
    <x-page-title
        title="Restaurants"
        :breadcrumbs="[
            ['label' => 'Admin', 'url' => route('admin.dashboard')],
            ['label' => 'Restaurants'],
        ]"
    />

    <x-alert />

    <div class="card">
        <div class="card-body">
            <form method="get" action="{{ route('admin.restaurants.index') }}" class="row g-3 align-items-end mb-4">
                <div class="col-md-3">
                    <label class="form-label">Search</label>
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Name, city, slug">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All</option>
                        @foreach($statuses as $st)
                            <option value="{{ $st }}" @selected(request('status') === $st)>{{ ucfirst($st) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Featured</label>
                    <select name="featured" class="form-select">
                        <option value="">All</option>
                        <option value="1" @selected(request('featured') === '1')>Featured only</option>
                        <option value="0" @selected(request('featured') === '0')>Not featured</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('admin.restaurants.index') }}" class="btn btn-soft-secondary">Reset</a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>City</th>
                        <th>Owner</th>
                        <th>Status</th>
                        <th>Active</th>
                        <th>Featured</th>
                        <th style="min-width: 280px;">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($restaurants as $restaurant)
                        <tr>
                            <td>
                                <strong>{{ $restaurant->name }}</strong>
                                <div class="text-muted small">{{ $restaurant->slug }}</div>
                            </td>
                            <td>{{ $restaurant->city }}</td>
                            <td>{{ $restaurant->owner?->email ?? '—' }}</td>
                            <td><span class="badge bg-secondary-subtle text-body">{{ $restaurant->status }}</span></td>
                            <td>{{ $restaurant->is_active ? 'Yes' : 'No' }}</td>
                            <td>{{ $restaurant->is_featured ? 'Yes' : 'No' }}</td>
                            <td>
                                <div class="d-flex flex-wrap gap-1">
                                    <a href="{{ route('admin.restaurants.show', $restaurant) }}" class="btn btn-sm btn-primary">View</a>
                                    @if($restaurant->status !== \App\Models\Restaurant::STATUS_APPROVED)
                                        <form action="{{ route('admin.restaurants.approve', $restaurant) }}" method="post" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                        </form>
                                    @endif
                                    @if($restaurant->status !== \App\Models\Restaurant::STATUS_REJECTED)
                                        <form action="{{ route('admin.restaurants.reject', $restaurant) }}" method="post" class="d-inline" onsubmit="return confirm('Reject this restaurant?');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Reject</button>
                                        </form>
                                    @endif
                                    @if($restaurant->status !== \App\Models\Restaurant::STATUS_SUSPENDED)
                                        <form action="{{ route('admin.restaurants.suspend', $restaurant) }}" method="post" class="d-inline" onsubmit="return confirm('Suspend this restaurant?');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-warning">Suspend</button>
                                        </form>
                                    @endif
                                    <form action="{{ route('admin.restaurants.toggle-featured', $restaurant) }}" method="post" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-soft-primary">
                                            {{ $restaurant->is_featured ? 'Unfeature' : 'Feature' }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No restaurants found.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $restaurants->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
