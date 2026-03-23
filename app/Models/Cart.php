<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = ['user_id','product_id','quantity','combination_id'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function combination()
    {
        return $this->belongsTo(ProductCombination::class, 'combination_id');
    }
}
