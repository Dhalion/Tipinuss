<div class="min-h-screen bg-zinc-50 dark:bg-zinc-900 py-12">
    <div class="max-w-7xl mx-auto px-4">
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-zinc-900 dark:text-white">Nutzerverwaltung</h1>
                <p class="text-zinc-600 dark:text-zinc-400 mt-1">{{ $users->count() }} Nutzer</p>
            </div>
            <a href="{{ route('admin.organisations') }}" wire:navigate class="text-sm text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200">
                → Organisationen verwalten
            </a>
        </div>

        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 rounded-lg">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-zinc-50 dark:bg-zinc-700 border-b border-zinc-200 dark:border-zinc-600">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-zinc-700 dark:text-zinc-200">Nutzer</th>
                        <th class="px-4 py-3 text-right font-semibold text-zinc-700 dark:text-zinc-200">Soapnuts 🌰</th>
                        <th class="px-4 py-3 text-center font-semibold text-zinc-700 dark:text-zinc-200">Admin</th>
                        <th class="px-4 py-3 text-left font-semibold text-zinc-700 dark:text-zinc-200">Organisation</th>
                        <th class="px-4 py-3 text-right font-semibold text-zinc-700 dark:text-zinc-200">Wetten</th>
                        <th class="px-4 py-3 text-left font-semibold text-zinc-700 dark:text-zinc-200">Registriert</th>
                        <th class="px-4 py-3 text-right font-semibold text-zinc-700 dark:text-zinc-200">Aktionen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700">
                    @foreach ($users as $user)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors">
                            <td class="px-4 py-3">
                                <div class="font-medium text-zinc-900 dark:text-white">{{ $user->name }}</div>
                                <div class="text-xs text-zinc-400 dark:text-zinc-500">{{ $user->email }}</div>
                            </td>

                            <td class="px-4 py-3 text-right">
                                <div class="font-mono font-semibold text-zinc-900 dark:text-white">
                                    {{ number_format((float) $user->soapnuts, 0, ',', '.') }}
                                </div>
                                <form wire:submit="adjustBalance('{{ $user->id }}')" class="mt-1 flex gap-1 justify-end">
                                    <input
                                        type="number"
                                        wire:model="balanceAdjustments.{{ $user->id }}"
                                        placeholder="±0"
                                        class="w-20 text-xs text-center border border-zinc-300 dark:border-zinc-600 rounded px-1 py-0.5 bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white"
                                    />
                                    <button type="submit" class="text-xs px-2 py-0.5 bg-zinc-800 dark:bg-zinc-200 text-white dark:text-zinc-900 rounded hover:opacity-80 transition">
                                        OK
                                    </button>
                                </form>
                            </td>

                            <td class="px-4 py-3 text-center">
                                <button
                                    wire:click="toggleAdmin('{{ $user->id }}')"
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-full text-sm transition
                                        {{ $user->is_admin
                                            ? 'bg-amber-100 dark:bg-amber-900 text-amber-700 dark:text-amber-200 hover:bg-amber-200 dark:hover:bg-amber-800'
                                            : 'bg-zinc-100 dark:bg-zinc-700 text-zinc-400 dark:text-zinc-500 hover:bg-zinc-200 dark:hover:bg-zinc-600'
                                        }}"
                                    title="{{ $user->is_admin ? 'Admin entziehen' : 'Zum Admin machen' }}"
                                >
                                    {{ $user->is_admin ? '★' : '☆' }}
                                </button>
                            </td>

                            <td class="px-4 py-3">
                                <select
                                    wire:change="assignOrganisation('{{ $user->id }}', $event.target.value)"
                                    class="text-sm border border-zinc-300 dark:border-zinc-600 rounded-lg px-2 py-1 bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-zinc-500 max-w-[160px]"
                                >
                                    <option value="">— Keine —</option>
                                    @foreach ($organisations as $org)
                                        <option value="{{ $org->id }}" {{ $user->organisation_id === $org->id ? 'selected' : '' }}>
                                            {{ $org->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>

                            <td class="px-4 py-3 text-right font-mono text-zinc-600 dark:text-zinc-300">
                                {{ $user->user_bets_count }}
                            </td>

                            <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400 text-xs">
                                {{ $user->created_at?->format('d.m.Y') }}
                            </td>

                            <td class="px-4 py-3 text-right">
                                <button
                                    wire:click="deleteUser('{{ $user->id }}')"
                                    wire:confirm="Nutzer '{{ $user->name }}' wirklich löschen? Alle Wetten und Daten werden gelöscht."
                                    class="text-xs text-red-500 dark:text-red-400 hover:underline"
                                >
                                    Löschen
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
