<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'name',
        'status',
        'user_id',
    ];

    public function promos(): HasMany
    {
        return $this->hasMany(Promo::class)->chaperone();;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
