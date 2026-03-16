@extends('admin.layouts.app')
@section('title')
    Withdrawals
@endsection
@section('content')
<div class="container-fluid">
    <x-page-title
        title="Withdrawal requests"
        :breadcrumbs="[
            ['label' => 'Admin', 'url' => route('admin.dashboard')],
            ['label' => 'Withdrawals'],
        ]"
    />

    <x-alert />

    <div class="card">
        <div class="card-body">
            <form method="get" action="{{ route('admin.withdrawals.index') }}" class="row g-3 align-items-end mb-4">
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All</option>
                        <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                        <option value="completed" @selected(request('status') === 'completed')>Completed</option>
                        <option value="rejected" @selected(request('status') === 'rejected')>Rejected</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('admin.withdrawals.index') }}" class="btn btn-soft-secondary">Reset</a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Wallet / holder</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Bank</th>
                        <th>Processed by</th>
                        <th style="min-width: 220px;">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($withdrawals as $w)
                        @php
                            $holder = $w->wallet?->holder;
                            $holderLabel = $holder
                                ? (\Illuminate\Support\Str::afterLast($w->wallet->holder_type, '\\') === 'Restaurant'
                                    ? $holder->name
                                    : ('#'.$holder->id))
                                : '—';
                        @endphp
                        <tr>
                            <td>#{{ $w->id }}</td>
                            <td>
                                <div>{{ $holderLabel }}</div>
                                <div class="text-muted small">Wallet #{{ $w->wallet_id }}</div>
                            </td>
                            <td>{{ $w->amount }} {{ $w->wallet?->currency ?? '' }}</td>
                            <td><span class="badge bg-secondary-subtle text-body">{{ $w->status }}</span></td>
                            <td class="small">{{ $w->bank_name ?? '—' }}</td>
                            <td class="small">{{ $w->processedBy?->email ?? '—' }}</td>
                            <td>
                                @if($w->status === 'pending')
                                    <form action="{{ route('admin.withdrawals.complete', $w) }}" method="post" class="mb-2">
                                        @csrf
                                        <label class="form-label small mb-0">Complete</label>
                                        <input type="text" name="admin_notes" class="form-control form-control-sm mb-1" placeholder="Admin notes (optional)" maxlength="5000">
                                        <button type="submit" class="btn btn-sm btn-success">Approve payout</button>
                                    </form>
                                    <form action="{{ route('admin.withdrawals.reject', $w) }}" method="post">
                                        @csrf
                                        <label class="form-label small mb-0">Reject</label>
                                        <input type="text" name="rejection_reason" class="form-control form-control-sm mb-1" placeholder="Reason (required)" required maxlength="2000">
                                        <input type="text" name="admin_notes" class="form-control form-control-sm mb-1" placeholder="Admin notes (optional)" maxlength="5000">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Reject</button>
                                    </form>
                                @else
                                    <span class="text-muted small">—</span>
                                    @if($w->admin_notes)
                                        <div class="small text-muted mt-1">Notes: {{ \Illuminate\Support\Str::limit($w->admin_notes, 80) }}</div>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No withdrawal requests.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $withdrawals->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
