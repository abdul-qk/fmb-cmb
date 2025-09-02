<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Country extends BaseModel
{
  protected $guarded = [];

  public function city(): HasOne
  {
  return $this->hasOne(City::class, 'country_id');
  }
  public function createdBy(): BelongsTo
  {
  return $this->belongsTo(User::class,'created_by');
  }
  public function updatedBy(): BelongsTo
  {
  return $this->belongsTo(User::class, 'updated_by');
  }
}
