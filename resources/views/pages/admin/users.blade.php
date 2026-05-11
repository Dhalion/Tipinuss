<div class="min-h-screen bg-zinc-50 dark:bg-zinc-900 py-8">
    <div class="max-w-7xl mx-auto px-4">

        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <flux:heading size="xl">{{ __('admin.users.title') }}</flux:heading>
                <flux:text class="mt-1">{{ trans_choice('admin.users.registered_count', $users->count(), ['count' => $users->count()]) }}</flux:text>
            </div>
            <flux:button href="{{ route('admin.organisations') }}" wire:navigate variant="ghost" size="sm" icon="building-office">
                {{ __('admin.organisations.title') }}
            </flux:button>
        </div>

        @if ($errors->any())
            <flux:callout icon="exclamation-triangle" variant="danger" class="mb-6">
                @foreach ($errors->all() as $error)
                    <flux:text>{{ $error }}</flux:text>
                @endforeach
            </flux:callout>
        @endif

        <flux:card class="p-0 overflow-hidden">
            <div class="overflow-x-auto">
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>{{ __('admin.users.table.user') }}</flux:table.column>
                        <flux:table.column class="text-right">{{ __('admin.users.table.balance') }}</flux:table.column>
                        <flux:table.column class="text-center">{{ __('admin.users.table.admin') }}</flux:table.column>
                        <flux:table.column>{{ __('admin.users.table.organisation') }}</flux:table.column>
                        <flux:table.column class="text-right">{{ __('admin.users.table.bets') }}</flux:table.column>
                        <flux:table.column>{{ __('admin.users.table.registered') }}</flux:table.column>
                        <flux:table.column class="text-right">{{ __('admin.users.table.actions') }}</flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @foreach ($users as $user)
                            <flux:table.row>
                                <flux:table.cell>
                                    <div class="font-medium text-zinc-900 dark:text-white whitespace-nowrap">{{ $user->name }}</div>
                                    <div class="text-xs text-zinc-400 dark:text-zinc-500">{{ $user->email }}</div>
                                </flux:table.cell>

                                <flux:table.cell class="text-right">
                                    <div class="font-mono font-semibold whitespace-nowrap">
                                        {{ number_format((float) $user->soapnuts, 0, ',', '.') }}
                                    </div>
                                    <form wire:submit="adjustBalance('{{ $user->id }}')" class="mt-1 flex gap-1 justify-end items-center">
                                        <flux:input
                                            type="number"
                                            wire:model="balanceAdjustments.{{ $user->id }}"
                                            placeholder="{{ __('admin.users.balance_placeholder') }}"
                                            size="sm"
                                            class="w-20 text-center"
                                        />
                                        <flux:button type="submit" size="sm" variant="ghost">{{ __('admin.users.apply') }}</flux:button>
                                    </form>
                                </flux:table.cell>

                                <flux:table.cell class="text-center">
                                    <flux:button
                                        wire:click="toggleAdmin('{{ $user->id }}')"
                                        variant="{{ $user->is_admin ? 'warning' : 'ghost' }}"
                                        size="sm"
                                        icon="{{ $user->is_admin ? 'star' : 'star' }}"
                                        title="{{ $user->is_admin ? __('admin.users.revoke_admin') : __('admin.users.make_admin') }}"
                                    >
                                        {{ $user->is_admin ? '★' : '☆' }}
                                    </flux:button>
                                </flux:table.cell>

                                <flux:table.cell>
                                    <flux:select
                                        wire:change="assignOrganisation('{{ $user->id }}', $event.target.value)"
                                        size="sm"
                                        class="max-w-[180px]"
                                    >
                                        <option value="">{{ __('admin.organisations.none') }}</option>
                                        @foreach ($organisations as $org)
                                            <option value="{{ $org->id }}" {{ $user->organisation_id === $org->id ? 'selected' : '' }}>
                                                {{ $org->name }}
                                            </option>
                                        @endforeach
                                    </flux:select>
                                </flux:table.cell>

                                <flux:table.cell class="text-right font-mono">
                                    {{ $user->user_bets_count }}
                                </flux:table.cell>

                                <flux:table.cell class="text-zinc-500 dark:text-zinc-400 text-sm whitespace-nowrap">
                                    {{ $user->created_at?->format('d.m.Y') }}
                                </flux:table.cell>

                                <flux:table.cell class="text-right">
                                    <flux:button
                                        wire:click="deleteUser('{{ $user->id }}')"
                                        wire:confirm="{{ __('admin.users.confirm_delete') }}"
                                        variant="danger"
                                        size="sm"
                                        icon="trash"
                                    />
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            </div>
        </flux:card>

    </div>
</div>
