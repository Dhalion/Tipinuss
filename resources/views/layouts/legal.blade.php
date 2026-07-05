<!DOCTYPE html>
<html lang="de" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle }} - {{ __('app.title') }}</title>
    <meta name="robots" content="noindex, nofollow">

    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    @vite(['resources/css/app.css'])
</head>

<body class="min-h-screen flex flex-col bg-zinc-50 dark:bg-zinc-900 antialiased">

    <main class="flex-1">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <a href="{{ route('main') }}"
               class="inline-flex items-center gap-1 text-sm text-zinc-500 dark:text-zinc-400 hover:text-primary-500 dark:hover:text-primary-400 mb-8 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4"><path fill-rule="evenodd" d="M17 10a.75.75 0 01-.75.75H5.612l4.158 3.96a.75.75 0 11-1.04 1.08l-5.5-5.25a.75.75 0 010-1.08l5.5-5.25a.75.75 0 111.04 1.08L5.612 9.25H16.25A.75.75 0 0117 10z" clip-rule="evenodd"/></svg>
                {{ __('app.legal.back_to_home') }}
            </a>

            <article class="prose prose-zinc dark:prose-invert max-w-none">
                <h1>{{ $pageTitle }}</h1>
                {{ $slot }}
            </article>

            <div class="mt-12 pt-6 border-t border-zinc-200 dark:border-zinc-700">
                <a href="{{ route('main') }}"
                   class="text-sm text-zinc-500 dark:text-zinc-400 hover:text-primary-500 dark:hover:text-primary-400 transition-colors">
                    &larr; {{ __('app.legal.back_to_home') }}
                </a>
            </div>
        </div>
    </main>



</body>
</html>
