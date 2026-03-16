@extends('restaurant.layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Operating Hours</h4>
            <div class="page-title-right">
                <a href="{{ route('restaurant.hours.edit') }}" class="btn btn-primary">
                    <i data-feather="edit-2" class="me-1" style="width: 16px; height: 16px;"></i>
                    Edit Hours
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-centered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Day</th>
                                <th>Status</th>
                                <th>Open Time</th>
                                <th>Close Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($days as $dayNum => $dayName)
                                @php
                                    $hour = $hours[$dayNum] ?? null;
                                @endphp
                                <tr>
                                    <td><strong>{{ $dayName }}</strong></td>
                                    <td>
                                        @if($hour && $hour->is_closed)
                                            <span class="badge bg-secondary">Closed</span>
                                        @else
                                            <span class="badge bg-success">Open</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($hour && !$hour->is_closed)
                                            {{ $hour->open_time }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if($hour && !$hour->is_closed)
                                            {{ $hour->close_time }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
