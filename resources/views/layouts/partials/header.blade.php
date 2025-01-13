<!-- resources/views/layouts/partials/header.blade.php -->
<header class="header">
    <div class="header-left">
        <div class="logo">
            <a href="{{ url('/') }}">
                <img src="https://iposg.com/en-gb/assets/images/logo.png" width="75px">
            </a>
        </div>
    </div>

    <div class="header-right">
        <!-- If you have user authentication, you can show the user name -->
        @if(Auth::check())
            <span class="user-name">{{ Auth::user()->name }}</span>
        @else
            <span class="user-name">Guest</span>
        @endif
    </div>
</header>
