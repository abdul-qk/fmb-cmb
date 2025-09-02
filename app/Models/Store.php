<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Store extends BaseModel
{
  protected $guarded = [];
  protected $appends = ['floor_name'];

  public function place(): BelongsTo
  {
    return $this->belongsTo(Place::class, 'place_id');
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
    // return Attribute::make(
    //   get: fn () => ($this->floor == 0) ? $placeName . ' - Ground' : $placeName . ' - Floor ' . $this->floor,
    // );
    return Attribute::make(
      // get: fn () => $placeName .' - '. $this->floor,
      get: fn () => $this->floor,
    );
  }
}
