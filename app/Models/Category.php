<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $fillable = ['name','slug','status_id','image'];

    public function subcategories()
    {
        return $this->hasMany(SubCategory::class);
    }
}
