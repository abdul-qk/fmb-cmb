<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserMenuSelection extends Model
{
    protected $guarded = [];

    /**
     * Get the event this selection is for
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the dish this selection is for
     */
    public function dish(): BelongsTo
    {
        return $this->belongsTo(Dish::class);
    }
}

