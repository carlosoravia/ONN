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
        @livewireStyles
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans bg-gray-500 antialiased">
        @if (session('success'))
            <div class="p-4 rounded mb-4 w-max h-max bg-green-100 text-green-800 message">
                {{ session('success') }}
                <button class="text-xl font-bold ml-4 messageBtn">×</button>
            </div>
        @endif
        @if ($errors->any())
            @foreach ($errors->all() as $e)
                <div class="p-4 rounded mb-4 w-max h-max bg-red-100 text-red-800 message">
                    {{ $e }}
                    <button class="text-xl font-bold ml-4 messageBtn">×</button>
                </div>
            @endforeach
        @endif
        @if (session('info'))
            <div class="p-4 rounded mb-4 w-max h-max bg-red-100 text-red-800 message">
                {{ session('info') }}
                <button class="text-xl font-bold ml-4 messageBtn">×</button>
            </div>
        @endif
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            <div>
                <x-application-logo class="h-40 bg-center text-dark-500" />
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>
        @livewireScripts
    </body>
</html>
