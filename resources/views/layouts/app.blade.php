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
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-500">
            @include('layouts.navigation')
            @if (session('success'))
                <div class="p-4 rounded mb-4 w-max h-max fixed top-15 left-0 bg-green-100 text-green-800 message">
                    {{ session('success') }}
                    <button class="text-xl font-bold ml-4 messageBtn">×</button>
                </div>
            @endif
            @if ($errors->any())
                @foreach ($errors->all() as $e)
                    <div class="p-4 rounded mb-4 w-max h-max fixed top-15 left-0 bg-red-100 text-red-800 message">
                        {{ $e }}
                        <button class="text-xl font-bold ml-4 messageBtn">×</button>
                    </div>
                @endforeach
            @endif
            @if (session('info'))
                <div class="p-4 rounded mb-4 w-max h-max fixed top-15 left-0 bg-red-100 text-red-800 message">
                    {{ session('info') }}
                    <button class="text-xl font-bold ml-4 messageBtn">×</button>
                </div>
            @endif

            <main>
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
