<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', 'Dan & Brian Reviews')</title>
        
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Share+Tech+Mono:wght@400&family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
        
        <!-- Styles -->
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
            
            /* Slider Section */
            .slider-section {
                width: 100%;
                height: 60vh;
                position: relative;
                overflow: hidden;
            }

            .slider-container {
                display: flex;
                height: 100%;
                transition: transform 0.5s ease-in-out;
            }

            .slide {
                min-width: 100%;
                height: 100%;
                background-size: cover;
                background-position: center;
                position: relative;
                display: flex;
                align-items: center;
                justify-content: center;
                text-align: center;
            }

            .slide::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.6);
                z-index: 1;
            }

            .slide-content {
                position: relative;
                z-index: 2;
                max-width: 800px;
                padding: 2rem;
                color: #FFFFFF;
            }

            .slide-type {
                display: inline-block;
                background: #E53E3E;
                padding: 0.25rem 0.75rem;
                border-radius: 4px;
                font-family: 'Share Tech Mono', monospace;
                font-weight: 700;
                margin-bottom: 1rem;
                text-transform: uppercase;
            }

            .slide-title {
                font-family: 'Share Tech Mono', monospace;
                font-size: 2.5rem;
                margin-bottom: 1rem;
                text-shadow: 2px 2px 8px rgba(0,0,0,0.7);
            }

            .slide-excerpt {
                font-size: 1.1rem;
                color: #A1A1AA;
                margin-bottom: 2rem;
            }

            /* Slider Navigation */
            .slider-nav button {
                position: absolute;
                top: 50%;
                transform: translateY(-50%);
                background: rgba(0, 0, 0, 0.5);
                border: 1px solid #3F3F46;
                color: #FFFFFF;
                padding: 0.5rem 1rem;
                border-radius: 8px;
                cursor: pointer;
                z-index: 3;
                font-size: 1.5rem;
            }

            .slider-nav .prev-btn {
                left: 2rem;
            }

            .slider-nav .next-btn {
                right: 2rem;
            }
            
            .slider-nav button:hover {
                background: #E53E3E;
            }

            .slider-dots {
                position: absolute;
                bottom: 1.5rem;
                left: 50%;
                transform: translateX(-50%);
                display: flex;
                gap: 0.75rem;
                z-index: 3;
            }

            .slider-dots .dot {
                width: 12px;
                height: 12px;
                border-radius: 50%;
                background: #3F3F46;
                cursor: pointer;
                transition: background 0.3s ease;
            }

            .slider-dots .dot.active {
                background: #E53E3E;
            }
            
            /* Content Sections */
            .section {
                padding: 3rem 0;
            }

            .hero {
                padding-top: 4rem; 
                padding-bottom: 4rem; 
                text-align: center; 
                background: linear-gradient(135deg, #1A1A1B 0%, #27272A 100%);
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
                grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
                gap: 1.5rem;
                margin-top: 2rem;
            }
            
            .feature-card {
                background: #27272A;
                padding: 1.25rem;
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
                font-size: 2rem;
                color: #E53E3E;
                margin-bottom: 0.75rem;
            }
            
            .feature-title {
                font-family: 'Share Tech Mono', monospace;
                font-size: 1rem;
                color: #FFFFFF;
                margin-bottom: 0.5rem;
            }
            
            .feature-description {
                color: #A1A1AA;
                line-height: 1.5;
                font-size: 0.9rem;
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
    </head>
    <body>
        @include('components.navbar')

        <main>
            @yield('content')
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

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const sliderContainer = document.querySelector('.slider-container');
                if (!sliderContainer) return;

                const slides = document.querySelectorAll('.slide');
                const nextBtn = document.querySelector('.next-btn');
                const prevBtn = document.querySelector('.prev-btn');
                const dotsContainer = document.querySelector('.slider-dots');
                let currentIndex = 0;
                let slideInterval;

                if (slides.length <= 1) {
                    if (nextBtn) nextBtn.style.display = 'none';
                    if (prevBtn) prevBtn.style.display = 'none';
                    return;
                }

                function createDots() {
                    slides.forEach((_, i) => {
                        const dot = document.createElement('button');
                        dot.classList.add('dot');
                        if (i === 0) dot.classList.add('active');
                        dot.addEventListener('click', () => {
                            goToSlide(i);
                            resetInterval();
                        });
                        dotsContainer.appendChild(dot);
                    });
                }

                function updateDots() {
                    const dots = document.querySelectorAll('.slider-dots .dot');
                    dots.forEach((dot, i) => {
                        dot.classList.toggle('active', i === currentIndex);
                    });
                }

                function goToSlide(index) {
                    currentIndex = (index + slides.length) % slides.length;
                    sliderContainer.style.transform = `translateX(-${currentIndex * 100}%)`;
                    updateDots();
                }

                function nextSlide() {
                    goToSlide(currentIndex + 1);
                }

                function prevSlide() {
                    goToSlide(currentIndex - 1);
                }
                
                function startInterval() {
                    slideInterval = setInterval(nextSlide, 5000); // Change slide every 5 seconds
                }

                function resetInterval() {
                    clearInterval(slideInterval);
                    startInterval();
                }

                nextBtn.addEventListener('click', () => {
                    nextSlide();
                    resetInterval();
                });

                prevBtn.addEventListener('click', () => {
                    prevSlide();
                    resetInterval();
                });

                createDots();
                startInterval();
            });
        </script>
    </body>
</html> 