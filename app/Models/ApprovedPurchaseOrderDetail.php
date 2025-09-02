<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ApprovedPurchaseOrderDetail extends BaseModel
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

    public function inventory(): HasOne
    {
      return $this->hasOne(Inventory::class, 'approved_purchase_order_detail_id');
    }
    public function inventories(): HasMany
    {
      return $this->hasMany(Inventory::class, 'approved_purchase_order_detail_id');
    }

    public function details(): HasMany
    {
      return $this->hasMany(PurchaseOrderDetail::class);
    }
}
