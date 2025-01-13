<!-- resources/views/layouts/partials/sidebar.blade.php -->
<aside class="sidebar">
    <nav class="nav-menu">
        <ul>
            <li><a href="{{ route('dashboard') }}"> <i class="fas fa-home"></i> Home</a></li>
            <!-- Add more links to your routes as needed -->
            <li><a href="{{ route('purchaseInvoice') }}"><i class="fa-solid fa-file-invoice"></i> Purchase Invoice</a></li>
            <li><a href="#"><i class="fa-solid fa-user"></i> User</a></li>
        </ul>
    </nav>
</aside>
