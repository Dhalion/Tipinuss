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
 * @property int $amount_wagered
 * @property int $potential_winnings
 * @property string|null $description
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
            'amount_wagered' => 'integer',
            'potential_winnings' => 'integer',
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
