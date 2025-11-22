<?php

namespace App\Models;

use App\Traits\Files;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = [];
    protected $hidden = [
        'created_at',
        'updated_at',
        'images'
    ];

    protected $appends = [
        'images_urls',
    ];

    public function getImagesUrlsAttribute()
    {
        return $this->images ? collect(explode("|", $this->images))->map(function ($image) {
            return asset('Images/Products') . '/' . $image;
        }) : null;
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function($product) {
            $images = [];
            foreach(request('images') as $image) {
                $images[] = Files::moveFile($image, "Images/Products");
            }
            $product->images = implode('|', $images);
            $product->saveQuietly(); // This won't trigger events
        });

        static::deleting(function($product) {
            collect(explode("|", $product->images))->map(function ($image) {
                Files::deleteFile(public_path("Images/Products/{$image}"));
            });
        });

        static::updated(function($product) {
            if(request('images')) {
                collect(explode("|", $product->images))->map(function ($image) {
                    Files::deleteFile(public_path("Images/Products/{$image}"));
                });

                $images = [];
                foreach(request('images') as $image) {
                    $images[] = Files::moveFile($image, "Images/Products");
                }
                $product->images = implode('|', $images);
                $product->saveQuietly();
            }
        });
    }
}
