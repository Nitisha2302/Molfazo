<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id','category_id','sub_category_id','child_category_id','name','description',
        'price','discount_price','available_quantity','delivery_available',
        'delivery_price','delivery_time','characteristics','tags','status_id','attributes_json'
    ];

    protected $casts = [
        'characteristics' => 'array',
        'tags' => 'array',
        'attributes_json' => 'array'
    ];

    public function store() { return $this->belongsTo(Store::class); }
    public function category() { return $this->belongsTo(Category::class); }
    public function subCategory() { return $this->belongsTo(SubCategory::class,'sub_category_id'); }
    public function childCategory()
    {
        return $this->belongsTo(ChildCategory::class);
    }


    // Relation for multiple images
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    
}
