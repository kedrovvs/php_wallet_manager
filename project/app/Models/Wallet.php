<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wallet extends Model
{
    protected $fillable = ['user_id', 'name', 'balance'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function holds(): HasMany
    {
        return $this->hasMany(Hold::class);
    }

    public function getAvailableBalanceAttribute(): int
    {
        $held = $this->holds()->where('status', 'active')->sum('amount');
        return $this->balance - $held;
    }

    public function getHeldBalanceAttribute(): int
    {
        return $this->holds()->where('status', 'active')->sum('amount');
    }
}
