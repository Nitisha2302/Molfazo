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

}
