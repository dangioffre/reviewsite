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
                
                /* Footer */
                .footer {
                    background: #27272A;
                    border-top: 2px solid #E53E3E;
                    padding: 2rem 0;
                    margin-top: 4rem;
                }
                
                .footer-content {
                    text-align: center;
                    color: #A1A1AA;
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
        
        <!-- Footer -->
        <footer class="footer">
            <div class="container">
                <div class="footer-content">
                    <p>&copy; {{ date('Y') }} Dan & Brian Reviews. All rights reserved.</p>
                    <p style="margin-top: 0.5rem; font-size: 0.9rem;">Built with ❤️ for the gaming community</p>
                </div>
            </div>
        </footer>
    </body>
</html> 