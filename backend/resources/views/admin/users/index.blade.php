@extends('admin.layouts.app')
@section('title')
    Users
@endsection
@section('content')
<div class="container-fluid">
    <x-page-title
        title="Users"
        :breadcrumbs="[
            ['label' => 'Admin', 'url' => route('admin.dashboard')],
            ['label' => 'Users'],
        ]"
    />
    @can('create users')
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary mb-3">Create User</a>
    @endcan
    <x-alert />

    <div class="card">
        <div class="card-body">
            <form method="get" action="{{ route('admin.users.index') }}" class="row g-3 align-items-end mb-4">
                <div class="col-md-4">
                    <label class="form-label">Search</label>
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Name or email">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All</option>
                        <option value="{{ \App\Models\User::STATUS_ACTIVE }}" @selected(request('status') === \App\Models\User::STATUS_ACTIVE)>Active</option>
                        <option value="{{ \App\Models\User::STATUS_SUSPENDED }}" @selected(request('status') === \App\Models\User::STATUS_SUSPENDED)>Suspended</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-soft-secondary">Reset</a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Roles</th>
                        <th style="min-width: 220px;">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <span class="badge bg-secondary-subtle text-body">{{ $user->status }}</span>
                            </td>
                            <td>
                                @foreach($user->roles as $role)
                                    <span class="badge bg-info">{{ $role->name }}</span>
                                @endforeach
                            </td>
                            <td>
                                <a href="{{ route('admin.users.edit', $user->id) }}"
                                   class="btn btn-sm btn-warning">Edit</a>

                                @if($user->status === \App\Models\User::STATUS_ACTIVE)
                                    @if(! $user->hasRole('super-admin'))
                                        <form action="{{ route('admin.users.suspend', $user) }}" method="post" class="d-inline" onsubmit="return confirm('Suspend this user?');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Suspend</button>
                                        </form>
                                    @endif
                                @else
                                    <form action="{{ route('admin.users.activate', $user) }}" method="post" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success">Activate</button>
                                    </form>
                                @endif

                                <form action="{{ route('admin.users.destroy', $user->id) }}"
                                      method="POST"
                                      class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger"
                                            onclick="return confirm('Delete this user?')">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
