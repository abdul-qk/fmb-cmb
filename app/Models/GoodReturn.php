<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GoodReturn extends BaseModel
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

  public function returnBy(): BelongsTo
  {
    return $this->belongsTo(User::class,'return_by');
  }

  public function inventoryDetailReturns(): HasMany
  {
    return $this->hasMany(InventoryDetailReturn::class);
  }

  public function vendor(): BelongsTo
  {
    return $this->belongsTo(Vendor::class);
  }

  public function supplierReturn(): HasMany
  {
    return $this->hasMany(SupplierReturn::class);
  }
}
