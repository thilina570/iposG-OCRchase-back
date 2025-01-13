<!-- resources/views/layouts/partials/top-user.blade.php -->
<div class="top-user-bar">
    <ul class="user-info">
        <li>Welcome, {{ Auth::user()->name ?? 'Guest' }}</li>
        <!-- Add logout or profile link if needed -->
    </ul>
</div>
