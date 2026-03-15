<flux:header container>
    <flux:brand href="#" logo="{{ URL::asset('images/logo-full.webp') }}" name="{{ __('app.title') }}" />

    <flux:navbar class="-mb-px">
        <flux:nav-item href="{{ route('home') }}" :active="request()->routeIs('home')">
            <flux:navbar.item icon="home" href="#" current>{{ __('app.navigation.home') }}</flux:navbar.item>
        </flux:nav-item>
    </flux:navbar>

    <flux:spacer />

    <flux:navbar class="me-4">
        <flux:navbar.item class="max-lg:hidden" icon="user" href="#" label="{{ __('app.navigation.account') }}" />
    </flux:navbar>
</flux:header>