<div class="py-8">
    <div class="max-w-7xl mx-auto px-4">

        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <flux:heading size="xl">{{ __('admin.beta_keys.title') }}</flux:heading>
                <flux:text class="mt-1">{{ trans_choice('admin.beta_keys.count', $keys->count(), ['count' => $keys->count()]) }}</flux:text>
            </div>
            <flux:button wire:click="$toggle('showCreateForm')" variant="primary" size="sm" icon="plus">
                {{ __('admin.beta_keys.create_button') }}
            </flux:button>
        </div>

        @if ($showCreateForm)
            <flux:card class="mb-6 p-6">
                <flux:heading size="lg" class="mb-4">{{ __('admin.beta_keys.create_title') }}</flux:heading>

                <form wire:submit="create" class="space-y-4">
                    <flux:select wire:model="organisationId" label="{{ __('admin.beta_keys.organisation_label') }}" required>
                        <option value="">{{ __('admin.beta_keys.organisation_placeholder') }}</option>
                        @foreach ($organisations as $org)
                            <option wire:key="org-{{ $org->id }}" value="{{ $org->id }}">{{ $org->name }}</option>
                        @endforeach
                    </flux:select>

                    <flux:input wire:model="customKey" label="{{ __('admin.beta_keys.key_label') }}" placeholder="{{ __('admin.beta_keys.key_placeholder') }}"
                        hint="{{ __('admin.beta_keys.key_hint') }}" />

                    <flux:input wire:model="expiresAt" label="{{ __('admin.beta_keys.expires_label') }}" type="date"
                        hint="{{ __('admin.beta_keys.expires_hint') }}" />

                    <flux:input wire:model="startBalance" label="{{ __('admin.beta_keys.start_balance_label') }}" type="number" min="0"
                        hint="{{ __('admin.beta_keys.start_balance_hint') }}" />

                    <flux:textarea wire:model="message" label="{{ __('admin.beta_keys.message_label') }}" rows="2" maxlength="{{ \App\Constants\AppDefaults::BETA_KEY_MESSAGE_MAX_LENGTH }}"
                        hint="{{ __('admin.beta_keys.message_hint') }}" />

                    <div class="flex gap-2 justify-end pt-2">
                        <flux:button wire:click="$set('showCreateForm', false)" variant="ghost">
                            {{ __('bets.cancel') }}
                        </flux:button>
                        <flux:button type="submit" variant="primary">
                            {{ __('admin.beta_keys.create_submit') }}
                        </flux:button>
                    </div>
                </form>
            </flux:card>
        @endif

        @if ($keys->isEmpty())
            <flux:card class="p-12 text-center">
                <flux:heading class="mb-2">{{ __('admin.beta_keys.empty_title') }}</flux:heading>
                <flux:text>{{ __('admin.beta_keys.empty_description') }}</flux:text>
            </flux:card>
        @else
            <flux:card class="overflow-hidden">
                <flux:table :paginate="$keys" class="min-w-[600px]">
                    <flux:table.columns>
                        <flux:table.column sortable :sorted="$sortBy === 'key'" :direction="$sortDirection" wire:click="sort('key')" class="w-[200px]">{{ __('admin.beta_keys.table.key') }}</flux:table.column>
                        <flux:table.column class="w-[150px]">{{ __('admin.beta_keys.table.organisation') }}</flux:table.column>
                        <flux:table.column sortable :sorted="$sortBy === 'is_active'" :direction="$sortDirection" wire:click="sort('is_active')" class="w-[100px]">{{ __('admin.beta_keys.table.status') }}</flux:table.column>
                        <flux:table.column class="w-[150px]">{{ __('admin.beta_keys.table.used_by') }}</flux:table.column>
                        <flux:table.column sortable :sorted="$sortBy === 'expires_at'" :direction="$sortDirection" wire:click="sort('expires_at')" class="w-[100px]">{{ __('admin.beta_keys.table.expires') }}</flux:table.column>
                        <flux:table.column sortable :sorted="$sortBy === 'start_balance'" :direction="$sortDirection" wire:click="sort('start_balance')" class="w-[100px]">{{ __('admin.beta_keys.table.start_balance') }}</flux:table.column>
                        <flux:table.column class="w-[160px]">{{ __('admin.beta_keys.table.message') }}</flux:table.column>
                        <flux:table.column sortable :sorted="$sortBy === 'created_at'" :direction="$sortDirection" wire:click="sort('created_at')" class="w-[120px]">{{ __('admin.beta_keys.table.created') }}</flux:table.column>
                        <flux:table.column class="text-right w-[60px]">{{ __('admin.beta_keys.table.actions') }}</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @foreach ($keys as $key)
                            <flux:table.row wire:key="key-{{ $key->id }}">
                                <flux:table.cell>
                                    <code class="font-mono text-sm font-semibold text-zinc-900 dark:text-white">
                                        {{ $key->key }}
                                    </code>
                                </flux:table.cell>

                                <flux:table.cell class="text-zinc-600 dark:text-zinc-400">
                                    {{ $key->organisation?->name ?? __('admin.beta_keys.none_org') }}
                                </flux:table.cell>

                                <flux:table.cell>
                                    <flux:badge :color="$key->status()->badgeColor()" size="sm">
                                        {{ __("admin.beta_keys.status_{$key->status()->value}") }}
                                    </flux:badge>
                                </flux:table.cell>

                                <flux:table.cell class="text-sm text-zinc-500 dark:text-zinc-400">
                                    {{ $key->usedByUser?->name ?? '—' }}
                                </flux:table.cell>

                                <flux:table.cell class="text-sm text-zinc-500 dark:text-zinc-400">
                                    {{ $key->expires_at?->format('d.m.Y') ?? '—' }}
                                </flux:table.cell>

                                <flux:table.cell class="text-sm font-semibold text-zinc-900 dark:text-white">
                                    {{ $key->start_balance !== null ? number_format($key->start_balance, 0) . ' 🌰' : '—' }}
                                </flux:table.cell>

                                <flux:table.cell class="text-sm text-zinc-500 dark:text-zinc-400 max-w-[160px] truncate" title="{{ $key->message }}">
                                    {{ $key->message ?? '—' }}
                                </flux:table.cell>

                                <flux:table.cell class="text-sm text-zinc-500 dark:text-zinc-400 whitespace-nowrap">
                                    {{ $key->created_at?->format('d.m.Y H:i') }}
                                </flux:table.cell>

                                <flux:table.cell class="text-right">
                                    @if ($key->isValid())
                                        <div class="flex items-center justify-end gap-1">
                                            <flux:button
                                                x-data="{ copied: false }"
                                                x-on:click="
                                                    navigator.clipboard.writeText('{{ route('register', ['key' => $key->key]) }}');
                                                    copied = true;
                                                    setTimeout(() => copied = false, 2000);
                                                "
                                                variant="ghost"
                                                size="sm"
                                                class="shrink-0"
                                            >
                                                <span x-show="!copied">{{ __('admin.beta_keys.copy_link') }}</span>
                                                <span x-show="copied" x-cloak>{{ __('admin.beta_keys.link_copied') }}</span>
                                            </flux:button>
                                            <flux:button
                                                wire:click="deactivate('{{ $key->id }}')"
                                                wire:confirm="{{ __('admin.beta_keys.confirm_deactivate') }}"
                                                variant="danger"
                                                size="sm"
                                                icon="x-mark"
                                            />
                                        </div>
                                    @endif
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            </flux:card>
        @endif

    </div>
</div>
