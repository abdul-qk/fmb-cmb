<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MenuServing extends BaseModel
{
  protected $guarded = [];
  
  public function menuServings(): BelongsTo
  {
    return $this->BelongsTo(EventServing::class, 'menu_id');
  }
  
  public function recipeItem(): BelongsTo
  {
    return $this->belongsTo(RecipeItem::class);
  }
}
