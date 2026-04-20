<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    <div class="min-h-screen flex flex-col">
        @include('client.includes.navbar')


        <!-- Hero Section -->
        @if (isset($hero))
            <section class="bg-gradient-to-r from-primary to-secondary text-white py-24">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    {{ $hero }}
                </div>
            </section>
        @endif

        <div class="flex-1 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
            <div class="w-full max-w-4xl">
                {{ $slot }}
            </div>
        </div>

        @include('client.includes.footer')

        <x-toast position="top-right" />
    </div>
</body>

</html>
