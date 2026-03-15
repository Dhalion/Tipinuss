<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $bet_id
 * @property string $title
 * @property float $odds
 * @property float|null $base_odds
 * @property bool|null $result
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read Bet $bet
 * @property-read \Illuminate\Database\Eloquent\Collection<int, UserBet> $userBets
 */
final class BetOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'bet_id',
        'title',
        'odds',
        'base_odds',
        'result',
    ];

    protected function casts(): array
    {
        return [
            'odds' => 'decimal:2',
            'base_odds' => 'decimal:2',
            'result' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function bet(): BelongsTo
    {
        return $this->belongsTo(Bet::class);
    }

    public function userBets(): HasMany
    {
        return $this->hasMany(UserBet::class);
    }
}
