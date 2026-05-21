<div class="py-6">
    <div class="max-w-7xl mx-auto px-4">
        <div class="mb-8">
            <flux:heading size="xl">{{ __('admin.organisations.title') }}</flux:heading>
            <flux:text class="mt-2">{{ __('admin.organisations.description') }}</flux:text>
        </div>

        @if (session('status'))
            <flux:callout icon="check-circle" variant="success" class="mb-6">
                <flux:text>{{ session('status') }}</flux:text>
            </flux:callout>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-1">
                <flux:card>
                    <flux:heading size="lg" class="mb-4">{{ __('admin.organisations.create_title') }}</flux:heading>

                    <form wire:submit="createOrganisation" class="space-y-4">
                        <flux:input
                            wire:model="newOrganisationName"
                            label="{{ __('admin.organisations.name_label') }}"
                            placeholder="{{ __('admin.organisations.name_placeholder') }}"
                        />

                        <flux:button type="submit" variant="primary" class="w-full">
                            {{ __('admin.organisations.create_button') }}
                        </flux:button>
                    </form>
                </flux:card>
            </div>

            <div class="lg:col-span-2 space-y-4">
                @forelse ($organisations as $organisation)
                    <flux:card>
                        <div class="flex items-center justify-between mb-4">
                            <flux:heading size="lg">{{ $organisation->name }}</flux:heading>
                            <flux:button
                                wire:click="deleteOrganisation('{{ $organisation->id }}')"
                                wire:confirm="{{ __('admin.organisations.confirm_delete') }}"
                                variant="danger"
                                size="sm"
                            >
                                {{ __('admin.organisations.delete') }}
                            </flux:button>
                        </div>

                        <flux:text class="mb-4">
                            {{ trans_choice('admin.organisations.member_count', $organisation->users->count(), ['count' => $organisation->users->count()]) }}
                        </flux:text>

                        @if ($organisation->users->isNotEmpty())
                            <div class="space-y-2 mb-4">
                                @foreach ($organisation->users as $member)
                                    <div class="flex items-center justify-between py-1">
                                        <span class="text-sm text-zinc-700 dark:text-zinc-300">{{ $member->name }}</span>
                                        <flux:button
                                            wire:click="assignUserToOrganisation('{{ $member->id }}', '')"
                                            variant="ghost"
                                            size="xs"
                                            class="text-zinc-400 hover:text-red-500"
                                        >
                                            {{ __('admin.organisations.remove') }}
                                        </flux:button>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </flux:card>
                @empty
                    <flux:card class="p-8 text-center">
                        <flux:text>{{ __('admin.organisations.empty') }}</flux:text>
                    </flux:card>
                @endforelse
            </div>
        </div>

        @if ($allUsers->isNotEmpty())
            <flux:card class="mt-8">
                <flux:heading size="lg" class="mb-4">{{ __('admin.organisations.assign_users') }}</flux:heading>
                <div class="space-y-3">
                    @foreach ($allUsers as $user)
                        <div class="flex items-center justify-between py-2 border-b border-zinc-100 dark:border-zinc-700 last:border-0">
                            <div>
                                <span class="font-medium text-zinc-900 dark:text-white text-sm">{{ $user->name }}</span>
                                <span class="ml-2 text-xs text-zinc-400 dark:text-zinc-500">{{ $user->email }}</span>
                            </div>
                            <flux:select
                                wire:change="assignUserToOrganisation('{{ $user->id }}', $event.target.value)"
                                size="sm"
                                class="min-w-0"
                            >
                                <option value="">{{ __('admin.organisations.no_group') }}</option>
                                @foreach ($organisations as $organisation)
                                    <option
                                        value="{{ $organisation->id }}"
                                        {{ $user->organisation_id === $organisation->id ? 'selected' : '' }}
                                    >
                                        {{ $organisation->name }}
                                    </option>
                                @endforeach
                            </flux:select>
                        </div>
                    @endforeach
                </div>
            </flux:card>
        @endif
    </div>
</div>
