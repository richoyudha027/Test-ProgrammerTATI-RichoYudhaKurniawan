<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Logbook extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'content',
        'image',
        'verified_by',
        'verification_status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }


    public function storeImage($image)
    {
        $imagePath = $image->store('logbooks', 'public');

        return $imagePath;
    }


}
