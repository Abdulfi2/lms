@props([
    'code',
    'title',
    'message',
    'iconBg' => 'bg-blue-100 dark:bg-blue-900/30',
    'iconColor' => 'text-primary',
    'customMessage' => null,
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }"
    x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))"
    :class="{ 'dark': darkMode }">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">

    <title>{{ $code }} - {{ $title }} - {{ config('app.name', 'LMS') }}</title>

    <link rel="icon" type="image/png" href="{{ asset('favicon-32.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon-192.png') }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Figtree', ui-sans-serif, system-ui, -apple-system, sans-serif;
        }

        .error-blob {
            background: radial-gradient(circle at 25% 15%, rgba(59, 130, 246, 0.16), transparent 55%),
                radial-gradient(circle at 85% 85%, rgba(29, 78, 216, 0.12), transparent 50%);
        }

        .error-code {
            font-size: clamp(4.5rem, 12vw, 7rem);
            line-height: 1;
            letter-spacing: -0.03em;
        }
    </style>
</head>

<body class="antialiased min-h-screen error-blob bg-gray-50 dark:bg-gray-900">
    <div class="min-h-screen flex flex-col">
        <!-- Header -->
        <header class="px-6 py-5 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center space-x-2">
                <x-application-logo class="w-9 h-9" />
                <span class="font-bold text-gray-800 dark:text-white">{{ config('app.name', 'LMS') }}</span>
            </a>
            <button @click="darkMode = !darkMode"
                class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800">
                <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                </svg>
                <svg x-show="darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            </button>
        </header>

        <!-- Content -->
        <main class="flex-1 flex items-center justify-center px-6 py-10">
            <div class="max-w-lg w-full text-center">
                <div class="mx-auto w-24 h-24 rounded-3xl {{ $iconBg }} flex items-center justify-center mb-6">
                    <div class="w-12 h-12 {{ $iconColor }}">
                        {{ $icon }}
                    </div>
                </div>

                <p class="error-code font-extrabold {{ $iconColor }} opacity-90">{{ $code }}</p>

                <h1 class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">{{ $title }}</h1>

                <p class="mt-3 text-gray-500 dark:text-gray-400">
                    {{ $message }}
                </p>

                @if ($customMessage)
                    <div class="mt-4 mx-auto max-w-md p-3 rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-sm text-gray-600 dark:text-gray-300">
                        {{ $customMessage }}
                    </div>
                @endif

                <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3">
                    {{ $slot }}

                    @auth
                        <a href="{{ route('dashboard') }}"
                            class="w-full sm:w-auto px-5 py-2.5 bg-primary text-white rounded-lg font-semibold hover:bg-secondary transition text-sm">
                            Ke Dashboard Saya
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                            class="w-full sm:w-auto px-5 py-2.5 bg-primary text-white rounded-lg font-semibold hover:bg-secondary transition text-sm">
                            Masuk
                        </a>
                    @endauth

                    <a href="{{ route('home') }}"
                        class="w-full sm:w-auto px-5 py-2.5 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-semibold hover:bg-gray-100 dark:hover:bg-gray-800 transition text-sm">
                        Kembali ke Beranda
                    </a>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="px-6 py-5 text-center text-xs text-gray-400 dark:text-gray-500">
            Butuh bantuan? Hubungi <a href="mailto:support@lms.com" class="text-primary hover:underline">support@lms.com</a>
        </footer>
    </div>
</body>

</html>
