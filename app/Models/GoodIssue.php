<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GoodIssue extends BaseModel
{
    protected $guarded = [];

    public function createdBy(): BelongsTo
    {
      return $this->belongsTo(User::class,'created_by');
    }

    public function updatedBy(): BelongsTo
    {
      return $this->belongsTo(User::class, 'updated_by');
    }

    public function event(): BelongsTo
    {
      return $this->belongsTo(Event::class);
    }

    public function kitchen(): BelongsTo
    {
      return $this->belongsTo(Kitchen::class);
    }

    public function inventoryDetail(): HasOne
    {
      return $this->hasOne(InventoryDetail::class);
    }
    public function inventoryDetails(): HasMany
    {
      return $this->hasMany(InventoryDetail::class);
    }
}
