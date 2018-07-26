<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class UserAvatar
 * @package App\Models
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string $path
 * @property \Carbon\Carbon|null $deleted_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \Carbon\Carbon|null $created_at
 * @property-read \App\Models\User $user
 * @mixin \Eloquent
 */
class UserAvatar extends Model
{
    const TYPE_MAIN = 'main';
    const TYPE_THUMBNAIL = 'thumbnail';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'type',
        'name',
        'path'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'deleted_at',
        'updated_at',
        'created_at'
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return string
     */
    public function getLink(): string
    {
        return sprintf('%s/storage/%s', config('app.url'), $this->path . $this->name);
    }
}
