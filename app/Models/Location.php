<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Location extends BaseModel
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
  public function country(): BelongsTo
  {
  return $this->belongsTo(Country::class, 'country_id');
  }
  public function city(): BelongsTo
  {
  return $this->belongsTo(City::class, 'city_id');
  }
}
