<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ $title ?? 'Dan & Brian Reviews' }}</title>
        
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Share+Tech+Mono:wght@400&family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
        
        <!-- Livewire Styles -->
        @livewireStyles
        
        <!-- Styles -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <style>
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                }
                
                body {
                    background: #1A1A1B;
                    font-family: 'Inter', sans-serif;
                    color: #FFFFFF;
                    font-size: 16px;
                    line-height: 1.6;
                }
                
                .container {
                    max-width: 1200px;
                    margin: 0 auto;
                    padding: 0 1rem;
                }
                
                /* Header */
                .header {
                    background: #1A1A1B;
                    border-bottom: 1px solid #3F3F46;
                    padding: 1rem 0;
                    position: sticky;
                    top: 0;
                    z-index: 10;
                }
                
                .nav {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    gap: 1.5rem;
                }
                
                .logo {
                    font-family: 'Share Tech Mono', monospace;
                    font-size: 1.5rem;
                    color: #FFFFFF;
                    text-decoration: none;
                    font-weight: 700;
                    letter-spacing: 1px;
                }

                .logo span {
                    color: #E53E3E;
                }
                
                .search-bar {
                    flex-grow: 1;
                    max-width: 400px;
                    position: relative;
                }

                .search-input {
                    width: 100%;
                    padding: 0.6rem 1rem;
                    padding-left: 2.5rem;
                    background: #27272A;
                    border: 1px solid #3F3F46;
                    border-radius: 8px;
                    color: #FFFFFF;
                    font-family: 'Inter', sans-serif;
                    font-size: 1rem;
                }

                .search-input:focus {
                    outline: none;
                    border-color: #2563EB;
                    box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.4);
                }

                .search-icon {
                    position: absolute;
                    left: 0.75rem;
                    top: 50%;
                    transform: translateY(-50%);
                    color: #A1A1AA;
                    width: 20px;
                    height: 20px;
                }

                .nav-links {
                    display: flex;
                    gap: 1.5rem;
                    list-style: none;
                    margin-left: auto;
                }
                
                .nav-links a {
                    color: #A1A1AA;
                    text-decoration: none;
                    font-weight: 600;
                    transition: color 0.3s ease;
                    font-size: 0.9rem;
                    text-transform: uppercase;
                }
                
                .nav-links a:hover {
                    color: #FFFFFF;
                }
                
                .nav-actions {
                    display: flex;
                    align-items: center;
                    gap: 1rem;
                }

                /* Buttons */
                .btn {
                    display: inline-block;
                    padding: 0.75rem 1.5rem;
                    border-radius: 8px;
                    text-decoration: none;
                    font-weight: 600;
                    transition: all 0.3s ease;
                    border: none;
                    cursor: pointer;
                    font-size: 1rem;
                }
                
                .btn-primary {
                    background: #E53E3E;
                    color: #FFFFFF;
                }
                
                .btn-primary:hover {
                    background: #DC2626;
                }
                
                .btn-secondary {
                    background: #27272A;
                    color: #FFFFFF;
                    border: 2px solid #E53E3E;
                }
                
                .btn-secondary:hover {
                    background: rgba(229, 62, 62, 0.1);
                }
                
                /* Content Sections */
                .section {
                    padding: 4rem 0;
                }
                
                .section-title {
                    font-family: 'Share Tech Mono', monospace;
                    font-size: 2rem;
                    color: #E53E3E;
                    margin-bottom: 2rem;
                    text-align: center;
                }
                
                .features-grid {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
                    gap: 2rem;
                    margin-top: 3rem;
                }
                
                .feature-card {
                    background: #27272A;
                    padding: 2rem;
                    border-radius: 8px;
                    border: 1px solid #3F3F46;
                    transition: all 0.3s ease;
                }
                
                .feature-card:hover {
                    border-color: #E53E3E;
                    box-shadow: 0 0 20px rgba(229, 62, 62, 0.2);
                    transform: translateY(-2px);
                }
                
                .feature-icon {
                    font-size: 2.5rem;
                    color: #E53E3E;
                    margin-bottom: 1rem;
                }
                
                .feature-title {
                    font-family: 'Share Tech Mono', monospace;
                    font-size: 1.2rem;
                    color: #FFFFFF;
                    margin-bottom: 1rem;
                }
                
                .feature-description {
                    color: #A1A1AA;
                    line-height: 1.6;
                }
                

                
                /* Responsive */
                @media (max-width: 1024px) {
                    .search-bar {
                        display: none;
                    }
                }

                @media (max-width: 768px) {
                    .nav {
                        flex-wrap: wrap;
                    }

                    .nav-links {
                        display: none; /* Simple hiding for now, can be a hamburger menu later */
                    }
                }
            </style>
        @endif
    </head>
    <body>
        <x-navbar />

        <main>
            {{ $slot }}
        </main>
        
        <!-- Modern Footer -->
        <footer class="bg-[#151515] border-t border-[#3F3F46] relative overflow-hidden">
            <!-- Background Effects -->
            <div class="absolute inset-0 bg-gradient-to-r from-purple-600/20 via-blue-600/20 to-red-600/20"></div>
            <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%239C92AC" fill-opacity="0.03"%3E%3Ccircle cx="30" cy="30" r="2"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')]"></div>
            
            <div class="relative z-10">
                <!-- Main Footer Content -->
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                        <!-- Brand Section -->
                        <div class="lg:col-span-1">
                            <div class="mb-6">
                                <h3 class="text-2xl font-bold text-white font-['Share_Tech_Mono'] mb-3">
                                    DAN & BRIAN <span class="text-[#E53E3E]">REVIEWS</span>
                                </h3>
                                <p class="text-zinc-400 font-['Inter'] leading-relaxed mb-6">
                                    Your ultimate destination for honest game reviews, epic podcasts, and the best streamers in gaming. Join thousands of gamers who trust our community.
                                </p>
                                <!-- Social Media Links -->
                                <div class="flex space-x-4">
                                    <a href="#" class="w-10 h-10 bg-zinc-800 hover:bg-[#E53E3E] text-zinc-400 hover:text-white rounded-lg flex items-center justify-center transition-all duration-300 transform hover:scale-110">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                                        </svg>
                                    </a>
                                    <a href="#" class="w-10 h-10 bg-zinc-800 hover:bg-[#8B5CF6] text-zinc-400 hover:text-white rounded-lg flex items-center justify-center transition-all duration-300 transform hover:scale-110">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M11.571 4.714h1.715v5.143H11.57zm4.715 0H18v5.143h-1.714zM6 0L1.714 4.286v15.428h5.143V24l4.286-4.286h3.428L22.286 12V0zm14.571 11.143l-3.428 3.428h-3.429l-3 3v-3H6.857V1.714h13.714Z"/>
                                        </svg>
                                    </a>
                                    <a href="#" class="w-10 h-10 bg-zinc-800 hover:bg-[#FF0000] text-zinc-400 hover:text-white rounded-lg flex items-center justify-center transition-all duration-300 transform hover:scale-110">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                        </svg>
                                    </a>
                                    <a href="#" class="w-10 h-10 bg-zinc-800 hover:bg-[#5865F2] text-zinc-400 hover:text-white rounded-lg flex items-center justify-center transition-all duration-300 transform hover:scale-110">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M20.317 4.37a19.791 19.791 0 0 0-4.885-1.515.074.074 0 0 0-.079.037c-.21.375-.444.864-.608 1.25a18.27 18.27 0 0 0-5.487 0 12.64 12.64 0 0 0-.617-1.25.077.077 0 0 0-.079-.037A19.736 19.736 0 0 0 3.677 4.37a.07.07 0 0 0-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 0 0 .031.057 19.9 19.9 0 0 0 5.993 3.03.078.078 0 0 0 .084-.028 14.09 14.09 0 0 0 1.226-1.994.076.076 0 0 0-.041-.106 13.107 13.107 0 0 1-1.872-.892.077.077 0 0 1-.008-.128 10.2 10.2 0 0 0 .372-.292.074.074 0 0 1 .077-.01c3.928 1.793 8.18 1.793 12.062 0a.074.074 0 0 1 .078.01c.12.098.246.198.373.292a.077.077 0 0 1-.006.127 12.299 12.299 0 0 1-1.873.892.077.077 0 0 0-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 0 0 .084.028 19.839 19.839 0 0 0 6.002-3.03.077.077 0 0 0 .032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 0 0-.031-.03zM8.02 15.33c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.956-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.956 2.418-2.157 2.418zm7.975 0c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.955-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.946 2.418-2.157 2.418Z"/>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Links -->
                        <div>
                            <h4 class="text-lg font-semibold text-white mb-6 font-['Share_Tech_Mono']">Explore</h4>
                            <ul class="space-y-3">
                                <li><a href="{{ route('games.index') }}" class="text-zinc-400 hover:text-[#E53E3E] transition-colors font-['Inter'] hover:translate-x-1 transform duration-200 inline-block">Game Reviews</a></li>
                                <li><a href="{{ route('tech.index') }}" class="text-zinc-400 hover:text-[#E53E3E] transition-colors font-['Inter'] hover:translate-x-1 transform duration-200 inline-block">Tech Reviews</a></li>
                                <li><a href="{{ route('streamer.profiles.index') }}" class="text-zinc-400 hover:text-purple-400 transition-colors font-['Inter'] hover:translate-x-1 transform duration-200 inline-block">Streamers</a></li>
                                <li><a href="{{ route('podcasts.index') }}" class="text-zinc-400 hover:text-green-400 transition-colors font-['Inter'] hover:translate-x-1 transform duration-200 inline-block">Podcasts</a></li>
                                <li><a href="{{ route('search.index') }}" class="text-zinc-400 hover:text-blue-400 transition-colors font-['Inter'] hover:translate-x-1 transform duration-200 inline-block">Search</a></li>
                                <li><a href="{{ route('lists.index') }}" class="text-zinc-400 hover:text-yellow-400 transition-colors font-['Inter'] hover:translate-x-1 transform duration-200 inline-block">Community Lists</a></li>
                            </ul>
                        </div>

                        <!-- Community -->
                        <div>
                            <h4 class="text-lg font-semibold text-white mb-6 font-['Share_Tech_Mono']">Community</h4>
                            <ul class="space-y-3">
                                @auth
                                    <li><a href="{{ route('dashboard') }}" class="text-zinc-400 hover:text-[#E53E3E] transition-colors font-['Inter'] hover:translate-x-1 transform duration-200 inline-block">Dashboard</a></li>
                                    @if(auth()->user()->streamerProfile)
                                        <li><a href="{{ route('streamer.profile.show', auth()->user()->streamerProfile) }}" class="text-zinc-400 hover:text-purple-400 transition-colors font-['Inter'] hover:translate-x-1 transform duration-200 inline-block">My Profile</a></li>
                                    @else
                                        <li><a href="{{ route('streamer.profiles.create') }}" class="text-zinc-400 hover:text-purple-400 transition-colors font-['Inter'] hover:translate-x-1 transform duration-200 inline-block">Create Profile</a></li>
                                    @endif
                                    <li><a href="{{ route('streamer.followers.index') }}" class="text-zinc-400 hover:text-purple-400 transition-colors font-['Inter'] hover:translate-x-1 transform duration-200 inline-block">Following</a></li>
                                @else
                                    <li><a href="{{ route('login') }}" class="text-zinc-400 hover:text-[#E53E3E] transition-colors font-['Inter'] hover:translate-x-1 transform duration-200 inline-block">Sign In</a></li>
                                    <li><a href="{{ route('register') }}" class="text-zinc-400 hover:text-green-400 transition-colors font-['Inter'] hover:translate-x-1 transform duration-200 inline-block">Join Community</a></li>
                                @endauth
                                <li><a href="{{ route('podcasts.create') }}" class="text-zinc-400 hover:text-green-400 transition-colors font-['Inter'] hover:translate-x-1 transform duration-200 inline-block">Submit Podcast</a></li>
                                <li><a href="#" class="text-zinc-400 hover:text-blue-400 transition-colors font-['Inter'] hover:translate-x-1 transform duration-200 inline-block">About Us</a></li>
                                <li><a href="#" class="text-zinc-400 hover:text-blue-400 transition-colors font-['Inter'] hover:translate-x-1 transform duration-200 inline-block">Contact</a></li>
                            </ul>
                        </div>

                        <!-- Newsletter -->
                        <div>
                            <h4 class="text-lg font-semibold text-white mb-6 font-['Share_Tech_Mono']">Stay Updated</h4>
                            <p class="text-zinc-400 text-sm mb-4 font-['Inter']">Get the latest reviews, streamer highlights, and gaming news delivered to your inbox.</p>
                            <form class="space-y-3">
                                <div class="relative">
                                    <input type="email" placeholder="Enter your email" class="w-full bg-zinc-800 border border-zinc-700 rounded-lg px-4 py-3 text-white placeholder-zinc-500 focus:border-[#E53E3E] focus:ring-2 focus:ring-[#E53E3E]/20 transition-all duration-300 font-['Inter'] text-sm">
                                    <button type="submit" class="absolute right-2 top-2 bottom-2 bg-[#E53E3E] hover:bg-red-700 text-white px-4 rounded-md transition-all duration-300 transform hover:scale-105 font-['Inter'] text-sm font-semibold">
                                        Subscribe
                                    </button>
                                </div>
                            </form>
                            
                            <!-- Quick Stats -->
                            <div class="mt-6 grid grid-cols-2 gap-4">
                                <div class="bg-zinc-800/50 rounded-lg p-3 text-center">
                                    <div class="text-[#E53E3E] font-bold text-lg font-['Share_Tech_Mono']">1K+</div>
                                    <div class="text-zinc-400 text-xs font-['Inter']">Reviews</div>
                                </div>
                                <div class="bg-zinc-800/50 rounded-lg p-3 text-center">
                                    <div class="text-purple-400 font-bold text-lg font-['Share_Tech_Mono']">200+</div>
                                    <div class="text-zinc-400 text-xs font-['Inter']">Streamers</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bottom Bar -->
                <div class="border-t border-zinc-800">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                        <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                            <div class="flex flex-col md:flex-row items-center space-y-2 md:space-y-0 md:space-x-6">
                                <p class="text-zinc-400 text-sm font-['Inter']">
                                    &copy; {{ date('Y') }} Dan & Brian Reviews. All rights reserved.
                                </p>
                                <div class="flex items-center space-x-4 text-zinc-500 text-xs font-['Inter']">
                                    <a href="#" class="hover:text-zinc-300 transition-colors">Privacy Policy</a>
                                    <span>•</span>
                                    <a href="#" class="hover:text-zinc-300 transition-colors">Terms of Service</a>
                                    <span>•</span>
                                    <a href="#" class="hover:text-zinc-300 transition-colors">Cookie Policy</a>
                                </div>
                            </div>
                            <div class="flex items-center text-zinc-400 text-sm font-['Inter']">
                                <svg class="w-4 h-4 mr-2 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
                                </svg>
                                Built with passion for the gaming community
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </footer>

        <!-- Livewire Scripts -->
        @livewireScripts
        
        <!-- Alpine.js -->
        <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    </body>
</html> 