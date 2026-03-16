@extends('restaurant.layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Branches</h4>
            <div class="page-title-right">
                <a href="{{ route('restaurant.branches.create') }}" class="btn btn-primary">
                    <i data-feather="plus" class="me-1" style="width: 16px; height: 16px;"></i>
                    Add Branch
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-centered table-nowrap mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Address</th>
                                <th>Phone</th>
                                <th>Delivery Radius</th>
                                <th>Prep Time</th>
                                <th>Status</th>
                                <th style="width: 150px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($branches as $branch)
                                <tr>
                                    <td><strong>{{ $branch->name }}</strong></td>
                                    <td>{{ $branch->address }}, {{ $branch->city }}</td>
                                    <td>{{ $branch->phone ?? '-' }}</td>
                                    <td>{{ $branch->delivery_radius ? $branch->delivery_radius.' km' : '-' }}</td>
                                    <td>{{ $branch->preparation_time ? $branch->preparation_time.' min' : '-' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $branch->is_active ? 'success' : 'secondary' }}">
                                            {{ $branch->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('restaurant.branches.edit', $branch) }}" class="btn btn-sm btn-primary">
                                                <i data-feather="edit-2" style="width: 14px; height: 14px;"></i>
                                            </a>
                                            <form action="{{ route('restaurant.branches.destroy', $branch) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this branch?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i data-feather="trash-2" style="width: 14px; height: 14px;"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <p class="text-muted mb-0">No branches found</p>
                                        <a href="{{ route('restaurant.branches.create') }}" class="btn btn-primary mt-2">Add your first branch</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $branches->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
