<a href="{{ route('bet.detail', ['bet' => $bet]) }}" wire:navigate.hover>
    <flux:card class="space-y-3">
        <div class="flex items-start justify-between gap-3">
            <div class="flex-1">
                <h3 class="font-semibold text-zinc-900 dark:text-white truncate">
                    {{ $bet->title }}
                </h3>
                @if($bet->description)
                    <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1 line-clamp-2">
                        {{ $bet->description }}
                    </p>
                @endif
            </div>
            <flux:badge :color="$bet->status->color()" size="sm" class="shrink-0">
                {{ $bet->status->label() }}
            </flux:badge>
        </div>

        <div class="grid grid-cols-2 gap-2 sm:grid-cols-3">
            @foreach($bet->betOptions as $option)
                <div class="rounded-lg bg-zinc-50 dark:bg-zinc-800 px-3 py-2 text-center text-sm">
                    <div class="text-xs text-zinc-500 dark:text-zinc-400 truncate">{{ $option->title }}</div>
                    <div class="font-semibold text-zinc-900 dark:text-white mt-0.5">{{ number_format($option->odds, 2) }}x</div>
                </div>
            @endforeach
        </div>

        <flux:separator />

        <div class="flex items-center justify-between text-xs text-zinc-400">
            <span>{{ $bet->creator->name }}</span>
            @if($bet->expires_at)
                <span>{{ $bet->expires_at->diffForHumans() }}</span>
            @endif
        </div>
    </flux:card>
</a>

