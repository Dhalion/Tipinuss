<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property string $user_id
 * @property TransactionType $type
 * @property int $amount
 * @property int $balance_after
 * @property string|null $user_bet_id
 * @property string|null $description
 */
final class BalanceTransaction extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'type',
        'amount',
        'balance_after',
        'user_bet_id',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'type' => TransactionType::class,
            'amount' => 'integer',
            'balance_after' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function userBet(): BelongsTo
    {
        return $this->belongsTo(UserBet::class);
    }
}
