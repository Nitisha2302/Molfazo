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

}