<?php
/*
 * File name: User.php
 * Last modified: 2021.06.28 at 23:44:43
 * Author: Musa ATALAY - musaatalay.work@gmail.com
 * Copyright (c) 2022
 */

namespace App\Models;

use App\Casts\UserCast;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
use Spatie\Image\Exceptions\InvalidManipulation;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Hash;

/**
 * Class User
 * @package App\Models
 * @version July 10, 2018, 11:44 am UTC
 *
 * @property int id
 * @property string name
 * @property string email
 * @property string phone_number
 * @property string phone_verified_at
 * @property string password
 * @property string api_token
 * @property string device_token
 * @property boolean is_driver
 */
class User extends Authenticatable implements HasMedia, Castable
{
    use Notifiable;
    use Billable;
    use HasMediaTrait {
        getFirstMediaUrl as protected getFirstMediaUrlTrait;
    }
    use HasRoles;

    //public $is_driver;

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|string|max:255|unique:users',
        'phone_number' => 'required|max:255|unique:users',
        'password' => 'required',
    ];

    public $table = 'users';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $fillable = [
        'name',
        'email',
        'phone_number',
        'phone_verified_at',
        'password',
        'api_token',
        'device_token',
        'is_driver',
    ];
    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'name' => 'string',
        'email' => 'string',
        'phone_number' => 'string',
        'password' => 'string',
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'api_token' => 'string',
        'device_token' => 'string',
        'remember_token' => 'string',
        'is_driver' => 'boolean'
    ];
    /**
     * New Attributes
     *
     * @var array
     */
    protected $appends = [
        'custom_fields',
        'has_media',
        'is_driver'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token'
    ];

    /**
     * @return CastsAttributes|CastsInboundAttributes|string
     */
    public static function castUsing()
    {
        return UserCast::class;
    }

    /**
     * Route notifications for the FCM channel.
     *
     * @param \Illuminate\Notifications\Notification $notification
     * @return string|null
     */
    public function routeNotificationForFcm(\Illuminate\Notifications\Notification $notification): ?string
    {
        return $this->device_token;
    }

    /**
     * @param Media|null $media
     * @throws InvalidManipulation
     */
    public function registerMediaConversions(\Spatie\MediaLibrary\Models\Media $media = null)
    {
        $this->addMediaConversion('thumb')
            ->fit(Manipulations::FIT_CROP, 200, 200)
            ->sharpen(10);

        $this->addMediaConversion('icon')
            ->fit(Manipulations::FIT_CROP, 100, 100)
            ->sharpen(10);
    }

    /**
     * to generate media url in case of fallback will
     * return the file type icon
     * @param string $conversion
     * @return string url
     */
    public function getFirstMediaUrl($collectionName = 'default', $conversion = '')
    {
        $url = $this->getFirstMediaUrlTrait($collectionName);
        if ($url) {
            $array = explode('.', $url);
            $extension = strtolower(end($array));
            if (in_array($extension, config('medialibrary.extensions_has_thumb'))) {
                return asset($this->getFirstMediaUrlTrait($collectionName, $conversion));
            } else {
                return asset(config('medialibrary.icons_folder') . '/' . $extension . '.png');
            }
        } else {
            return asset('images/avatar_default.png');
        }
    }

    public function getCustomFieldsAttribute()
    {
        $hasCustomField = in_array(static::class, setting('custom_field_models', []));
        if (!$hasCustomField) {
            return [];
        }
        $array = $this->customFieldsValues()
            ->join('custom_fields', 'custom_fields.id', '=', 'custom_field_values.custom_field_id')
            ->select(['value', 'view', 'name'])
            ->get()->toArray();

        return convertToAssoc($array, 'name');
    }

    public function customFieldsValues(): MorphMany
    {
        return $this->morphMany('App\Models\CustomFieldValue', 'customizable');
    }

    public function getAuthPassword() {
        return $this->attributes["password"];
    }

    public function setPasswordAttribute($value)
    {
        if( \Hash::needsRehash($value) ) {
            $value = \Hash::make($value);
        }
        $this->attributes['password'] = $value;
    }

    /**
     * Add Media to api results
     * @return bool
     */
    public function getHasMediaAttribute(): bool
    {
        return $this->hasMedia('avatar');
    }

    /**
     * Add User Type to api results
     * @return bool
     */
    public function getIsDriverAttribute(): bool
    {
        return $this->attributes["is_driver"];
    }

    /**
     * @return BelongsToMany
     **/
    /*public function eProviders(): BelongsToMany
    {
        return $this->belongsToMany(EProvider::class, 'e_provider_users');
    }*/

}
