<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TiffinSize extends BaseModel
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
}
