@extends('restaurant.layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Edit Operating Hours</h4>
            <div class="page-title-right">
                <a href="{{ route('restaurant.hours.index') }}" class="btn btn-secondary">
                    <i data-feather="arrow-left" class="me-1" style="width: 16px; height: 16px;"></i>
                    Back to Hours
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('restaurant.hours.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    @foreach($days as $dayNum => $dayName)
                        @php
                            $hour = $hours[$dayNum] ?? null;
                            $isClosed = $hour?->is_closed ?? false;
                        @endphp
                        <div class="row align-items-center mb-3 pb-3 border-bottom">
                            <div class="col-md-2">
                                <strong>{{ $dayName }}</strong>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="closed_{{ $dayNum }}" name="days[{{ $dayNum }}][is_closed]" value="1" {{ $isClosed ? 'checked' : '' }} onchange="toggleHours({{ $dayNum }})">
                                    <label class="form-check-label" for="closed_{{ $dayNum }}">Closed</label>
                                </div>
                            </div>
                            <div class="col-md-3 hours-input-{{ $dayNum }}" style="{{ $isClosed ? 'display: none;' : '' }}">
                                <input type="time" class="form-control" name="days[{{ $dayNum }}][open_time]" value="{{ old('days.'.$dayNum.'.open_time', $hour?->open_time ?? '09:00') }}">
                            </div>
                            <div class="col-md-3 hours-input-{{ $dayNum }}" style="{{ $isClosed ? 'display: none;' : '' }}">
                                <input type="time" class="form-control" name="days[{{ $dayNum }}][close_time]" value="{{ old('days.'.$dayNum.'.close_time', $hour?->close_time ?? '22:00') }}">
                            </div>
                        </div>
                    @endforeach

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i data-feather="save" class="me-1" style="width: 16px; height: 16px;"></i>
                            Save Hours
                        </button>
                        <a href="{{ route('restaurant.hours.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function toggleHours(day) {
    const isClosed = document.getElementById('closed_' + day).checked;
    const inputs = document.querySelectorAll('.hours-input-' + day);
    inputs.forEach(input => {
        input.style.display = isClosed ? 'none' : 'block';
    });
}
</script>
@endsection
