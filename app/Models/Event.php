<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Event extends BaseModel
{
  protected $guarded = [];

  public function servingItems(): HasMany
  {
    return $this->hasMany(ServingItem::class, 'event_id', 'id');
  }

  public function place(): BelongsTo
  {
    return $this->belongsTo(Place::class);
  }
  public function recipe(): BelongsTo
  {
    return $this->belongsTo(Recipe::class);
  }
  
  public function createdBy(): BelongsTo
  {
  return $this->belongsTo(User::class,'created_by');
  }

  public function updatedBy(): BelongsTo
  {
  return $this->belongsTo(User::class, 'updated_by');
  }

  public function menus(): HasMany
  {
    return $this->hasMany(Menu::class);
  }
  public function menu(): HasOne
  {
    return $this->hasOne(Menu::class);
  }

  public function purchaseOrder(): BelongsToMany
  {
    return $this->belongsToMany(PurchaseOrder::class);
  }

}
