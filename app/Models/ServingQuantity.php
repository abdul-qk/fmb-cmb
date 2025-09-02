<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServingQuantity extends BaseModel
{
  protected $guarded = [];

  public function createdBy(): BelongsTo
  {
    return $this->belongsTo(User::class, 'created_by');
  }
  public function updatedBy(): BelongsTo
  {
    return $this->belongsTo(User::class, 'updated_by');
  }
  public function servingQuantityItems(): HasMany
  {
    return $this->hasMany(ServingQuantityTiffin::class, 'serving_quantity_id');
  }

  protected static function boot()
  {
    parent::boot();
    static::deleting(function ($item) {
      $item->servingQuantityItems()->each(function ($quantityItem) {
        $quantityItem->delete();
      });
    });
  }
}
