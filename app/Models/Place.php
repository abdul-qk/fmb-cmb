<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Place extends BaseModel
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

  public function location(): BelongsTo
  {
    return $this->belongsTo(Location::class, 'location_id');
  }

  public function country()
  {
    return $this->hasOneThrough(Country::class, Location::class, 'id', 'id', 'location_id', 'country_id');
  }

  public function city()
  {
    return $this->hasOneThrough(City::class, Location::class, 'id', 'id', 'location_id', 'city_id');
  }
}
