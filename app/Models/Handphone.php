<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Handphone extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', // iphone 15 promax
        'slug', // domain.com/iphone-15-promax
        'thumbnail',
        'about',
        'price',
        'stock',
        'is_popular',
        'category_id', // fk, foreign key
        'brand_id',
    ];

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value; // iphone 15 promax
        $this->attributes['slug'] = Str::slug($value); // iphone-15-promax
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(HandphonePhoto::class);
    }

    public function capacities(): HasMany
    {
        return $this->hasMany(HandphoneCapacity::class);
    }
}
