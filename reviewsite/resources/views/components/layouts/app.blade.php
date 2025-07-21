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
        <footer class="bg-[#121212] border-t border-[#292929] relative">
            <div class="relative z-10">
                <!-- Main Footer Content -->
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                        <!-- Brand Section -->
                        <div class="lg:col-span-1">
                            <div class="mb-6">
                                <h3 class="text-2xl font-bold text-white font-['Poppins'] mb-3">
                                    DAN & BRIAN <span class="text-[#DC2626]">REVIEWS</span>
                                </h3>
                                <p class="text-[#A0A0A0] font-['Inter'] leading-relaxed mb-6">
                                    Your ultimate destination for honest game reviews, epic podcasts, and the best streamers in gaming. Join thousands of gamers who trust our community.
                                </p>
                                <!-- Social Media Links -->
                                <div class="flex space-x-4">
                                    <a href="#" class="w-10 h-10 bg-[#18181B] border border-[#292929] rounded-full flex items-center justify-center text-[#A0A0A0] hover:text-[#DC2626] hover:bg-[#232326] hover:border-[#DC2626] transition-all duration-300 transform hover:scale-110">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
                                        </svg>
                                    </a>
                                    <a href="#" class="w-10 h-10 bg-[#18181B] border border-[#292929] rounded-full flex items-center justify-center text-[#A0A0A0] hover:text-[#DC2626] hover:bg-[#232326] hover:border-[#DC2626] transition-all duration-300 transform hover:scale-110">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M22.46 6c-.77.35-1.6.58-2.46.69.88-.53 1.56-1.37 1.88-2.38-.83.5-1.75.85-2.72 1.05C18.37 4.5 17.26 4 16 4c-2.35 0-4.27 1.92-4.27 4.29 0 .34.04.67.11.98C8.28 9.09 5.11 7.38 3 4.79c-.37.63-.58 1.37-.58 2.15 0 1.49.75 2.81 1.91 3.56-.71 0-1.37-.2-1.95-.5v.03c0 2.08 1.48 3.82 3.44 4.21a4.22 4.22 0 0 1-1.93.07 4.28 4.28 0 0 0 4 2.98 8.521 8.521 0 0 1-5.33 1.84c-.34 0-.68-.02-1.02-.06C3.44 20.29 5.7 21 8.12 21 16 21 20.33 14.46 20.33 8.79c0-.19 0-.37-.01-.56.84-.6 1.56-1.36 2.14-2.23z"/>
                                        </svg>
                                    </a>
                                    <a href="#" class="w-10 h-10 bg-[#18181B] border border-[#292929] rounded-full flex items-center justify-center text-[#A0A0A0] hover:text-[#DC2626] hover:bg-[#232326] hover:border-[#DC2626] transition-all duration-300 transform hover:scale-110">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                                        </svg>
                                    </a>
                                    <a href="#" class="w-10 h-10 bg-[#18181B] border border-[#292929] rounded-full flex items-center justify-center text-[#A0A0A0] hover:text-[#DC2626] hover:bg-[#232326] hover:border-[#DC2626] transition-all duration-300 transform hover:scale-110">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.174-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.72-.359-1.781c0-1.663.967-2.911 2.168-2.911 1.024 0 1.518.769 1.518 1.688 0 1.029-.653 2.567-.992 3.992-.285 1.193.6 2.165 1.775 2.165 2.128 0 3.768-2.245 3.768-5.487 0-2.861-2.063-4.869-5.008-4.869-3.41 0-5.409 2.562-5.409 5.199 0 1.033.394 2.143.889 2.741.099.12.112.225.083.345-.09.375-.293 1.199-.334 1.363-.053.225-.172.271-.402.165-1.495-.69-2.433-2.878-2.433-4.646 0-3.776 2.748-7.252 7.92-7.252 4.158 0 7.392 2.967 7.392 6.923 0 4.135-2.607 7.462-6.233 7.462-1.214 0-2.357-.629-2.758-1.378l-.749 2.848c-.269 1.045-1.004 2.352-1.498 3.146 1.123.345 2.306.535 3.55.535 6.624 0 11.99-5.367 11.99-11.99C24.007 5.367 18.641.001.012.001z"/>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Explore -->
                        <div>
                            <h4 class="text-lg font-semibold text-white mb-6 font-['Poppins']">Explore</h4>
                            <ul class="space-y-3">
                                <li><a href="{{ route('games.index') }}" class="text-[#A0A0A0] hover:text-[#DC2626] transition-colors font-['Inter'] hover:translate-x-1 transform duration-200 inline-block">Game Reviews</a></li>
                                <li><a href="{{ route('tech.index') }}" class="text-[#A0A0A0] hover:text-[#DC2626] transition-colors font-['Inter'] hover:translate-x-1 transform duration-200 inline-block">Tech Reviews</a></li>
                                <li><a href="{{ route('streamer.profiles.index') }}" class="text-[#A0A0A0] hover:text-white transition-colors font-['Inter'] hover:translate-x-1 transform duration-200 inline-block">Streamers</a></li>
                                <li><a href="{{ route('podcasts.index') }}" class="text-[#A0A0A0] hover:text-white transition-colors font-['Inter'] hover:translate-x-1 transform duration-200 inline-block">Podcasts</a></li>
                                <li><a href="{{ route('search.index') }}" class="text-[#A0A0A0] hover:text-white transition-colors font-['Inter'] hover:translate-x-1 transform duration-200 inline-block">Search</a></li>
                                <li><a href="{{ route('lists.index') }}" class="text-[#A0A0A0] hover:text-white transition-colors font-['Inter'] hover:translate-x-1 transform duration-200 inline-block">Community Lists</a></li>
                            </ul>
                        </div>

                        <!-- Community -->
                        <div>
                            <h4 class="text-lg font-semibold text-white mb-6 font-['Poppins']">Community</h4>
                            <ul class="space-y-3">
                                @auth
                                    <li><a href="{{ route('dashboard') }}" class="text-[#A0A0A0] hover:text-[#DC2626] transition-colors font-['Inter'] hover:translate-x-1 transform duration-200 inline-block">Dashboard</a></li>
                                    @if(auth()->user()->streamerProfile)
                                        <li><a href="{{ route('streamer.profile.show', auth()->user()->streamerProfile) }}" class="text-[#A0A0A0] hover:text-white transition-colors font-['Inter'] hover:translate-x-1 transform duration-200 inline-block">My Profile</a></li>
                                    @else
                                        <li><a href="{{ route('streamer.profiles.create') }}" class="text-[#A0A0A0] hover:text-white transition-colors font-['Inter'] hover:translate-x-1 transform duration-200 inline-block">Create Profile</a></li>
                                    @endif
                                    <li><a href="{{ route('streamer.followers.index') }}" class="text-[#A0A0A0] hover:text-white transition-colors font-['Inter'] hover:translate-x-1 transform duration-200 inline-block">Following</a></li>
                                @else
                                    <li><a href="{{ route('login') }}" class="text-[#A0A0A0] hover:text-[#DC2626] transition-colors font-['Inter'] hover:translate-x-1 transform duration-200 inline-block">Sign In</a></li>
                                    <li><a href="{{ route('register') }}" class="text-[#A0A0A0] hover:text-[#DC2626] transition-colors font-['Inter'] hover:translate-x-1 transform duration-200 inline-block">Join Community</a></li>
                                @endauth
                                <li><a href="{{ route('podcasts.create') }}" class="text-[#A0A0A0] hover:text-white transition-colors font-['Inter'] hover:translate-x-1 transform duration-200 inline-block">Submit Podcast</a></li>
                                <li><a href="#" class="text-[#A0A0A0] hover:text-white transition-colors font-['Inter'] hover:translate-x-1 transform duration-200 inline-block">About Us</a></li>
                                <li><a href="#" class="text-[#A0A0A0] hover:text-white transition-colors font-['Inter'] hover:translate-x-1 transform duration-200 inline-block">Contact</a></li>
                            </ul>
                        </div>

                        <!-- Newsletter -->
                        <div>
                            <h4 class="text-lg font-semibold text-white mb-6 font-['Poppins']">Stay Updated</h4>
                            <p class="text-[#A0A0A0] text-sm mb-4 font-['Inter']">Get the latest reviews, streamer highlights, and gaming news delivered to your inbox.</p>
                            <form class="space-y-3">
                                <div class="relative">
                                    <input type="email" placeholder="Enter your email" class="w-full bg-[#18181B] border border-[#292929] rounded-lg px-4 py-3 text-white placeholder-[#A0A0A0] focus:border-[#DC2626] focus:ring-2 focus:ring-[#DC2626]/20 transition-all duration-300 font-['Inter'] text-sm">
                                    <button type="submit" class="absolute right-2 top-2 bottom-2 bg-[#DC2626] hover:bg-[#B91C1C] text-white px-4 rounded-md transition-all duration-300 transform hover:scale-105 font-['Inter'] text-sm font-semibold">
                                        Subscribe
                                    </button>
                                </div>
                            </form>
                            
                            <!-- Quick Stats -->
                            <div class="mt-6 grid grid-cols-2 gap-4">
                                <div class="bg-[#18181B] border border-[#292929] rounded-lg p-3 text-center">
                                    <div class="text-[#DC2626] font-bold text-lg font-['Poppins']">1K+</div>
                                    <div class="text-[#A0A0A0] text-xs font-['Inter']">Reviews</div>
                                </div>
                                <div class="bg-[#18181B] border border-[#292929] rounded-lg p-3 text-center">
                                    <div class="text-white font-bold text-lg font-['Poppins']">200+</div>
                                    <div class="text-[#A0A0A0] text-xs font-['Inter']">Streamers</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bottom Bar -->
                <div class="border-t border-[#292929]">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                        <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                            <div class="flex flex-col md:flex-row items-center space-y-2 md:space-y-0 md:space-x-6">
                                <p class="text-[#A0A0A0] text-sm font-['Inter']">
                                    &copy; {{ date('Y') }} Dan & Brian Reviews. All rights reserved.
                                </p>
                                <div class="flex items-center space-x-4 text-[#A0A0A0] text-xs font-['Inter']">
                                    <a href="#" class="hover:text-white transition-colors">Privacy Policy</a>
                                    <span>•</span>
                                    <a href="#" class="hover:text-white transition-colors">Terms of Service</a>
                                    <span>•</span>
                                    <a href="#" class="hover:text-white transition-colors">Cookie Policy</a>
                                </div>
                            </div>
                            <div class="flex items-center text-[#A0A0A0] text-sm font-['Inter']">
                                <svg class="w-4 h-4 mr-2 text-[#DC2626]" fill="currentColor" viewBox="0 0 20 20">
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
