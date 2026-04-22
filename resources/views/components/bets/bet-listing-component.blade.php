<a href="{{ route('bets.detail', ['bet' => $bet]) }}" wire:navigate.hover class="block">
    <flux:card class="space-y-4 hover:shadow-md transition">
        <div class="flex items-start justify-between gap-4">
            <div class="flex-1 min-w-0">
                <h3 class="font-semibold text-lg text-zinc-900 dark:text-white truncate">
                    {{ $bet->title }}
                </h3>
                @if($bet->description)
                    <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-2 line-clamp-2">
                        {{ $bet->description }}
                    </p>
                @endif
            </div>
            <flux:badge :color="$bet->status->color()" size="sm" class="shrink-0 mt-0.5">
                {{ $bet->status->label() }}
            </flux:badge>
        </div>

        <div class="grid grid-cols-2 gap-2 sm:grid-cols-3 md:grid-cols-4">
            @foreach($bet->betOptions as $option)
                <div class="rounded-lg bg-zinc-100 dark:bg-zinc-700 px-3 py-2.5 text-center">
                    <div class="text-xs font-medium text-zinc-600 dark:text-zinc-300 truncate">{{ $option->title }}</div>
                    <div class="font-bold text-zinc-900 dark:text-white mt-1 text-sm">{{ number_format($option->odds, 2) }}x</div>
                </div>
            @endforeach
        </div>

        <flux:separator />

        <div class="flex items-center justify-between gap-2 text-xs text-zinc-500 dark:text-zinc-400">
            <span class="font-medium">{{ $bet->creator->name }}</span>
            @if($bet->expires_at)
                <span class="text-right">{{ $bet->expires_at->diffForHumans() }}</span>
            @endif
        </div>
    </flux:card>
</a>

