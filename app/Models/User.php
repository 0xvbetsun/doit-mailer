<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Class User
 * @package App\Models
 * @property int $id
 * @property string $email
 * @property string $password
 * @property string $api_token
 * @property \Carbon\Carbon|null $deleted_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \Carbon\Carbon|null $created_at
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\UserAvatar[] $avatars
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\UserAvatar[] $thumbnails
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
        'api_token',
        'deleted_at',
        'updated_at',
        'created_at'
    ];

    /**
     * @return  HasMany
     */
    public function avatars(): HasMany
    {
        return $this->hasMany(UserAvatar::class)
            ->where('type', '=', UserAvatar::TYPE_MAIN)
            ->orderBy('created_at', 'DESC')
            ;
    }

    /**
     * @return HasMany
     */
    public function thumbnails(): HasMany
    {
        return $this->hasMany(UserAvatar::class)
            ->where('type', '=', UserAvatar::TYPE_THUMBNAIL)
            ->orderBy('created_at', 'DESC')
            ;
    }

    /**
     * @return string
     */
    public function getAvatar(): string
    {
        $link = 'default.png';
        if ($this->avatars->isNotEmpty()) {
            $image = $this->avatars()->first();
            $link = $image->path . $image->name;
        }

        return sprintf('%s/storage/%s', config('app.url'), $link);
    }

    /**
     * @return string
     */
    public function getAvatarThumbnail(): string
    {
        $link = 'default-thumbnail.png';
        if ($this->thumbnails->isNotEmpty()) {
            $image = $this->thumbnails()->first();
            $link = $image->path . $image->name;
        }

        return sprintf('%s/storage/%s', config('app.url'), $link);
    }

    /**
     * @return string
     */
    private static function generateToken(): string
    {
        return str_random(60);
    }
}
