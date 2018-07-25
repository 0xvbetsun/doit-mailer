<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Class User
 * @package App\Models
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    use Notifiable, SoftDeletes;

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->attributes['api_token'] = static::generateToken();
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email',
        'password'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'deleted_at',
        'updated_at',
        'created_at'
    ];

    public function getAvatar()
    {
        return sprintf('%s/storage/%s', config('app.url'), 'default.png');
    }

    public function getAvatarThumbnail()
    {
        return sprintf('%s/storage/%s', config('app.url'), 'default-thumbnail.png');
    }

    private static function generateToken()
    {
        return str_random(60);
    }
}
