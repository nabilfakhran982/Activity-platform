<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Activio') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'DM Sans', sans-serif; }
        .font-display { font-family: 'Playfair Display', serif; }

        .auth-bg {
            background: #1a1a18;
            min-height: 100vh;
            position: relative;
            overflow: hidden;
        }
        .auth-bg::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 60% 50% at 80% 30%, rgba(212,163,80,0.12) 0%, transparent 70%),
                radial-gradient(ellipse 40% 60% at 10% 80%, rgba(93,202,165,0.07) 0%, transparent 60%);
            pointer-events: none;
        }

        .auth-card {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.10);
            border-radius: 24px;
            backdrop-filter: blur(12px);
        }

        .auth-input {
            background: rgba(255,255,255,0.06) !important;
            border: 1px solid rgba(255,255,255,0.12) !important;
            border-radius: 12px !important;
            color: #fff !important;
            padding: 12px 16px !important;
            width: 100%;
            font-size: 14px;
            transition: border-color 0.2s;
            outline: none;
        }
        .auth-input::placeholder { color: rgba(255,255,255,0.28); }
        .auth-input:focus { border-color: rgba(212,163,80,0.55) !important; }

        .auth-label {
            color: rgba(255,255,255,0.55);
            font-size: 13px;
            font-weight: 400;
            display: block;
            margin-bottom: 6px;
        }

        .auth-btn {
            background: #D4A350;
            color: #1a1a18;
            font-weight: 500;
            border-radius: 999px;
            padding: 12px 32px;
            font-size: 14px;
            width: 100%;
            border: none;
            cursor: pointer;
            transition: background 0.2s, transform 0.1s;
        }
        .auth-btn:hover { background: #e8b85c; transform: scale(1.01); }

        .auth-link {
            color: #D4A350;
            font-size: 13px;
            text-decoration: none;
            transition: opacity 0.2s;
        }
        .auth-link:hover { opacity: 0.75; }

        .auth-error {
            color: #f87171;
            font-size: 12px;
            margin-top: 4px;
        }

        /* Override Breeze default input styles */
        input[type="text"],
        input[type="email"],
        input[type="password"] {
            background: rgba(255,255,255,0.06) !important;
            border: 1px solid rgba(255,255,255,0.12) !important;
            border-radius: 12px !important;
            color: #fff !important;
            padding: 12px 16px !important;
        }
        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus {
            border-color: rgba(212,163,80,0.55) !important;
            box-shadow: none !important;
            outline: none !important;
        }
        input[type="checkbox"] { accent-color: #D4A350; }

        label { color: rgba(255,255,255,0.55) !important; font-size: 13px !important; }

        /* Breeze error messages */
        .text-red-600 { color: #f87171 !important; font-size: 12px !important; }

        /* Remember me / forgot password row */
        .text-gray-600 { color: rgba(255,255,255,0.45) !important; }
        .text-gray-700 { color: rgba(255,255,255,0.55) !important; }

        /* Breeze primary button override */
        button[type="submit"],
        input[type="submit"] {
            background: #D4A350 !important;
            color: #1a1a18 !important;
            font-weight: 500 !important;
            border-radius: 999px !important;
            border: none !important;
            padding: 12px 32px !important;
            width: 100% !important;
            cursor: pointer !important;
            transition: background 0.2s !important;
            font-size: 14px !important;
        }
        button[type="submit"]:hover { background: #e8b85c !important; }
    </style>
</head>
<body>
<div class="auth-bg flex flex-col items-center justify-center px-4 py-12">

    {{-- Logo --}}
    <a href="/" class="mb-8 font-display text-white text-2xl font-bold tracking-tight">
        Acti<span style="color:#D4A350">vio</span>
    </a>

    {{-- Card --}}
    <div class="auth-card w-full max-w-md px-8 py-10">
        {{ $slot }}
    </div>

    {{-- Footer note --}}
    <p class="mt-8 text-xs" style="color:rgba(255,255,255,0.25)">
        © {{ date('Y') }} Activio. Built in Lebanon 🇱🇧
    </p>

</div>
</body>
</html>