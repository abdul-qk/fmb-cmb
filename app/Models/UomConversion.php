<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UomConversion extends BaseModel
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
  public function baseUom(): BelongsTo
  {
  return $this->belongsTo(UnitMeasure::class, 'base_uom');
  }
  public function conversionUom(): BelongsTo
  {
  return $this->belongsTo(UnitMeasure::class, 'secondary_uom');
  }
}
