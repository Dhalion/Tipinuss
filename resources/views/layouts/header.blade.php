<flux:header container class="bg-zinc-50 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700">
    <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

    <flux:brand href="{{ route('main') }}"
                logo="{{ URL::asset('images/tipinuss-waschnusskönig.webp') }}"
                name="{{ __('app.title') }}"
                wire:navigate.hover/>

    <flux:navbar class="-mb-px max-lg:hidden">
        @auth
            @if(auth()->user()->isApproved())
                <flux:navbar.item href="{{ route('bets.list') }}"
                                  wire:navigate.hover>{{ __('app.navigation.bets.list') }}</flux:navbar.item>
                <flux:navbar.item href="{{ route('bets.create') }}"
                                  wire:navigate.hover>{{ __('app.navigation.bets.create') }}</flux:navbar.item>
                @if(auth()->user()->isAdmin())
                    <flux:navbar.item href="{{ route('admin.users') }}"
                                      wire:navigate.hover>{{ __('app.navigation.admin') }}</flux:navbar.item>
                @endif
            @endif
        @endauth

        <flux:navbar.item href="{{ route('main') }}"
                          wire:navigate.hover>{{ __('app.navigation.home') }}</flux:navbar.item>
    </flux:navbar>

    <flux:spacer/>

    @auth
        @if(auth()->user()->isApproved())
            <span class="text-sm text-gold-400 mr-2 whitespace-nowrap font-semibold">
                @livewire('soapnuts-balance')
            </span>
            <flux:dropdown>
                <flux:profile name="{{ Auth::user()->name }}" class="max-lg:hidden"/>
                <flux:menu>
                    <flux:menu.item href="{{ route('account') }}" wire:navigate>{{ __('app.navigation.account') }}</flux:menu.item>
                    <form method="POST" action="{{ route('logout') }}" class="contents">
                        @csrf
                        <flux:menu.item as="button" type="submit">
                            {{ __('app.navigation.logout') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        @else
            <form method="POST" action="{{ route('logout') }}" class="contents">
                @csrf
                <flux:button type="submit" variant="ghost" size="sm" icon="arrow-right-start-on-rectangle">
                    {{ __('app.navigation.logout') }}
                </flux:button>
            </form>
        @endif
    @endauth

    @guest
        <div class="flex items-center gap-2">
            <flux:navbar.item href="{{ route('login') }}" wire:navigate class="text-sm">{{ __('app.navigation.login') }}
            </flux:navbar.item>
            <flux:navbar.item href="{{ route('register') }}" wire:navigate class="text-sm">{{ __('app.navigation.register') }}
            </flux:navbar.item>
        </div>
    @endguest
</flux:header>
