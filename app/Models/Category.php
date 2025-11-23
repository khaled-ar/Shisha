<?php

namespace App\Models;

use App\Traits\Files;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'created_at',
        'updated_at',
        'logo'
    ];

    protected $guarded = [];

    protected $appends = [
        'logo_url'
    ];

    public function scopeParent($query) {
        $parent = request('parent');
        if($parent && $parent == 'yes') {
            $query->whereNull('parent_id');
        } elseif($parent && $parent == 'no') {
            $query->whereNotNull('parent_id');
        }
        return $query;
    }

    public function children() {
        return $this->hasMany(Category::class, 'parent_id')->with(['children', 'products']);
    }

    public function products() {
        return $this->hasMany(Product::class);
    }

    public function getLogoUrlAttribute()
    {
        return $this->logo ? asset("Images/Logos") . '/' . $this->logo : null;
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function($category) {
            if(request()->has('logo')) {
                $category->logo = Files::moveFile(request('logo'), "Images/Logos");
                $category->save();
            }
        });

        static::deleting(function($category) {
            Files::deleteFile(public_path("Images/Logos/{$category->logo}"));
        });
    }
}
