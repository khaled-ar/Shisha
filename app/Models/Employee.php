<?php

namespace App\Models;

use App\Traits\Files;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $guarded = [];
    protected $hidden = [
        'created_at',
        'updated_at',
        'front_id_image',
        'back_id_image'
    ];
    protected $appends = [
        'back_id_image_url',
        'front_id_image_url',
    ];

    public function user() {
        return $this->belongsTo(User::class)->select([
            'id', 'image', 'name', 'phone'
        ]);
    }

    public function getFrontIdImageUrlAttribute()
    {
        return $this->front_id_image ? asset("Images/Users") . '/' . $this->front_id_image : null;
    }

    public function getBackIdImageUrlAttribute()
    {
        return $this->back_id_image ? asset("Images/Users") . '/' . $this->back_id_image : null;
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function($employee) {
            $employee->front_id_image = Files::moveFile(request('front_id_image'), "Images/Users");
            $employee->back_id_image = Files::moveFile(request('back_id_image'), "Images/Users");
            $employee->save();
        });

        static::deleting(function($employee) {
            Files::deleteFile(public_path("Images/Users/{$employee->front_id_image}"));
            Files::deleteFile(public_path("Images/Users/{$employee->back_id_image}"));
        });
    }
}
