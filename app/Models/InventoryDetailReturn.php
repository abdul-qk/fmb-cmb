<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryDetailReturn extends BaseModel
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
    public function returnBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'return_by');
    }

    public function inventoryDetail(): BelongsTo
    {
        return $this->belongsTo(InventoryDetail::class);
    }
}
