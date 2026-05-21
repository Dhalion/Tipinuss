<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ isset($pageTitle) ? $pageTitle . ' - ' : '' }}{{ __('app.title') }}</title>
    <meta name="description" content="{{ $pageDescription ?? __('app.description') }}">
    
    <!-- OpenGraph Tags -->
    <meta property="og:title" content="{{ $pageTitle ?? __('app.title') }}">
    <meta property="og:description" content="{{ $pageDescription ?? __('app.description') }}">
    <meta property="og:image" content="{{ $pageImage ?? asset('images/logo-full.webp') }}">
    <meta property="og:url" content="{{ $pageUrl ?? url()->current() }}">
    <meta property="og:type" content="website">
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageTitle ?? __('app.title') }}">
    <meta name="twitter:description" content="{{ $pageDescription ?? __('app.description') }}">
    <meta name="twitter:image" content="{{ $pageImage ?? asset('images/logo-full.webp') }}">

    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('images/android-chrome-192x192.png') }}">
    <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('images/android-chrome-512x512.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    @fluxAppearance
</head>

<body class="min-h-screen flex flex-col bg-zinc-50 dark:bg-zinc-900 antialiased">

    @include('layouts.header')

    @include('layouts.sidebar')

    <flux:main class="flex-1">
        {{ $slot }}
    </flux:main>

    @include('layouts.footer')

    @persist('toast')
        <flux:toast />
    @endpersist

    @livewireScripts
    @fluxScripts

</body>

</html>