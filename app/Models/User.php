<?php

namespace App\Models;

use App\Notifications\ResetPassword;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * App\Models\User
 *
 * @property int                                                                                                            $id
 * @property int                                                                                                            $role_id
 * @property string                                                                                                         $name
 * @property string                                                                                                         $phone
 * @property string                                                                                                         $email
 * @property string                                                                                                         $password
 * @property string|null                                                                                                    $remember_token
 * @property string|null                                                                                                    $api_token
 * @property int|null                                                                                                       $active
 * @property \Illuminate\Support\Carbon|null                                                                                $created_at
 * @property \Illuminate\Support\Carbon|null                                                                                $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read \App\Models\Role                                                                                          $role
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User query()
 * @mixin \Eloquent
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\UserSocialite[] $socialites
 * @property-read int|null $socialites_count
 */
class User extends Authenticatable {
	use Notifiable;

	const SOCIALITE_FACEBOOK = 'facebook';
	const SOCIALITE_GOOGLE = 'google';
	const SOCIALITE_VK = 'vkontakte';

	const SOCIALITE_PROVIDERS = [self::SOCIALITE_VK, self::SOCIALITE_FACEBOOK, self::SOCIALITE_GOOGLE];


	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'name',
		'phone',
		'email',
		'password',
		'remember_token',
		'api_token',
		'active',
	];

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = [
		'password',
	];

	public function sendPasswordResetNotification($token) {
		$this->notify(new ResetPassword($token));
	}

	public function role() {
		return $this->belongsTo('App\Models\Role');
	}

	public function socialites()
	{
		return $this->hasMany(UserSocialite::class);
	}
}
