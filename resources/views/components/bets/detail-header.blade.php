@php /** @var \App\Models\Bet $bet */ @endphp

<flux:card class="space-y-4">
    <div class="flex items-start justify-between gap-4">
        <h1 class="text-xl font-bold text-zinc-900 dark:text-white leading-snug">
            {{ $bet->title }}
        </h1>
        <flux:badge :color="$bet->status->color()" size="sm" class="shrink-0">
            {{ $bet->status->label() }}
        </flux:badge>
    </div>

    @if($bet->description)
        <p class="text-sm text-zinc-500 dark:text-zinc-400">
            {{ $bet->description }}
        </p>
    @endif

    <flux:separator />

    <div class="flex flex-wrap gap-x-6 gap-y-1 text-xs text-zinc-400">
        <span>Erstellt von <span class="font-medium text-zinc-600 dark:text-zinc-300">{{ $bet->creator->name }}</span></span>
        <span>Am {{ $bet->created_at->format('d.m.Y') }}</span>
        @if($bet->expires_at)
            <span>Läuft ab {{ $bet->expires_at->diffForHumans() }}</span>
        @endif
    </div>
</flux:card>
