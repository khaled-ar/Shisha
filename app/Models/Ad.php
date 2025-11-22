<?php

namespace App\Models;

use App\Traits\Files;
use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{

    protected $hidden = [
        'created_at',
        'updated_at',
        'image'
    ];

    protected $appends = [
        'image_url'
    ];

    protected $guarded = [];

    public function getImageUrlAttribute()
    {
        return $this->image ? asset("Images/Ads") . '/' . $this->image : null;
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function($ad) {
            if(request()->has('image')) {
                $ad->image = Files::moveFile(request('image'), "Images/Ads");
                $ad->saveQuietly();
            }
        });

        static::deleting(function($ad) {
            Files::deleteFile(public_path("Images/Ads/{$ad->image}"));
        });
    }
}
