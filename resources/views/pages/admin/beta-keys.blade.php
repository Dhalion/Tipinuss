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
                            <option value="{{ $org->id }}">{{ $org->name }}</option>
                        @endforeach
                    </flux:select>

                    <flux:input wire:model="customKey" label="{{ __('admin.beta_keys.key_label') }}" placeholder="{{ __('admin.beta_keys.key_placeholder') }}"
                        hint="{{ __('admin.beta_keys.key_hint') }}" />

                    <flux:input wire:model="expiresAt" label="{{ __('admin.beta_keys.expires_label') }}" type="date"
                        hint="{{ __('admin.beta_keys.expires_hint') }}" />

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
            <flux:card>
                <div class="overflow-x-auto -mx-6">
                    <flux:table class="min-w-[600px] w-full">
                        <flux:table.columns>
                            <flux:table.column class="w-[200px]">{{ __('admin.beta_keys.table.key') }}</flux:table.column>
                            <flux:table.column class="w-[150px]">{{ __('admin.beta_keys.table.organisation') }}</flux:table.column>
                            <flux:table.column class="w-[100px]">{{ __('admin.beta_keys.table.status') }}</flux:table.column>
                            <flux:table.column class="w-[150px]">{{ __('admin.beta_keys.table.used_by') }}</flux:table.column>
                            <flux:table.column class="w-[100px]">{{ __('admin.beta_keys.table.expires') }}</flux:table.column>
                            <flux:table.column class="w-[120px]">{{ __('admin.beta_keys.table.created') }}</flux:table.column>
                            <flux:table.column class="text-right w-[60px]">{{ __('admin.beta_keys.table.actions') }}</flux:table.column>
                        </flux:table.columns>

                        <flux:table.rows>
                            @foreach ($keys as $key)
                                @php
                                    $status = $key->isValid() ? 'available' : (\is_null($key->used_at) ? 'inactive' : 'used');
                                @endphp
                                <flux:table.row>
                                    <flux:table.cell class="align-middle">
                                        <code class="font-mono text-sm font-semibold text-zinc-900 dark:text-white">
                                            {{ $key->key }}
                                        </code>
                                    </flux:table.cell>

                                    <flux:table.cell class="align-middle text-zinc-600 dark:text-zinc-400">
                                        {{ $key->organisation?->name ?? __('admin.beta_keys.none_org') }}
                                    </flux:table.cell>

                                    <flux:table.cell class="align-middle">
                                        @if ($status === 'available')
                                            <flux:badge color="green" size="sm">{{ __('admin.beta_keys.status_available') }}</flux:badge>
                                        @elseif ($status === 'used')
                                            <flux:badge color="red" size="sm">{{ __('admin.beta_keys.status_used') }}</flux:badge>
                                        @else
                                            <flux:badge color="yellow" size="sm">{{ __('admin.beta_keys.status_inactive') }}</flux:badge>
                                        @endif
                                    </flux:table.cell>

                                    <flux:table.cell class="align-middle text-sm text-zinc-500 dark:text-zinc-400">
                                        {{ $key->usedByUser?->name ?? '—' }}
                                    </flux:table.cell>

                                    <flux:table.cell class="align-middle text-sm text-zinc-500 dark:text-zinc-400">
                                        {{ $key->expires_at?->format('d.m.Y') ?? '—' }}
                                    </flux:table.cell>

                                    <flux:table.cell class="align-middle text-sm text-zinc-500 dark:text-zinc-400 whitespace-nowrap">
                                        {{ $key->created_at?->format('d.m.Y H:i') }}
                                    </flux:table.cell>

                                    <flux:table.cell class="text-right align-middle">
                                        @if ($key->isValid())
                                            <flux:button
                                                wire:click="deactivate('{{ $key->id }}')"
                                                wire:confirm="{{ __('admin.beta_keys.confirm_deactivate') }}"
                                                variant="danger"
                                                size="sm"
                                                icon="x-mark"
                                            />
                                        @endif
                                    </flux:table.cell>
                                </flux:table.row>
                            @endforeach
                        </flux:table.rows>
                    </flux:table>
                </div>
            </flux:card>
        @endif

    </div>
</div>
