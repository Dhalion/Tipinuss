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
    <link rel="apple-touch-icon" href="{{ asset('images/logo-minimal.webp') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    @fluxAppearance
</head>

<body class="bg-white dark:bg-zinc-900">

    @include('layouts.header')

    <main class="min-h-screen">
        {{ $slot }}
    </main>

    @include('layouts.footer')

    @livewireScripts
    @fluxScripts

</body>

</html>