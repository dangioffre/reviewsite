<!-- Header -->
<header class="header">
    <div class="container">
        <nav class="nav">
            <a href="{{ route('home') }}" class="logo">DAN & BRIAN <span>REVIEWS</span></a>
            
            <div class="search-bar">
                <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" placeholder="Search games, articles..." class="search-input">
            </div>

            <ul class="nav-links">
                <li><a href="#">Reviews</a></li>
                <li><a href="#">Podcasts</a></li>
                <li><a href="#">Streams</a></li>
                <li><a href="#">Articles</a></li>
            </ul>

            <div class="nav-actions">
                @auth
                    <a href="#" class="btn btn-primary">Dashboard</a>
                @else
                    <a href="#" class="btn btn-secondary" style="font-size: 0.9rem; padding: 0.5rem 1rem;">Login</a>
                    <a href="#" class="btn btn-primary" style="font-size: 0.9rem; padding: 0.5rem 1rem;">Register</a>
                @endauth
            </div>
        </nav>
    </div>
</header> 