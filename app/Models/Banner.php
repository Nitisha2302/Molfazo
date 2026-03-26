<?php


// app/Models/Banner.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
use HasFactory;

protected $fillable = ['title','image','status','cities','link_ids','link_type'];

protected $casts = [
 'cities' => 'array',
   'link_ids' => 'array',
];

}