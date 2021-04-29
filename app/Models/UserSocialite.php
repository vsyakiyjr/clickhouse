<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UserSocialite
 *
 * @property int $id
 * @property int $user_id
 * @property string $provider
 * @property string $api_data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserSocialite newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserSocialite newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserSocialite query()
 * @mixin \Eloquent
 */
class UserSocialite extends Model
{
    
    protected $fillable = [
        'provider',
        'api_data',
    ];
}
