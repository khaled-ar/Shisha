<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Traits\Files;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    protected $guarded = ['role'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'created_at',
        'updated_at',
        'fcm',
        'image'
    ];

    protected $appends = [
        'image_url'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'phone_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function employee() {
        return $this->hasOne(Employee::class);
    }

    public function getImageUrlAttribute()
    {
        return $this->image ? asset("Images/Users") . '/' . $this->image : null;
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function($user) {
            if(request()->has('image')) {
                $user->image = Files::moveFile(request('image'), "Images/Users");
                $user->save();
            }
        });

        static::deleting(function($user) {
            Files::deleteFile(public_path("Images/Users/{$user->image}"));
        });
    }
}
