<?php

namespace App\Models;

use App\Enums\BetStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * @property int $id
 * @property int $user_id
 * @property string $title
 * @property string|null $description
 * @property BetStatus $status
 * @property \Carbon\Carbon|null $expires_at
 * @property \Carbon\Carbon|null $closed_at
 * @property bool $dynamic_odds_enabled
 * @property \Carbon\Carbon|null $odds_last_updated_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read User $creator
 * @property-read \Illuminate\Database\Eloquent\Collection<int, BetOption> $betOptions
 * @property-read \Illuminate\Database\Eloquent\Collection<int, UserBet> $userBets
 */
final class Bet extends Model
{
    /** @use HasFactory<\Database\Factories\BetFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'description',
        'user_id',
        'status',
        'expires_at',
        'closed_at',
        'dynamic_odds_enabled',
        'odds_last_updated_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => BetStatus::class,
            'expires_at' => 'datetime',
            'closed_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'dynamic_odds_enabled' => 'boolean',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function betOptions(): HasMany
    {
        return $this->hasMany(BetOption::class);
    }

    public function userBets(): HasManyThrough
    {
        return $this->hasManyThrough(UserBet::class, BetOption::class);
    }
}
