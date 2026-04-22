<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\UserBetStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property string $user_id
 * @property string $bet_option_id
 * @property float $amount_wagered
 * @property float $potential_winnings
 * @property UserBetStatus $status
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
final class UserBet extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'bet_option_id',
        'amount_wagered',
        'potential_winnings',
        'status',
    ];

    protected $keyType = 'string';
    public $incrementing = false;

    protected function casts(): array
    {
        return [
            'amount_wagered' => 'decimal:2',
            'potential_winnings' => 'decimal:2',
            'status' => UserBetStatus::class,
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function betOption(): BelongsTo
    {
        return $this->belongsTo(BetOption::class);
    }
}

