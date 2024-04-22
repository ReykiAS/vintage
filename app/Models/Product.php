<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Searchable;

use App\Traits\HasImageTrait;
class Product extends Model
{
    use HasFactory, SoftDeletes, HasImageTrait,Searchable;

    protected $fillable = [
        'name',
        'price',
        'qty',
        'weight',
        'description',
        'discount',
        'brand_id',
        'category_id',
        'user_id',
    ];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function favorite()
    {
        return $this->hasMany(Favorite::class);
    }

    public function cart()
    {
        return $this->hasMany(Cart::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }
    public function image()
    {
        return $this->morphOne(Image::class, 'imageable');
    }
    public function getDiscountedPriceAttribute()
    {
        return $this->price - ($this->discount / 100 * $this->price);
    }
    public function variants()
    {
        return $this->hasMany(Variant::class);
    }

    // Accessor method to calculate and return the average rating
    public function getAverageRatingAttribute()
    {
        // Check if ratings exist to avoid division by zero
        if ($this->ratings->count() > 0) {
            return $this->ratings->avg('rating');
        } else {
            return null; // Or any placeholder value you prefer for products without ratings
        }
    }
}
