@extends('layouts.app')

@section('title', 'Home - Dan & Brian Reviews')

@section('content')
    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1 style="font-family: 'Share Tech Mono', monospace; font-size: 3rem; margin-bottom: 1rem; color: #E53E3E; text-shadow: 0 0 20px #E53E3E;">WELCOME TO THE ULTIMATE GAMING HUB</h1>
            <p style="font-size: 1.2rem; color: #A1A1AA; margin-bottom: 2rem; max-width: 600px; margin-left: auto; margin-right: auto;">Discover honest reviews, epic podcasts, live streams, and in-depth articles from Dan & Brian. Your go-to destination for everything gaming.</p>
            <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                <a href="#" class="btn btn-primary">Explore Reviews</a>
                <a href="#" class="btn btn-secondary">Watch Streams</a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="section">
        <div class="container">
            <h2 class="section-title">WHAT WE OFFER</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">🎮</div>
                    <h3 class="feature-title">GAME REVIEWS</h3>
                    <p class="feature-description">Honest, detailed reviews of the latest games. No BS, just real opinions from gamers who actually play.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">🎧</div>
                    <h3 class="feature-title">PODCASTS</h3>
                    <p class="feature-description">Weekly gaming discussions, industry insights, and hilarious banter. Your commute just got better.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">📺</div>
                    <h3 class="feature-title">LIVE STREAMS</h3>
                    <p class="feature-description">Join us live for gaming sessions, reviews in real-time, and interactive community fun.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">📝</div>
                    <h3 class="feature-title">ARTICLES</h3>
                    <p class="feature-description">In-depth analysis, gaming news, and thought-provoking content about the industry we love.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">👥</div>
                    <h3 class="feature-title">COMMUNITY</h3>
                    <p class="feature-description">Connect with fellow gamers, share your thoughts, and be part of our growing community.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">⭐</div>
                    <h3 class="feature-title">RATINGS</h3>
                    <p class="feature-description">Rate games, see community scores, and discover what's worth your time and money.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="section" style="background: #27272A; text-align: center;">
        <div class="container">
            <h2 class="section-title">READY TO DIVE IN?</h2>
            <p style="color: #A1A1AA; margin-bottom: 2rem; font-size: 1.1rem;">
                Join thousands of gamers who trust Dan & Brian for their gaming decisions.
            </p>
            <a href="#" class="btn btn-primary">Get Started</a>
        </div>
    </section>
@endsection 