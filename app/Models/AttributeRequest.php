<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttributeRequest extends Model
{

    protected $fillable = [

        'vendor_id',
        'child_category_id',
        'attribute_name',
        'attribute_value',
        'status'

    ];

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function childCategory()
    {
        return $this->belongsTo(ChildCategory::class, 'child_category_id');
    }

}