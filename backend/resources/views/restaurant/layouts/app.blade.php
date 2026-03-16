@include('admin.layouts.css')
<div id="layout-wrapper">
    @include('admin.partials.navbar')
    @include('restaurant.partials.sidebar')

    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
        <!-- End Page-content -->
        @include('admin.partials.footer')
    </div>
</div>

@include('admin.layouts.javascript')
