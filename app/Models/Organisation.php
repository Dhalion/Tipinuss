<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $id
 * @property string $name
 */
final class Organisation extends Model
{
    use HasUuids;

    protected $fillable = ['name'];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function bets(): HasMany
    {
        return $this->hasMany(Bet::class);
    }
}
