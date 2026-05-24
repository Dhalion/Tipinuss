<flux:sidebar sticky collapsible="mobile" class="lg:hidden bg-zinc-50 dark:bg-zinc-900 border-r border-zinc-200 dark:border-zinc-700">
    <flux:sidebar.header>
        <flux:brand href="{{ route('main') }}"
                    logo="{{ URL::asset('images/logo-full.webp') }}"
                    name="{{ __('app.title') }}"
                    wire:navigate.hover/>
    </flux:sidebar.header>

    <flux:sidebar.nav>
        <flux:sidebar.item href="{{ route('main') }}" icon="home" wire:navigate.hover>
            {{ __('app.navigation.home') }}
        </flux:sidebar.item>

        @auth
            @if(auth()->user()->isApproved())
                <flux:sidebar.item href="{{ route('bets.list') }}" icon="list-bullet" wire:navigate.hover>
                    {{ __('app.navigation.bets.list') }}
                </flux:sidebar.item>
                <flux:sidebar.item href="{{ route('bets.create') }}" icon="plus-circle" wire:navigate.hover>
                    {{ __('app.navigation.bets.create') }}
                </flux:sidebar.item>
                <flux:sidebar.item href="{{ route('account') }}" icon="user" wire:navigate>
                    {{ __('app.navigation.account') }}
                </flux:sidebar.item>
                @if(auth()->user()->isAdmin())
                    <flux:sidebar.item href="{{ route('admin.users') }}" icon="shield-check" wire:navigate>
                        {{ __('app.navigation.admin') }}
                    </flux:sidebar.item>
                @endif
            @endif
        @endauth

        @guest
            <flux:sidebar.item href="{{ route('login') }}" icon="arrow-right-end-on-rectangle" wire:navigate>
                {{ __('app.navigation.login') }}
            </flux:sidebar.item>
            <flux:sidebar.item href="{{ route('register') }}" icon="user-plus" wire:navigate>
                {{ __('app.navigation.register') }}
            </flux:sidebar.item>
        @endguest
    </flux:sidebar.nav>

    <flux:sidebar.spacer />

    @auth
        @if(auth()->user()->isApproved())
            <flux:sidebar.nav>
                <div class="px-3 py-2 text-sm text-gold-500 font-semibold">
                    @livewire('soapnuts-balance')
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <flux:sidebar.item icon="arrow-right-start-on-rectangle" as="button" type="submit">
                        {{ __('app.navigation.logout') }}
                    </flux:sidebar.item>
                </form>
            </flux:sidebar.nav>
        @else
            <flux:sidebar.nav>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <flux:sidebar.item icon="arrow-right-start-on-rectangle" as="button" type="submit">
                        {{ __('app.navigation.logout') }}
                    </flux:sidebar.item>
                </form>
            </flux:sidebar.nav>
        @endif
    @endauth
</flux:sidebar>
