<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecipeItem extends BaseModel
{
  protected $guarded = [];
  public function measurement(): BelongsTo
  {
    return $this->belongsTo(UnitMeasure::class, 'measurement_id');
  }
  public function item(): BelongsTo
  {
    return $this->belongsTo(Item::class, 'item_id');
  }
}
