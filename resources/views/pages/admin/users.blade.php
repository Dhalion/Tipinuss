<div class="py-8">
    <div class="max-w-7xl mx-auto px-4">

        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <flux:heading size="xl">{{ __('admin.users.title') }}</flux:heading>
                <flux:text class="mt-1">{{ trans_choice('admin.users.registered_count', $users->count(), ['count' => $users->count()]) }}</flux:text>
            </div>
            <div class="flex gap-2 items-center">
                <flux:select wire:model.live="approvalFilter" size="sm" class="w-44">
                    <option value="">{{ __('admin.users.filter_all') }}</option>
                    <option value="approved">{{ __('admin.users.filter_approved') }}</option>
                    <option value="pending">{{ __('admin.users.filter_pending') }}</option>
                </flux:select>
                <flux:button href="{{ route('admin.organisations') }}" wire:navigate variant="ghost" size="sm" icon="building-office">
                    {{ __('admin.organisations.title') }}
                </flux:button>
                <flux:button href="{{ route('admin.beta-keys') }}" wire:navigate variant="ghost" size="sm" icon="key">
                    {{ __('admin.beta_keys.nav_title') }}
                </flux:button>
            </div>
        </div>

        @if ($pendingCount > 0 && $approvalFilter !== 'pending')
            <flux:callout icon="clock" variant="warning" class="mb-6">
                <div class="flex items-center justify-between w-full">
                    <flux:text>
                        {{ trans_choice('admin.users.pending_notification', $pendingCount, ['count' => $pendingCount]) }}
                    </flux:text>
                    <flux:button wire:click="setFilter('pending')" size="sm">
                        {{ __('admin.users.pending_view') }}
                    </flux:button>
                </div>
            </flux:callout>
        @endif

        @if ($errors->any())
            <flux:callout icon="exclamation-triangle" variant="danger" class="mb-6">
                @foreach ($errors->all() as $error)
                    <flux:text>{{ $error }}</flux:text>
                @endforeach
            </flux:callout>
        @endif

<flux:card>
    <div class="overflow-x-auto -mx-6">
        <flux:table class="min-w-[700px] w-full">
            <flux:table.columns>
                <flux:table.column class="w-[200px]">{{ __('admin.users.table.user') }}</flux:table.column>
                <flux:table.column class="text-right w-[140px]">{{ __('admin.users.table.balance') }}</flux:table.column>
                <flux:table.column class="text-center w-[100px]">{{ __('admin.users.table.status') }}</flux:table.column>
                <flux:table.column class="text-center w-[120px]">{{ __('admin.users.table.admin') }}</flux:table.column>
                <flux:table.column class="w-[180px]">{{ __('admin.users.table.organisation') }}</flux:table.column>
                <flux:table.column class="text-center w-[60px]">{{ __('admin.users.table.bets') }}</flux:table.column>
                <flux:table.column class="w-[100px]">{{ __('admin.users.table.registered') }}</flux:table.column>
                <flux:table.column class="text-right w-[60px]">{{ __('admin.users.table.actions') }}</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @foreach ($users as $user)
                    <flux:table.row>
                        <flux:table.cell class="align-middle">
                            <div class="font-medium text-zinc-900 dark:text-white">{{ $user->name }}</div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400 truncate max-w-[180px]">{{ $user->email }}</div>
                        </flux:table.cell>

                        <flux:table.cell class="text-right align-middle whitespace-nowrap">
                            <div class="font-mono font-semibold text-zinc-900 dark:text-white">
                                {{ number_format((float) $user->soapnuts, 0, ',', '.') }}
                            </div>
                            <form wire:submit="adjustBalance('{{ $user->id }}')" class="mt-1.5 flex gap-1 justify-end items-center">
                                <flux:input
                                    type="number"
                                    wire:model="balanceAdjustments.{{ $user->id }}"
                                    placeholder="+/-"
                                    size="xs"
                                    class="w-16 text-center"
                                />
                                <flux:button type="submit" size="xs" variant="ghost">{{ __('admin.users.apply') }}</flux:button>
                            </form>
                        </flux:table.cell>

                        <flux:table.cell class="text-center align-middle whitespace-nowrap">
                            @if ($user->isApproved())
                                <flux:badge color="green" size="sm">{{ __('admin.users.status_approved') }}</flux:badge>
                            @else
                                <flux:badge color="yellow" size="sm">{{ __('admin.users.status_pending') }}</flux:badge>
                            @endif
                        </flux:table.cell>

                        <flux:table.cell class="text-center align-middle whitespace-nowrap">
                            <div class="flex items-center justify-center gap-1.5">
                                <flux:badge color="{{ $user->is_admin ? 'purple' : 'gray' }}" size="sm">
                                    {{ $user->is_admin ? __('admin.users.admin_role') : __('admin.users.user_role') }}
                                </flux:badge>
                                <flux:button
                                    wire:click="toggleAdmin('{{ $user->id }}')"
                                    variant="ghost"
                                    size="xs"
                                    icon="arrow-path"
                                    title="{{ $user->is_admin ? __('admin.users.revoke_admin') : __('admin.users.make_admin') }}"
                                />
                            </div>
                        </flux:table.cell>

                        <flux:table.cell class="align-middle">
                            <div class="flex gap-1.5 items-center">
                                <flux:select
                                    wire:change="assignOrganisation('{{ $user->id }}', $event.target.value)"
                                    size="sm"
                                    class="min-w-0"
                                >
                                    <option value="">{{ __('admin.organisations.none') }}</option>
                                    @foreach ($organisations as $org)
                                        <option value="{{ $org->id }}" {{ $user->organisation_id === $org->id ? 'selected' : '' }}>
                                            {{ $org->name }}
                                        </option>
                                    @endforeach
                                </flux:select>
                                @if (! $user->isApproved())
                                    <flux:button
                                        wire:click="approveUser('{{ $user->id }}', '{{ $user->organisation_id }}')"
                                        size="sm"
                                        variant="primary"
                                        icon="check"
                                        class="shrink-0"
                                        title="{{ __('admin.users.approve_title') }}"
                                    />
                                @endif
                            </div>
                        </flux:table.cell>

                        <flux:table.cell class="text-center align-middle font-mono text-sm text-zinc-600 dark:text-zinc-400">
                            {{ $user->user_bets_count }}
                        </flux:table.cell>

                        <flux:table.cell class="align-middle text-sm text-zinc-500 dark:text-zinc-400 whitespace-nowrap">
                            {{ $user->created_at?->format('d.m.Y') }}
                        </flux:table.cell>

                        <flux:table.cell class="text-right align-middle whitespace-nowrap">
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
