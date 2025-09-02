<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChefRecipeItem extends BaseModel
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
  public function recipe(): HasOne
  {
    return $this->hasOne(Recipe::class, 'id' ,'recipe_id');
  }
  public function recipeItem(): BelongsTo
  {
    return $this->belongsTo(RecipeItem::class, 'recipe_item_id');
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
