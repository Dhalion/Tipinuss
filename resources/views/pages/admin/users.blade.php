<div id="admin-users-page" class="py-8">
    <div class="max-w-7xl mx-auto px-4">

        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <flux:heading size="xl">{{ __('admin.users.title') }}</flux:heading>
                <flux:text class="mt-1">{{ trans_choice('admin.users.registered_count', $users->total(), ['count' => $users->total()]) }}</flux:text>
            </div>
            <div class="flex gap-2 items-center">
                <flux:select wire:model.live="approvalFilter" size="sm" class="w-44">
                    <option value="">{{ __('admin.users.filter_all') }}</option>
                    <option value="approved">{{ __('admin.users.filter_approved') }}</option>
                    <option value="pending">{{ __('admin.users.filter_pending') }}</option>
                </flux:select>
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

        <x:admin-nav />

        @if ($errors->any())
            <flux:callout icon="exclamation-triangle" variant="danger" class="mb-6">
                @foreach ($errors->all() as $index => $error)
                    <flux:text wire:key="error-{{ $index }}">{{ $error }}</flux:text>
                @endforeach
            </flux:callout>
        @endif

        <flux:card class="overflow-hidden">
            <flux:table :paginate="$users">
                <flux:table.columns>
                    <flux:table.column sortable :sorted="$sortBy === 'name'" :direction="$sortDirection" wire:click="sort('name')">{{ __('admin.users.table.user') }}</flux:table.column>
                    <flux:table.column sortable :sorted="$sortBy === 'soapnuts'" :direction="$sortDirection" wire:click="sort('soapnuts')" align="end">{{ __('admin.users.table.balance') }}</flux:table.column>
                    <flux:table.column sortable :sorted="$sortBy === 'is_approved'" :direction="$sortDirection" wire:click="sort('is_approved')" align="center">{{ __('admin.users.table.status') }}</flux:table.column>
                    <flux:table.column sortable :sorted="$sortBy === 'is_admin'" :direction="$sortDirection" wire:click="sort('is_admin')" align="center">{{ __('admin.users.table.admin') }}</flux:table.column>
                    <flux:table.column>{{ __('admin.users.table.organisation') }}</flux:table.column>
                    <flux:table.column sortable :sorted="$sortBy === 'user_bets_count'" :direction="$sortDirection" wire:click="sort('user_bets_count')" align="center">{{ __('admin.users.table.bets') }}</flux:table.column>
                    <flux:table.column sortable :sorted="$sortBy === 'created_at'" :direction="$sortDirection" wire:click="sort('created_at')">{{ __('admin.users.table.registered') }}</flux:table.column>
                    <flux:table.column align="end">{{ __('admin.users.table.actions') }}</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @foreach ($users as $user)
                        <flux:table.row wire:key="user-{{ $user->id }}">
                            <flux:table.cell>
                                <div class="font-medium text-zinc-900 dark:text-white">{{ $user->name }}</div>
                                <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $user->email }}</div>
                            </flux:table.cell>

                            <flux:table.cell variant="strong" class="whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2">
                                    <span>{{ number_format((float) $user->soapnuts, 0, ',', '.') }}</span>
                                    <flux:button
                                        id="user-balance-adjust-{{ $user->id }}"
                                        wire:click="openBalanceModal('{{ $user->id }}')"
                                        variant="ghost"
                                        size="xs"
                                        icon="currency-euro"
                                        class="shrink-0"
                                        title="{{ __('admin.balance_modal.title') }}"
                                    />
                                </div>
                            </flux:table.cell>

                            <flux:table.cell align="center">
                                @if ($user->isApproved())
                                    <flux:badge color="green" size="sm">{{ __('admin.users.status_approved') }}</flux:badge>
                                @else
                                    <flux:badge color="yellow" size="sm">{{ __('admin.users.status_pending') }}</flux:badge>
                                @endif
                            </flux:table.cell>

                            <flux:table.cell align="center">
                                <div class="flex items-center justify-center gap-1">
                                    <flux:badge color="{{ $user->is_admin ? 'purple' : 'gray' }}" size="sm">
                                        {{ $user->is_admin ? __('admin.users.admin_role') : __('admin.users.user_role') }}
                                    </flux:badge>
                                    <flux:button
                                        id="user-toggle-admin-{{ $user->id }}"
                                        wire:click="toggleAdmin('{{ $user->id }}')"
                                        variant="ghost"
                                        size="xs"
                                        icon="arrow-path"
                                        title="{{ $user->is_admin ? __('admin.users.revoke_admin') : __('admin.users.make_admin') }}"
                                    />
                                </div>
                            </flux:table.cell>

                            <flux:table.cell>
                                <div class="flex gap-1.5 items-center">
                                    <flux:select
                                        wire:change="assignOrganisation('{{ $user->id }}', $event.target.value)"
                                        size="sm"
                                    >
                                        <option value="">{{ __('admin.organisations.none') }}</option>
                                        @foreach ($organisations as $org)
                                            <option wire:key="org-{{ $org->id }}-user-{{ $user->id }}" value="{{ $org->id }}" {{ $user->organisation_id === $org->id ? 'selected' : '' }}>
                                                {{ $org->name }}
                                            </option>
                                        @endforeach
                                    </flux:select>
                                    @if (! $user->isApproved())
                                        <flux:button
                                            id="user-approve-{{ $user->id }}"
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

                            <flux:table.cell align="center" class="font-mono text-sm text-zinc-600 dark:text-zinc-400">
                                {{ $user->user_bets_count }}
                            </flux:table.cell>

                            <flux:table.cell class="text-sm text-zinc-500 dark:text-zinc-400 whitespace-nowrap">
                                {{ $user->created_at?->format('d.m.Y') }}
                            </flux:table.cell>

                            <flux:table.cell align="end" class="whitespace-nowrap">
                                <flux:button
                                    id="user-delete-{{ $user->id }}"
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
        </flux:card>

        <flux:modal wire:model="showBalanceModal" class="min-w-sm">
            <flux:heading size="lg" class="mb-4">{{ __('admin.balance_modal.title') }}</flux:heading>

            <form wire:submit="adjustBalance" class="space-y-4">
                <flux:input
                    id="balance-adjust-input"
                    wire:model="modalAdjustment"
                    type="number"
                    label="{{ __('admin.balance_modal.label') }}"
                    placeholder="{{ __('admin.balance_modal.placeholder') }}"
                    autofocus
                />

                <div class="flex gap-2 justify-end">
                    <flux:button wire:click="closeBalanceModal" variant="ghost">
                        {{ __('bets.cancel') }}
                    </flux:button>
                    <flux:button id="balance-adjust-submit" type="submit" variant="primary">
                        {{ __('admin.balance_modal.apply') }}
                    </flux:button>
                </div>
            </form>
        </flux:modal>

    </div>
</div>
