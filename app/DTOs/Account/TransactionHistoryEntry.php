<?php

declare(strict_types=1);

namespace App\DTOs\Account;

final readonly class TransactionHistoryEntry
{
    public function __construct(
        public string $id,
        public string $type,
        public int $amount,
        public int $balanceAfter,
        public string $description,
        public ?string $badgeLabel = null,
        public ?string $badgeColor = null,
        public ?string $createdAt = null,
    ) {}
}
