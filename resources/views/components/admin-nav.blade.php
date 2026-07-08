<nav class="flex gap-1 p-1 rounded-xl bg-zinc-100 dark:bg-zinc-800 w-fit mb-6" role="tablist">
    <a
        href="{{ route('admin.users') }}"
        wire:navigate
        role="tab"
        @class([
            'px-4 py-2 rounded-lg text-sm font-medium transition-colors',
            'bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white shadow-sm' => request()->routeIs('admin.users'),
            'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white' => !request()->routeIs('admin.users'),
        ])
    >
        {{ __('admin.users.title') }}
    </a>
    <a
        href="{{ route('admin.organisations') }}"
        wire:navigate
        role="tab"
        @class([
            'px-4 py-2 rounded-lg text-sm font-medium transition-colors',
            'bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white shadow-sm' => request()->routeIs('admin.organisations*'),
            'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white' => !request()->routeIs('admin.organisations*'),
        ])
    >
        {{ __('admin.organisations.title') }}
    </a>
    <a
        href="{{ route('admin.beta-keys') }}"
        wire:navigate
        role="tab"
        @class([
            'px-4 py-2 rounded-lg text-sm font-medium transition-colors',
            'bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white shadow-sm' => request()->routeIs('admin.beta-keys'),
            'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white' => !request()->routeIs('admin.beta-keys'),
        ])
    >
        {{ __('admin.beta_keys.nav_title') }}
    </a>
</nav>
