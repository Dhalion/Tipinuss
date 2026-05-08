<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\BetStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * @property string $id
 * @property string $user_id
 * @property string $title
 * @property string|null $description
 * @property BetStatus $status
 * @property Carbon|null $expires_at
 * @property Carbon|null $closed_at
 * @property bool $dynamic_odds_enabled
 * @property Carbon|null $odds_last_updated_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
final class Bet extends Model
{
    use HasFactory, HasUuids;

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

    protected $keyType = 'string';

    public $incrementing = false;

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

    public function isOpen(): bool
    {
        return $this->status === BetStatus::Open;
    }

    public function isClosed(): bool
    {
        return $this->status === BetStatus::Closed;
    }
}
