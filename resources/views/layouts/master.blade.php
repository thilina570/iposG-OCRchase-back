<!-- resources/views/layouts/master.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    @include('layouts.partials.head')
</head>
<body>
<!-- Header -->
@include('layouts.partials.header')

<div class="layout-container">
    <!-- Sidebar (left menu) -->
    @include('layouts.partials.sidebar')

    <!-- Main content area -->
    <div class="main-content">
        <div class="page-content">
            <!-- Page Title -->
            @yield('page-title')

            <!-- Page Body -->
            <div class="content-body">
                @yield('content')
            </div>
        </div>

        <!-- Footer -->
        @include('layouts.partials.footer')
    </div>
</div>
</body>
</html>
