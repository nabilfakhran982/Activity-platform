<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found | Activio</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Display:wght@400;500;700&family=DM+Sans:ital,wght@0,400;0,500;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: linear-gradient(135deg, #1a1a18 0%, #2a2520 50%, #3d3430 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: auto;
            overflow-x: hidden;
            position: relative;
            width: 100%;
            margin: 0;
            padding: 0;
        }

        /* Animated background blobs */
        .blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.15;
            animation: float 20s ease-in-out infinite;
        }

        .blob-1 {
            width: 300px;
            height: 300px;
            background: #D4A350;
            top: -100px;
            left: -100px;
            animation-duration: 25s;
        }

        .blob-2 {
            width: 250px;
            height: 250px;
            background: #e05252;
            bottom: -50px;
            right: -50px;
            animation-duration: 30s;
            animation-delay: -5s;
        }

        .blob-3 {
            width: 200px;
            height: 200px;
            background: #D4A350;
            top: 50%;
            left: 50%;
            animation-duration: 28s;
            animation-delay: -10s;
        }

        @keyframes float {
            0%, 100% {
                transform: translate(0, 0);
            }
            33% {
                transform: translate(30px, -50px);
            }
            66% {
                transform: translate(-30px, 50px);
            }
        }

        .container {
            position: relative;
            z-index: 10;
            text-align: center;
            padding: 40px 20px;
            max-width: 600px;
            width: 100%;
        }

        /* 404 Number Animation */
        .error-code {
            font-family: 'DM Display', sans-serif;
            font-size: clamp(120px, 25vw, 200px);
            font-weight: 700;
            background: linear-gradient(135deg, #D4A350 0%, #e05252 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: -20px;
            animation: slideDown 0.8s ease-out;
            letter-spacing: -2px;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Lost Icon */
        .lost-icon {
            font-size: 80px;
            margin-bottom: 30px;
            animation: bounce 2s ease-in-out infinite;
        }

        @keyframes bounce {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-20px);
            }
        }

        .heading {
            font-family: 'DM Display', sans-serif;
            font-size: clamp(24px, 6vw, 36px);
            font-weight: 700;
            color: #fff;
            margin-bottom: 16px;
            animation: fadeInUp 0.8s ease-out 0.1s both;
        }

        .description {
            font-size: 16px;
            color: #a09890;
            line-height: 1.6;
            margin-bottom: 40px;
            animation: fadeInUp 0.8s ease-out 0.2s both;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .cta-buttons {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
            animation: fadeInUp 0.8s ease-out 0.3s both;
        }

        .btn {
            padding: 14px 28px;
            border-radius: 999px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'DM Sans', sans-serif;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #D4A350 0%, #c99340 100%);
            color: #1a1a18;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(212, 163, 80, 0.3);
        }

        .btn-primary:active {
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: rgba(212, 163, 80, 0.1);
            color: #D4A350;
            border: 2px solid #D4A350;
        }

        .btn-secondary:hover {
            background: rgba(212, 163, 80, 0.2);
            transform: translateY(-3px);
        }

        .btn-secondary:active {
            transform: translateY(-1px);
        }

        .icon-small {
            font-size: 16px;
        }

        /* Search box suggestion */
        .search-suggestion {
            margin-top: 50px;
            padding-top: 40px;
            border-top: 1px solid rgba(212, 163, 80, 0.2);
            animation: fadeInUp 0.8s ease-out 0.4s both;
        }

        .search-suggestion p {
            font-size: 13px;
            color: #8a7a6a;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .search-box {
            display: flex;
            gap: 8px;
        }

        .search-input {
            flex: 1;
            padding: 12px 16px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(212, 163, 80, 0.3);
            border-radius: 12px;
            color: #fff;
            font-family: 'DM Sans', sans-serif;
            transition: all 0.3s ease;
        }

        .search-input::placeholder {
            color: #8a7a6a;
        }

        .search-input:focus {
            outline: none;
            background: rgba(255, 255, 255, 0.12);
            border-color: #D4A350;
            box-shadow: 0 0 20px rgba(212, 163, 80, 0.2);
        }

        .search-btn {
            padding: 12px 24px;
            background: #D4A350;
            border: none;
            border-radius: 12px;
            color: #1a1a18;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .search-btn:hover {
            background: #c99340;
            transform: translateY(-2px);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .container {
                padding: 35px 20px;
                max-width: 100%;
            }

            .error-code {
                font-size: clamp(80px, 20vw, 160px);
                margin-bottom: -15px;
            }

            .lost-icon {
                font-size: clamp(50px, 15vw, 80px);
                margin-bottom: 25px;
            }

            .heading {
                font-size: clamp(20px, 6vw, 32px);
                margin-bottom: 14px;
            }

            .description {
                font-size: clamp(14px, 4vw, 16px);
                margin-bottom: 30px;
                line-height: 1.5;
            }

            .cta-buttons {
                gap: 10px;
                flex-wrap: wrap;
                justify-content: center;
            }

            .btn {
                padding: 12px 24px;
                font-size: 14px;
                flex: 0 1 auto;
                min-width: 140px;
            }

            .search-box {
                flex-direction: column;
                gap: 10px;
            }

            .search-input,
            .search-btn {
                width: 100%;
            }

            .search-suggestion {
                margin-top: 40px;
                padding-top: 30px;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 0;
                min-height: auto;
            }

            .container {
                padding: 25px 15px;
            }

            .error-code {
                font-size: clamp(70px, 18vw, 120px);
                margin-bottom: -10px;
            }

            .lost-icon {
                font-size: clamp(45px, 12vw, 70px);
                margin-bottom: 20px;
            }

            .heading {
                font-size: clamp(18px, 5vw, 26px);
                margin-bottom: 12px;
            }

            .description {
                font-size: clamp(13px, 3.5vw, 15px);
                margin-bottom: 20px;
                line-height: 1.4;
            }

            .cta-buttons {
                gap: 8px;
                flex-direction: column;
                align-items: stretch;
            }

            .btn {
                padding: 11px 20px;
                font-size: 13px;
                width: 100%;
                justify-content: center;
            }

            .search-suggestion {
                margin-top: 30px;
                padding-top: 20px;
            }

            .search-suggestion p {
                font-size: 12px;
            }

            .search-input {
                padding: 10px 14px;
                font-size: 14px;
            }

            .search-btn {
                padding: 10px 20px;
                font-size: 13px;
            }
        }

        @media (max-width: 320px) {
            .container {
                padding: 20px 12px;
            }

            .error-code {
                font-size: 60px;
                margin-bottom: -5px;
            }

            .lost-icon {
                font-size: 40px;
                margin-bottom: 15px;
            }

            .heading {
                font-size: 18px;
                margin-bottom: 10px;
            }

            .description {
                font-size: 13px;
                margin-bottom: 15px;
            }

            .btn {
                padding: 10px 18px;
                font-size: 12px;
            }

            .icon-small {
                font-size: 14px;
            }
        }

        /* Accessibility */
        @media (prefers-reduced-motion: reduce) {
            .error-code,
            .lost-icon,
            .heading,
            .description,
            .cta-buttons,
            .search-suggestion,
            .blob {
                animation: none !important;
            }

            .btn {
                transition: none;
            }
        }
    </style>
</head>
<body>
    <!-- Animated background blobs -->
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    <div class="blob blob-3"></div>

    <!-- Main content -->
    <div class="container">
        <div class="lost-icon">
            <i class="fas fa-map-location-dot"></i>
        </div>

        <div class="error-code">404</div>

        <h1 class="heading">Oops! Lost?</h1>
        <p class="description">
            We couldn't find the page you're looking for. It might have moved, been deleted, or you might have taken a wrong turn in our activity map.
        </p>

        <div class="cta-buttons">
            <a href="/" class="btn btn-primary">
                <i class="fas fa-home icon-small"></i>
                Back Home
            </a>
            <a href="/activities" class="btn btn-secondary">
                <i class="fas fa-compass icon-small"></i>
                Browse Activities
            </a>
        </div>

        <div class="search-suggestion">
            <p>Looking for something specific?</p>
            <form action="/search" method="GET" class="search-box">
                <input
                    type="text"
                    name="q"
                    class="search-input"
                    placeholder="Search activities, centers, or instructors..."
                    required
                >
                <button type="submit" class="search-btn">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>
    </div>

    <script>
        // Smooth scroll to top on page load
        window.addEventListener('load', () => {
            window.scrollTo(0, 0);
        });

        // Interactive error code animation on interact
        const errorCode = document.querySelector('.error-code');
        if (errorCode) {
            errorCode.addEventListener('click', () => {
                errorCode.style.animation = 'none';
                setTimeout(() => {
                    errorCode.style.animation = 'slideDown 0.8s ease-out';
                }, 10);
            });
        }

        // Search form focus effect
        const searchInput = document.querySelector('.search-input');
        if (searchInput) {
            searchInput.addEventListener('focus', () => {
                document.querySelector('.search-suggestion').style.transform = 'scale(1.02)';
            });
            searchInput.addEventListener('blur', () => {
                document.querySelector('.search-suggestion').style.transform = 'scale(1)';
            });
        }
    </script>
</body>
</html>
