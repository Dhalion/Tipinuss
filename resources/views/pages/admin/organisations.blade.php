<div class="min-h-screen bg-zinc-50 dark:bg-zinc-900 py-12">
    <div class="max-w-5xl mx-auto px-4">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-zinc-900 dark:text-white">Organisationen verwalten</h1>
            <p class="text-zinc-600 dark:text-zinc-400 mt-2">Gruppen erstellen und Nutzer zuweisen</p>
        </div>

        @if (session('status'))
            <div class="mb-4 p-4 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded-lg">
                {{ session('status') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <h2 class="text-lg font-semibold text-zinc-900 dark:text-white mb-4">Neue Organisation</h2>

                    <form wire:submit="createOrganisation">
                        <div class="space-y-4">
                            <div>
                                <label for="newOrganisationName" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Name</label>
                                <input
                                    id="newOrganisationName"
                                    type="text"
                                    wire:model="newOrganisationName"
                                    placeholder="z.B. Team Alpha"
                                    class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white placeholder-zinc-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-zinc-500 dark:focus:ring-zinc-400"
                                />
                                @error('newOrganisationName')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <button
                                type="submit"
                                class="w-full px-4 py-2 bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 rounded-lg font-semibold text-sm hover:opacity-90 transition"
                            >
                                Organisation erstellen
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="lg:col-span-2 space-y-4">
                @forelse ($organisations as $organisation)
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">{{ $organisation->name }}</h3>
                            <button
                                wire:click="deleteOrganisation('{{ $organisation->id }}')"
                                wire:confirm="Organisation '{{ $organisation->name }}' wirklich löschen? Nutzer werden keiner Gruppe mehr zugewiesen."
                                class="text-sm text-red-600 dark:text-red-400 hover:underline"
                            >
                                Löschen
                            </button>
                        </div>

                        <p class="text-sm text-zinc-500 dark:text-zinc-400 mb-4">
                            {{ $organisation->users->count() }} {{ $organisation->users->count() === 1 ? 'Mitglied' : 'Mitglieder' }}
                        </p>

                        @if ($organisation->users->isNotEmpty())
                            <div class="space-y-2 mb-4">
                                @foreach ($organisation->users as $member)
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-zinc-700 dark:text-zinc-300">{{ $member->name }}</span>
                                        <button
                                            wire:click="assignUserToOrganisation('{{ $member->id }}', '')"
                                            class="text-zinc-400 hover:text-red-500 transition text-xs"
                                        >
                                            Entfernen
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-8 text-center">
                        <p class="text-zinc-500 dark:text-zinc-400">Noch keine Organisationen vorhanden.</p>
                    </div>
                @endforelse
            </div>
        </div>

        @if ($allUsers->isNotEmpty())
            <div class="mt-8 bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-6">
                <h2 class="text-lg font-semibold text-zinc-900 dark:text-white mb-4">Nutzer zuweisen</h2>
                <div class="space-y-3">
                    @foreach ($allUsers as $user)
                        <div class="flex items-center justify-between py-2 border-b border-zinc-100 dark:border-zinc-700 last:border-0">
                            <div>
                                <span class="font-medium text-zinc-900 dark:text-white text-sm">{{ $user->name }}</span>
                                <span class="ml-2 text-xs text-zinc-400 dark:text-zinc-500">{{ $user->email }}</span>
                            </div>
                            <select
                                wire:change="assignUserToOrganisation('{{ $user->id }}', $event.target.value)"
                                class="text-sm border border-zinc-300 dark:border-zinc-600 rounded-lg px-3 py-1.5 bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-zinc-500"
                            >
                                <option value="">— Keine Gruppe —</option>
                                @foreach ($organisations as $organisation)
                                    <option
                                        value="{{ $organisation->id }}"
                                        {{ $user->organisation_id === $organisation->id ? 'selected' : '' }}
                                    >
                                        {{ $organisation->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
