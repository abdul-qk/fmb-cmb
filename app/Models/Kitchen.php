<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Kitchen extends BaseModel
{
  protected $guarded = [];
  protected $appends = [
    'floor_name',
  ];

  public function place(): BelongsTo
  {
    return $this->belongsTo(Place::class, 'place_id');
  }


  public function location()
  {
    return $this->hasOneThrough(
      Location::class,
      Place::class,
      'id', // Foreign key on Place
      'id', // Foreign key on Location
      'place_id', // Local key on current model
      'location_id' // Local key on Place
    );
  }

  public function country()
  {
    return $this->hasOneThrough(
      Country::class,
      Location::class,
      'id', // Foreign key on Location
      'id', // Foreign key on Country
      'id', // Local key on current model
      'country_id' // Local key on Location
    );
  }

  public function city()
  {
    return $this->hasOneThrough(
      City::class,
      Location::class,
      'id', // Foreign key on Location
      'id', // Foreign key on City
      'id', // Local key on current model
      'city_id' // Local key on Location
    );
  }
  public function createdBy(): BelongsTo
  {
    return $this->belongsTo(User::class, 'created_by');
  }
  public function updatedBy(): BelongsTo
  {
    return $this->belongsTo(User::class, 'updated_by');
  }
  protected function floorName(): Attribute
  {
    $placeName = $this->place()->pluck('name')->first();
    return Attribute::make(
      get: fn () => ($this->floor == 0) ? $placeName . ' - Ground' : $placeName . ' - Floor ' . $this->floor,
    );
  }
}
