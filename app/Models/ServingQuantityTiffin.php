<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ServingQuantityTiffin extends BaseModel
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

  public function servingQuantityTiffinItems(): BelongsTo
  {
  return $this->belongsTo(TiffinSize::class, 'tiffin_size_id');
  }
}
