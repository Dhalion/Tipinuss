<div class="py-8">
    <div class="max-w-7xl mx-auto px-4">

        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <flux:heading size="xl">{{ __('admin.organisations.title') }}</flux:heading>
                <flux:text class="mt-2">{{ __('admin.organisations.description') }}</flux:text>
            </div>
            @if ($organisations->isNotEmpty())
                <flux:button wire:click="$toggle('showCreateForm')" variant="primary" size="sm" icon="plus">
                    {{ __('admin.organisations.create_button') }}
                </flux:button>
            @endif
        </div>

        @if ($showCreateForm)
            <flux:card class="mb-6 p-6">
                <flux:heading size="lg" class="mb-4">{{ __('admin.organisations.create_title') }}</flux:heading>
                <form wire:submit="createOrganisation" class="flex gap-3 items-end">
                    <div class="flex-1">
                        <flux:input
                            wire:model="newOrganisationName"
                            label="{{ __('admin.organisations.name_label') }}"
                            placeholder="{{ __('admin.organisations.name_placeholder') }}"
                        />
                    </div>
                    <flux:button type="submit" variant="primary">
                        {{ __('admin.organisations.create_button') }}
                    </flux:button>
                </form>
            </flux:card>
        @endif

        @if ($organisations->isEmpty())
            <flux:card class="p-12 text-center">
                <flux:heading class="mb-2">{{ __('admin.organisations.empty') }}</flux:heading>
                <flux:text class="mb-4">{{ __('admin.organisations.description') }}</flux:text>
                <flux:button wire:click="$set('showCreateForm', true)" variant="primary" icon="plus">
                    {{ __('admin.organisations.create_button') }}
                </flux:button>
            </flux:card>
        @else
            <flux:card class="overflow-hidden">
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>{{ __('admin.organisations.name_label') }}</flux:table.column>
                        <flux:table.column align="center">{{ __('admin.organisations.members') }}</flux:table.column>
                        <flux:table.column>{{ __('admin.users.table.registered') }}</flux:table.column>
                        <flux:table.column align="end">{{ __('admin.users.table.actions') }}</flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @foreach ($organisations as $organisation)
                            <flux:table.row wire:key="org-{{ $organisation->id }}">
                                <flux:table.cell>
                                    <a href="{{ route('admin.organisations.detail', $organisation) }}" wire:navigate class="font-medium text-zinc-900 dark:text-white hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
                                        {{ $organisation->name }}
                                    </a>
                                </flux:table.cell>
                                <flux:table.cell class="text-center font-mono text-sm text-zinc-600 dark:text-zinc-400">
                                    {{ $organisation->users->count() }}
                                </flux:table.cell>
                                <flux:table.cell class="text-sm text-zinc-500 dark:text-zinc-400 whitespace-nowrap">
                                    {{ $organisation->created_at?->format('d.m.Y') }}
                                </flux:table.cell>
                                <flux:table.cell class="text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <flux:button
                                            href="{{ route('admin.organisations.detail', $organisation) }}"
                                            wire:navigate
                                            variant="ghost"
                                            size="sm"
                                            icon="chevron-right"
                                        >
                                            {{ __('admin.organisations.manage') }}
                                        </flux:button>
                                        <flux:button
                                            wire:click="deleteOrganisation('{{ $organisation->id }}')"
                                            wire:confirm="{{ __('admin.organisations.confirm_delete') }}"
                                            variant="danger"
                                            size="sm"
                                            icon="trash"
                                        />
                                    </div>
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            </flux:card>
        @endif

    </div>
</div>
