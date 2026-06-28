<div class="py-8">
    <div class="max-w-7xl mx-auto px-4">

        <div class="mb-6">
            <flux:breadcrumbs>
                <flux:breadcrumbs.item href="{{ route('admin.organisations') }}" wire:navigate>
                    {{ __('admin.organisations.title') }}
                </flux:breadcrumbs.item>
                <flux:breadcrumbs.item>{{ $organisation->name }}</flux:breadcrumbs.item>
            </flux:breadcrumbs>
        </div>

        <div class="flex items-start justify-between mb-6">
            <div>
                <flux:heading size="xl">{{ $organisation->name }}</flux:heading>
                <flux:text class="mt-1">
                    {{ trans_choice('admin.organisations.member_count', $organisation->users->count(), ['count' => $organisation->users->count()]) }}
                </flux:text>
            </div>
            <flux:button
                wire:click="deleteOrganisation"
                wire:confirm="{{ __('admin.organisations.confirm_delete') }}"
                variant="danger"
                icon="trash"
            >
                {{ __('admin.organisations.delete') }}
            </flux:button>
        </div>

        @if ($organisation->users->isNotEmpty())
            <flux:card class="overflow-hidden mb-6">
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>{{ __('admin.users.table.user') }}</flux:table.column>
                        <flux:table.column>{{ __('admin.users.table.registered') }}</flux:table.column>
                        <flux:table.column align="end">{{ __('admin.users.table.actions') }}</flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @foreach ($organisation->users as $member)
                            <flux:table.row wire:key="member-{{ $member->id }}">
                                <flux:table.cell>
                                    <div class="font-medium text-zinc-900 dark:text-white">{{ $member->name }}</div>
                                    <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $member->email }}</div>
                                </flux:table.cell>
                                <flux:table.cell class="text-sm text-zinc-500 dark:text-zinc-400">
                                    {{ $member->created_at?->format('d.m.Y') }}
                                </flux:table.cell>
                                <flux:table.cell class="text-right">
                                    <flux:button
                                        wire:click="removeMember('{{ $member->id }}')"
                                        wire:confirm="{{ __('admin.organisations.confirm_remove_member', ['name' => $member->name]) }}"
                                        variant="ghost"
                                        size="sm"
                                        icon="user-minus"
                                    >
                                        {{ __('admin.organisations.remove') }}
                                    </flux:button>
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            </flux:card>
        @else
            <flux:card class="p-8 text-center mb-6">
                <flux:heading class="mb-2">{{ __('admin.organisations.no_members') }}</flux:heading>
            </flux:card>
        @endif

        @if ($unassignedUsers->isNotEmpty())
            <flux:card class="p-6">
                <flux:heading size="lg" class="mb-4">{{ __('admin.organisations.add_member') }}</flux:heading>
                <form wire:submit="addMember" class="flex gap-3 items-end">
                    <div class="flex-1">
                        <flux:select wire:model="selectedUserId" class="w-full">
                            <option value="">{{ __('admin.organisations.select_user') }}</option>
                            @foreach ($unassignedUsers as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </flux:select>
                    </div>
                    <flux:button type="submit" variant="primary" icon="user-plus">
                        {{ __('admin.organisations.add_member') }}
                    </flux:button>
                </form>
            </flux:card>
        @else
            <flux:card class="p-6 text-center">
                <flux:text>{{ __('admin.organisations.no_users_to_assign') }}</flux:text>
            </flux:card>
        @endif

    </div>
</div>
