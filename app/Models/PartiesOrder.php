<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartiesOrder extends Model
{
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $guarded = ['status'];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
