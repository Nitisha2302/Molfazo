<?php



namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'reported_user_id',
        'description',
    ];

    // User who reported
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // User being reported
    public function reportedUser()
    {
        return $this->belongsTo(User::class, 'reported_user_id');
    }
}