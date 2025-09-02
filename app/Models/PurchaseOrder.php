<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrder extends BaseModel
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

    public function currency(): BelongsTo
    {
      return $this->belongsTo(Currency::class);
    }
    public function vendor(): BelongsTo
    {
      return $this->belongsTo(Vendor::class);
    }

    public function detail(): HasMany
    {
        return $this->hasMany(PurchaseOrderDetail::class);
    }

    public function events(): BelongsToMany
    {
      return $this->belongsToMany(Event::class);
    }

    public function place(): BelongsTo
    {
      return $this->belongsTo(Place::class);
    }
    public function store(): BelongsTo
    {
      return $this->belongsTo(Store::class);
    }
    public function menu(): BelongsTo
    {
      return $this->belongsTo(Menu::class);
    }

    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($purchaseOrder) {
            $purchaseOrder->detail()->each(function ($detail) {
              $detail->delete();
            });
            $purchaseOrder->events()->detach();
        });
    }
}
