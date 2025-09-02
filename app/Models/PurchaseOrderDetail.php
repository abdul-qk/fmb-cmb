<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PurchaseOrderDetail extends BaseModel
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

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function unitMeasure(): BelongsTo
    {
        return $this->belongsTo(UnitMeasure::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function approvedDetail(): HasOne
    {
        return $this->hasOne(ApprovedPurchaseOrderDetail::class);
    }

    public function selectedUnitMeasure(): BelongsTo
    {
        return $this->belongsTo(UnitMeasure::class, 'select_unit_measure_id');
    }

    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($purchaseOrder) {
            $purchaseOrder->approvedDetail()->each(function ($approvedDetail) {
                $approvedDetail->delete();
            });
        });
    }
}
