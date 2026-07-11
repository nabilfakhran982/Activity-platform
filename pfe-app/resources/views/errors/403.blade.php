<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Access Forbidden | Activio</title>
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
            overflow: hidden;
            position: relative;
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
            background: #FF6B6B;
            top: -100px;
            left: -100px;
            animation-duration: 25s;
        }

        .blob-2 {
            width: 250px;
            height: 250px;
            background: #FFA500;
            bottom: -50px;
            right: -50px;
            animation-duration: 30s;
            animation-delay: -5s;
        }

        .blob-3 {
            width: 200px;
            height: 200px;
            background: #FF6B6B;
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

        /* Lock Icon with animation */
        .lock-icon {
            font-size: 100px;
            margin-bottom: 30px;
            animation: shake 2s ease-in-out infinite;
            color: #FF6B6B;
        }

        @keyframes shake {
            0%, 100% {
                transform: rotate(0deg);
            }
            25% {
                transform: rotate(-5deg);
            }
            75% {
                transform: rotate(5deg);
            }
        }

        /* 403 Number Animation */
        .error-code {
            font-family: 'DM Display', sans-serif;
            font-size: clamp(120px, 25vw, 200px);
            font-weight: 700;
            background: linear-gradient(135deg, #FF6B6B 0%, #FFA500 100%);
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
            background: linear-gradient(135deg, #FF6B6B 0%, #FF5252 100%);
            color: #fff;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(255, 107, 107, 0.4);
        }

        .btn-primary:active {
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: rgba(255, 107, 107, 0.1);
            color: #FF6B6B;
            border: 2px solid #FF6B6B;
        }

        .btn-secondary:hover {
            background: rgba(255, 107, 107, 0.2);
            transform: translateY(-3px);
        }

        .btn-secondary:active {
            transform: translateY(-1px);
        }

        .icon-small {
            font-size: 16px;
        }

        /* Additional info box */
        .info-box {
            margin-top: 50px;
            padding: 20px;
            background: rgba(255, 107, 107, 0.08);
            border: 1px solid rgba(255, 107, 107, 0.3);
            border-radius: 12px;
            animation: fadeInUp 0.8s ease-out 0.4s both;
        }

        .info-box p {
            font-size: 14px;
            color: #a09890;
            margin: 0;
        }

        .info-box strong {
            color: #FF6B6B;
        }

        /* Responsive */
        @media (max-width: 600px) {
            .error-code {
                font-size: 80px;
                margin-bottom: -10px;
            }

            .lock-icon {
                font-size: 70px;
            }

            .cta-buttons {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <!-- Animated Background Blobs -->
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    <div class="blob blob-3"></div>

    <!-- Main Content -->
    <div class="container">
        <!-- Lock Icon -->
        <div class="lock-icon">
            <i class="fas fa-lock"></i>
        </div>

        <!-- Error Code -->
        <div class="error-code">403</div>

        <!-- Heading -->
        <h1 class="heading">Access Forbidden</h1>

        <!-- Description -->
        <p class="description">
            Sorry, you don't have permission to access this resource. Your request has been denied by the server.
        </p>

        <!-- Action Buttons -->
        <div class="cta-buttons">
            <a href="/" class="btn btn-primary">
                <i class="fas fa-home icon-small"></i>
                Go to Home
            </a>
            <a href="javascript:history.back()" class="btn btn-secondary">
                <i class="fas fa-arrow-left icon-small"></i>
                Go Back
            </a>
        </div>

        <!-- Info Box -->
        <div class="info-box">
            <p>
                If you believe this is a mistake, please <strong>contact us</strong> or try logging in with a different account that has the necessary permissions.
            </p>
        </div>
    </div>
</body>
</html>
