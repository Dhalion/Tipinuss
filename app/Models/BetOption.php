<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $id
 * @property string $bet_id
 * @property string $title
 * @property float $odds
 * @property float|null $base_odds
 * @property bool|null $result
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Bet $bet
 */
final class BetOption extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'bet_id',
        'title',
        'odds',
        'base_odds',
        'result',
    ];

    protected $keyType = 'string';

    public $incrementing = false;

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
