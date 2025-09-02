<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryDetail extends BaseModel
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

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function unitMeasure(): BelongsTo
    {
        return $this->belongsTo(UnitMeasure::class);
    }
    public function unitMeasureSelect(): BelongsTo
    {
        return $this->belongsTo(UnitMeasure::class,"select_unit_measure_id");
    }

    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class,'received_by');
    }
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class,);
    }

    public function returns(): HasMany
    {
        return $this->hasMany(InventoryDetailReturn::class);
    }
    public function return(): HasOne
    {
        return $this->hasOne(InventoryDetailReturn::class);
    }

    public function inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class);
    }
}
