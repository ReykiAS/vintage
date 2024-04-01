<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Traits\HasImageTrait;
class Product extends Model
{
    use HasFactory, SoftDeletes, HasImageTrait;

    protected $fillable = [
        'name',
        'price',
        'qty',
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
        return $this->belongsTo(Favorite::class);
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
}
