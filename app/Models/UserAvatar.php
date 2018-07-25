<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAvatar extends Model
{
    /**
     * @return string
     */
    public function getLink(): string
    {
        return sprintf('%s/storage/%s', config('app.url'), $this->path . $this->name);
    }
}
