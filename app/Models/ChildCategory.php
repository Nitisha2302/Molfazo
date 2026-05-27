<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChildCategory extends Model
{
    protected $fillable = [
        'sub_category_id',
        'name',
        'slug',
        'status_id',
        'image',
    ];

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }

    // ChildCategory.php

    // SubCategory.php
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // App\Models\ChildCategory.php

    public function attributeTemplate()
    {
        return $this->hasOne(CategoryAttribute::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'child_category_id');
    }


    

}
