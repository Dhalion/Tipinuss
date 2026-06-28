<!DOCTYPE html>
<html lang="de" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle }} - {{ __('app.title') }}</title>
    <meta name="description" content="{{ $pageDescription ?? __('app.description') }}">
    <meta name="robots" content="noindex, nofollow">

    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('images/android-chrome-192x192.png') }}">
    <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('images/android-chrome-512x512.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>:root.dark { color-scheme: dark; }</style>
</head>

<body class="min-h-screen flex flex-col bg-zinc-50 dark:bg-zinc-900 antialiased">

    <main class="flex-1">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

            <a href="{{ route('main') }}"
               wire:navigate.hover
               class="inline-flex items-center gap-1.5 text-sm font-medium text-zinc-500 dark:text-zinc-400 hover:text-primary-500 dark:hover:text-primary-400 mb-10 transition-colors group">
                <flux:icon.arrow-left class="h-4 w-4 transition-transform group-hover:-translate-x-0.5" />
                {{ __('app.legal.back_to_home') }}
            </a>

            <article class="text-zinc-700 dark:text-zinc-300 leading-relaxed text-sm sm:text-base">
                <h1 class="!mb-0 !text-3xl !font-bold !tracking-tight text-zinc-900 dark:text-white">{{ $pageTitle }}</h1>
                <hr class="!mt-4 !mb-8 !border-zinc-200 dark:!border-zinc-700">

                {{ $slot }}
            </article>

            <div class="mt-12 pt-8 border-t border-zinc-200 dark:border-zinc-700 space-y-6">
                <div class="text-xs text-zinc-400 dark:text-zinc-500 leading-relaxed space-y-2">
                    <p>{{ __('app.legal.disclaimer') }}</p>
                    @if (Lang::has('app.legal.disclaimer_long'))
                        <p class="text-zinc-400 dark:text-zinc-500">{{ __('app.legal.disclaimer_long') }}</p>
                    @endif
                </div>

                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-zinc-500 dark:text-zinc-400">
                    <a href="{{ route('legal.datenschutz') }}"
                       wire:navigate.hover
                       class="hover:text-primary-500 dark:hover:text-primary-400 transition-colors">
                        {{ __('app.legal.datenschutz') }}
                    </a>
                    <a href="{{ route('legal.agb') }}"
                       wire:navigate.hover
                       class="hover:text-primary-500 dark:hover:text-primary-400 transition-colors">
                        {{ __('app.legal.agb') }}
                    </a>
                    <span class="text-zinc-300 dark:text-zinc-600">·</span>
                    <span class="text-zinc-400 dark:text-zinc-500">
                        &copy; {{ date('Y') }} {{ __('app.title') }}
                    </span>
                    <span class="text-zinc-300 dark:text-zinc-600">·</span>
                    <a href="{{ route('main') }}"
                       wire:navigate.hover
                       class="hover:text-primary-500 dark:hover:text-primary-400 transition-colors">
                        {{ __('app.legal.back_to_home') }}
                    </a>
                </div>
            </div>
        </div>
    </main>

    @persist('toast')
        <flux:toast />
    @endpersist

    @livewireScripts
    @fluxScripts

</body>

</html>
