@extends('restaurant.layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Orders Board</h4>
            <div class="page-title-right d-flex gap-2">
                <a href="{{ route('restaurant.orders.index') }}" class="btn btn-secondary">
                    <i data-feather="list" class="me-1" style="width: 16px; height: 16px;"></i>
                    List View
                </a>
                <button type="button" class="btn btn-light" onclick="refreshBoard()" id="refreshBtn">
                    <i data-feather="refresh-cw" class="me-1" style="width: 16px; height: 16px;"></i>
                    Refresh
                </button>
            </div>
        </div>
    </div>
</div>

<div class="row kanban-board">
    <!-- Pending -->
    <div class="col-md-3">
        <div class="card">
            <div class="card-header bg-warning bg-opacity-10">
                <h5 class="card-title mb-0 d-flex justify-content-between align-items-center">
                    <span><i data-feather="clock" class="me-1" style="width: 16px; height: 16px;"></i> Pending</span>
                    <span class="badge bg-warning" id="count-pending">{{ $pendingOrders->count() }}</span>
                </h5>
            </div>
            <div class="card-body p-2" style="min-height: 400px; max-height: 600px; overflow-y: auto;">
                @foreach($pendingOrders as $order)
                    <div class="card mb-2 border-warning">
                        <div class="card-body p-2">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <strong>#{{ $order->order_number }}</strong>
                                <small class="text-muted">{{ $order->created_at->diffForHumans() }}</small>
                            </div>
                            <p class="mb-1"><strong>{{ $order->user?->name ?? 'Guest' }}</strong></p>
                            <p class="mb-1">${{ number_format($order->total, 2) }}</p>
                            <p class="mb-2"><small>{{ $order->orderItems->count() }} items</small></p>
                            <div class="d-grid gap-1">
                                <form action="{{ route('restaurant.orders.accept', $order) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success w-100">Accept</button>
                                </form>
                                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#cancelModal{{ $order->id }}">
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Cancel Modal -->
                    <div class="modal fade" id="cancelModal{{ $order->id }}" tabindex="-1">
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
                @endforeach
            </div>
        </div>
    </div>

    <!-- Accepted -->
    <div class="col-md-3">
        <div class="card">
            <div class="card-header bg-info bg-opacity-10">
                <h5 class="card-title mb-0 d-flex justify-content-between align-items-center">
                    <span><i data-feather="check-circle" class="me-1" style="width: 16px; height: 16px;"></i> Accepted</span>
                    <span class="badge bg-info" id="count-accepted">{{ $acceptedOrders->count() }}</span>
                </h5>
            </div>
            <div class="card-body p-2" style="min-height: 400px; max-height: 600px; overflow-y: auto;">
                @foreach($acceptedOrders as $order)
                    <div class="card mb-2 border-info">
                        <div class="card-body p-2">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <strong>#{{ $order->order_number }}</strong>
                                <small class="text-muted">{{ $order->accepted_at?->diffForHumans() ?? 'Just now' }}</small>
                            </div>
                            <p class="mb-1"><strong>{{ $order->user?->name ?? 'Guest' }}</strong></p>
                            <p class="mb-1">${{ number_format($order->total, 2) }}</p>
                            <div class="d-grid">
                                <form action="{{ route('restaurant.orders.prepare', $order) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-primary w-100">Start Preparing</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Preparing -->
    <div class="col-md-3">
        <div class="card">
            <div class="card-header bg-primary bg-opacity-10">
                <h5 class="card-title mb-0 d-flex justify-content-between align-items-center">
                    <span><i data-feather="loader" class="me-1" style="width: 16px; height: 16px;"></i> Preparing</span>
                    <span class="badge bg-primary" id="count-preparing">{{ $preparingOrders->count() }}</span>
                </h5>
            </div>
            <div class="card-body p-2" style="min-height: 400px; max-height: 600px; overflow-y: auto;">
                @foreach($preparingOrders as $order)
                    <div class="card mb-2 border-primary">
                        <div class="card-body p-2">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <strong>#{{ $order->order_number }}</strong>
                                <small class="text-muted">{{ $order->preparing_at?->diffForHumans() ?? 'Just now' }}</small>
                            </div>
                            <p class="mb-1"><strong>{{ $order->user?->name ?? 'Guest' }}</strong></p>
                            <p class="mb-1">${{ number_format($order->total, 2) }}</p>
                            <div class="d-grid">
                                <form action="{{ route('restaurant.orders.ready', $order) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success w-100">Mark Ready</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Ready -->
    <div class="col-md-3">
        <div class="card">
            <div class="card-header bg-success bg-opacity-10">
                <h5 class="card-title mb-0 d-flex justify-content-between align-items-center">
                    <span><i data-feather="package" class="me-1" style="width: 16px; height: 16px;"></i> Ready</span>
                    <span class="badge bg-success" id="count-ready">{{ $readyOrders->count() }}</span>
                </h5>
            </div>
            <div class="card-body p-2" style="min-height: 400px; max-height: 600px; overflow-y: auto;">
                @foreach($readyOrders as $order)
                    <div class="card mb-2 border-success">
                        <div class="card-body p-2">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <strong>#{{ $order->order_number }}</strong>
                                <small class="text-muted">{{ $order->ready_at?->diffForHumans() ?? 'Just now' }}</small>
                            </div>
                            <p class="mb-1"><strong>{{ $order->user?->name ?? 'Guest' }}</strong></p>
                            <p class="mb-1">${{ number_format($order->total, 2) }}</p>
                            @if($order->delivery?->driver)
                                <p class="mb-0 text-success">
                                    <small><i data-feather="user" style="width: 12px; height: 12px;"></i> {{ $order->delivery->driver->user->name }}</small>
                                </p>
                            @else
                                <p class="mb-0 text-warning"><small>Waiting for driver</small></p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script>
function refreshBoard() {
    const btn = document.getElementById('refreshBtn');
    btn.disabled = true;
    btn.innerHTML = '<i data-feather="loader" class="me-1" style="width: 16px; height: 16px;"></i> Loading...';
    
    fetch('{{ route('restaurant.orders.refresh') }}')
        .then(response => response.json())
        .then(data => {
            // Update counts
            document.getElementById('count-pending').textContent = data.counts['pending'] || 0;
            document.getElementById('count-accepted').textContent = data.counts['accepted'] || 0;
            document.getElementById('count-preparing').textContent = data.counts['preparing'] || 0;
            document.getElementById('count-ready').textContent = data.counts['ready'] || 0;
            
            // Reload page if counts changed significantly
            location.reload();
        })
        .catch(error => {
            console.error('Error:', error);
            btn.disabled = false;
            btn.innerHTML = '<i data-feather="refresh-cw" class="me-1" style="width: 16px; height: 16px;"></i> Refresh';
            feather.replace();
        });
}

// Auto-refresh every 30 seconds
setInterval(refreshBoard, 30000);
</script>
@endsection
