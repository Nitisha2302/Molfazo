<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryAttribute extends Model
{
    protected $fillable = [
        'child_category_id',
        'attributes_json'
    ];

    protected $casts = [
        'attributes_json' => 'array',
    ];

    public function childCategory()
    {
        return $this->belongsTo(ChildCategory::class);
    }
}
