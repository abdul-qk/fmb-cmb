<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ItemBaseUom extends BaseModel
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

  public function item(): BelongsTo
  {
    return $this->belongsTo(Item::class);
  }

  public function baseUom(): BelongsTo
  {
    return $this->BelongsTo(UnitMeasure::class, 'unit_measure_id');
  }
  public function unitMeasure(): BelongsToMany
  {
    return $this->belongsToMany(UnitMeasure::class, 'item_base_uom_unit_measure', 'item_base_uom_id', 'secondary_uom');
  }

  protected static function boot()
  {
    parent::boot();
    static::deleting(function ($result) {
      $result->unitMeasure()->detach();
    });
  }
}
