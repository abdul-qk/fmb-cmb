<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Vendor extends BaseModel
{
  protected $guarded = [];

  public function city()
  {
    return $this->belongsTo(City::class, "city_id");
  }

  public function contactPerson(): HasOne
  {
      return $this->hasOne(VendorContactPerson::class)->where('primary', '1');
  }
  public function bank():HasOne
  {
    return $this->hasOne(VendorBank::class)->where('primary', '1');
  }

  public function contactPersons():HasMany
  {
    return $this->hasMany(VendorContactPerson::class);
  }
  public function banks():HasMany
  {
    return $this->hasMany(VendorBank::class);
  }
  
  public function createdBy(): BelongsTo
  {
    return $this->belongsTo(User::class,'created_by');
  }

  public function updatedBy(): BelongsTo
  {
    return $this->belongsTo(User::class, 'updated_by');
  }

  public function items(): BelongsToMany
  {
    return $this->belongsToMany(Item::class);
  }

  public function purchaseOrders():HasMany
  {
    return $this->hasMany(PurchaseOrder::class);
  }

  protected static function boot()
  {
    parent::boot();
    static::deleting(function ($vendor) {
      $vendor->banks()->each(function ($contactPerson) {
        $contactPerson->delete();
      });
      $vendor->contactPersons()->each(function ($contactPerson) {
        $contactPerson->delete();
      });
      $vendor->purchaseOrders()->each(function ($purchaseOrder) {
        $purchaseOrder->delete();
      });
      $vendor->items()->detach();
    });
  }
}
