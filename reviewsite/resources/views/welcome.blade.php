<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Dan & Brian Reviews - Coming Soon</title>
        
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Share+Tech+Mono:wght@400&family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
        
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
                overflow-x: hidden;
                min-height: 100vh;
                position: relative;
                font-size: 16px;
            }
            
            body::before {
                content: '';
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: 
                    radial-gradient(circle at 20% 80%, rgba(229, 62, 62, 0.1) 0%, transparent 50%),
                    radial-gradient(circle at 80% 20%, rgba(37, 99, 235, 0.1) 0%, transparent 50%),
                    radial-gradient(circle at 40% 40%, rgba(229, 62, 62, 0.05) 0%, transparent 50%);
                pointer-events: none;
                z-index: 1;
            }
            
            .container {
                position: relative;
                z-index: 2;
                min-height: 100vh;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                padding: 2rem;
            }
            
            .glitch-container {
                position: relative;
                margin-bottom: 3rem;
            }
            
            .title {
                font-family: 'Share Tech Mono', monospace;
                font-size: clamp(1.5rem, 5vw, 3rem);
                text-align: center;
                text-shadow: 
                    0 0 10px #E53E3E,
                    0 0 20px #E53E3E,
                    0 0 30px #E53E3E;
                animation: glow 2s ease-in-out infinite alternate;
                position: relative;
                color: #FFFFFF;
            }
            
            .title::before,
            .title::after {
                content: 'DAN & BRIAN REVIEWS';
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                opacity: 0.8;
            }
            
            .title::before {
                animation: glitch 3s infinite;
                color: #2563EB;
                z-index: -1;
            }
            
            .title::after {
                animation: glitch 2s infinite reverse;
                color: #4CAF50;
                z-index: -2;
            }
            
            .subtitle {
                font-family: 'Inter', sans-serif;
                font-size: clamp(0.8rem, 2vw, 1.2rem);
                text-align: center;
                margin-bottom: 2rem;
                color: #A1A1AA;
                text-shadow: 0 0 5px #A1A1AA;
                animation: pulse 2s ease-in-out infinite;
            }
            
            .coming-soon {
                font-family: 'Share Tech Mono', monospace;
                font-size: clamp(0.6rem, 1.5vw, 1rem);
                text-align: center;
                color: #E53E3E;
                text-shadow: 0 0 10px #E53E3E;
                margin-bottom: 3rem;
                animation: blink 1.5s infinite;
            }
            
            .pixel-border {
                border: 3px solid #E53E3E;
                padding: 2rem;
                position: relative;
                background: #27272A;
                backdrop-filter: blur(10px);
                box-shadow: 
                    0 0 20px rgba(229, 62, 62, 0.3),
                    inset 0 0 20px rgba(229, 62, 62, 0.1);
                border-radius: 8px;
            }
            
            .pixel-border::before {
                content: '';
                position: absolute;
                top: -5px;
                left: -5px;
                right: -5px;
                bottom: -5px;
                background: linear-gradient(45deg, #E53E3E, #DC2626, #E53E3E);
                z-index: -1;
                animation: borderGlow 3s ease-in-out infinite;
                border-radius: 12px;
            }
            
            .loading-bar {
                width: 300px;
                height: 20px;
                background: #1A1A1B;
                border: 2px solid #E53E3E;
                margin: 2rem auto;
                position: relative;
                overflow: hidden;
                border-radius: 4px;
            }
            
            .loading-fill {
                height: 100%;
                background: linear-gradient(90deg, #E53E3E, #DC2626, #E53E3E);
                width: 0%;
                animation: loading 3s ease-in-out infinite;
                box-shadow: 0 0 10px #E53E3E;
            }
            
            .pixel-art {
                display: flex;
                justify-content: center;
                gap: 1rem;
                margin: 2rem 0;
                flex-wrap: wrap;
            }
            
            .pixel {
                width: 8px;
                height: 8px;
                background: #E53E3E;
                animation: pixelGlow 2s ease-in-out infinite;
                border-radius: 2px;
            }
            
            .pixel:nth-child(odd) {
                animation-delay: 0.5s;
            }
            
            .pixel:nth-child(3n) {
                animation-delay: 1s;
            }
            
            .pixel:nth-child(4n) {
                animation-delay: 1.5s;
            }
            
            @keyframes glow {
                from { text-shadow: 0 0 10px #E53E3E, 0 0 20px #E53E3E, 0 0 30px #E53E3E; }
                to { text-shadow: 0 0 20px #E53E3E, 0 0 30px #E53E3E, 0 0 40px #E53E3E; }
            }
            
            @keyframes glitch {
                0%, 100% { transform: translate(0); }
                20% { transform: translate(-2px, 2px); }
                40% { transform: translate(-2px, -2px); }
                60% { transform: translate(2px, 2px); }
                80% { transform: translate(2px, -2px); }
            }
            
            @keyframes pulse {
                0%, 100% { opacity: 1; }
                50% { opacity: 0.7; }
            }
            
            @keyframes blink {
                0%, 50% { opacity: 1; }
                51%, 100% { opacity: 0; }
            }
            
            @keyframes borderGlow {
                0%, 100% { opacity: 1; }
                50% { opacity: 0.5; }
            }
            
            @keyframes loading {
                0% { width: 0%; }
                50% { width: 70%; }
                100% { width: 100%; }
            }
            
            @keyframes pixelGlow {
                0%, 100% { opacity: 1; transform: scale(1); }
                50% { opacity: 0.5; transform: scale(1.2); }
            }
            
            .scan-lines {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: linear-gradient(
                    transparent 50%,
                    rgba(229, 62, 62, 0.02) 50%
                );
                background-size: 100% 4px;
                pointer-events: none;
                z-index: 1;
                animation: scan 10s linear infinite;
            }
            
            @keyframes scan {
                0% { transform: translateY(0); }
                100% { transform: translateY(4px); }
            }
            
            .noise {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100"><filter id="noise"><feTurbulence type="fractalNoise" baseFrequency="0.9" numOctaves="4" stitchTiles="stitch"/></filter><rect width="100" height="100" filter="url(%23noise)" opacity="0.1"/></svg>');
                pointer-events: none;
                z-index: 1;
                opacity: 0.1;
            }
            
            @media (max-width: 768px) {
                .title {
                    font-size: 1.5rem;
                }
                
                .subtitle {
                    font-size: 0.8rem;
                }
                
                .coming-soon {
                    font-size: 0.6rem;
                }
                
                .loading-bar {
                    width: 250px;
                }
            }
        </style>
    </head>
    <body>
        <div class="scan-lines"></div>
        <div class="noise"></div>
        
        <div class="container">
            <div class="glitch-container">
                <h1 class="title">DAN & BRIAN REVIEWS</h1>
            </div>
            
            <div class="pixel-border">
                <p class="coming-soon">COMING SOON</p>
                
                <div class="pixel-art">
                    <div class="pixel"></div>
                    <div class="pixel"></div>
                    <div class="pixel"></div>
                    <div class="pixel"></div>
                    <div class="pixel"></div>
                    <div class="pixel"></div>
                    <div class="pixel"></div>
                    <div class="pixel"></div>
                    <div class="pixel"></div>
                    <div class="pixel"></div>
                    <div class="pixel"></div>
                    <div class="pixel"></div>
                    <div class="pixel"></div>
                    <div class="pixel"></div>
                    <div class="pixel"></div>
                    <div class="pixel"></div>
                    <div class="pixel"></div>
                    <div class="pixel"></div>
                    <div class="pixel"></div>
                    <div class="pixel"></div>
                </div>
                
                <p class="subtitle">LOADING REVIEWS AND SHIT...</p>
                
                <div class="loading-bar">
                    <div class="loading-fill"></div>
                </div>
                
                <p class="subtitle">PREPARING THE ULTIMATE GAMING REVIEW EXPERIENCE</p>
            </div>
        </div>
    </body>
</html>
