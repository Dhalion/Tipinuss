<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tipinuss - Online Betting Platform</title>
    <meta name="description" content="Tipinuss - A modern online betting platform">
    <meta property="og:image" content="{{ asset('images/logo-full.webp') }}">
    <meta property="og:type" content="website">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo-minimal.webp') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @fluxAppearance
</head>

<body class="bg-white dark:bg-zinc-900">
    <livewire:bet-status-monitor />
    
    @include('layouts.header')

    <main class="min-h-screen">
        {{ $slot }}
    </main>

    @include('layouts.footer')
    
    @include('components.toast')

    @livewireScripts
    @fluxScripts
</body>

</html>