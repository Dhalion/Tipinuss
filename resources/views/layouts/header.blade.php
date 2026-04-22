<flux:header container>
    <flux:brand href="{{ route('main') }}"
                logo="{{ URL::asset('images/logo-full.webp') }}"
                name="{{ __('app.title') }}"/>

    <flux:navbar class="-mb-px">
        <flux:navbar.item href="{{ route('main') }}"
                          wire:navigate.hover>{{ __('app.navigation.home') }}</flux:navbar.item>
        @auth
            <flux:navbar.item href="{{ route('bet.list') }}"
                              wire:navigate.hover>{{ __('app.navigation.bets.list') }}</flux:navbar.item>
            <flux:navbar.item href="{{ route('bet.create') }} "
                              wire:navigate.hover>{{ __('app.navigation.bets.create') }}</flux:navbar.item>
        @endauth
    </flux:navbar>

    <flux:spacer/>


    @auth
        <span class="text-sm text-amber-300 dark:text-amber-300 mr-4">
            {{ number_format(auth()->user()->soapnuts) }}
            <span class="text-lg">🌰</span>
        </span>
        <flux:dropdown>
            <flux:profile name="{{ Auth::user()->name }}"/>
            <flux:menu>

                <flux:menu.item href="{{ route('account') }}">{{ __('app.navigation.account') }}</flux:menu.item>
                <form method="POST" action="{{ route('logout') }}" class="contents">
                    @csrf
                    <flux:menu.item as="button" type="submit">
                        {{ __('app.navigation.logout') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    @endauth

    @guest
        <div class="flex items-center gap-2">
            <flux:navbar.item href="{{ route('login') }}" class="text-sm">{{ __('app.navigation.login') }}
            </flux:navbar.item>
            <flux:navbar.item href="{{ route('register') }}" class="text-sm">{{ __('app.navigation.register') }}
            </flux:navbar.item>
        </div>
    @endguest
</flux:header>
