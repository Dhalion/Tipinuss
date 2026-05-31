<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coming Soon – {{ __('app.title') }}</title>
    <meta name="description" content="{{ __('app.title_long') }} — {{ __('app.hero_tagline') }}">

    <meta property="og:title" content="{{ __('app.title') }}">
    <meta property="og:description" content="{{ __('app.description') }}">
    <meta property="og:image" content="{{ asset('images/tipinuss-waschnusskönig-winning.webp') }}">
    <meta property="og:type" content="website">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ __('app.title') }}">
    <meta name="twitter:description" content="{{ __('app.description') }}">
    <meta name="twitter:image" content="{{ asset('images/tipinuss-waschnusskönig-winning.webp') }}">

    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('images/android-chrome-192x192.png') }}">
    <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('images/android-chrome-512x512.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900&display=swap" rel="stylesheet">

    @vite('resources/css/app.css')
    @fluxAppearance
</head>
<body class="min-h-screen flex flex-col items-center justify-center bg-zinc-50 dark:bg-zinc-900 antialiased px-6 py-12 sm:py-16">
    <main class="flex flex-col items-center gap-10 sm:gap-16 w-full max-w-2xl mx-auto text-center">

        <h1 class="text-4xl sm:text-5xl lg:text-7xl font-black text-gold-500 dark:text-gold-400 tracking-tight">
            {{ __('app.coming_soon') }}
        </h1>

        <img src="{{ asset('images/tipinuss-waschnusskönig-winning.webp') }}"
             alt="{{ __('app.title') }}"
             class="w-full max-w-[280px] sm:max-w-md object-contain drop-shadow-2xl">

        <p class="text-base sm:text-lg lg:text-xl text-zinc-500 dark:text-zinc-400 leading-relaxed max-w-md">
            {{ __('app.coming_soon_tagline') }}
        </p>

        <p class="text-sm text-zinc-400 dark:text-zinc-500">
            {{ __('app.title_long') }}
        </p>

    </main>
</body>
</html>
