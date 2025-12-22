<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartiesOrder extends Model
{
    protected $hidden = [
        'updated_at',
    ];

    protected $guarded = ['status'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function getCreatedAtAttribute($value) {
        return \Carbon\Carbon::parse($value)->format('Y-m-d h:i');
    }
}
