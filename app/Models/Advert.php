<?php
/*
 * File name: Advert.php
 * Last modified: 2022.01.02
 * Author: Musa ATALAY - musaatalay.work@gmail.com
 */

namespace App\Models;

use App\Casts\AdvertCast;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Image\Exceptions\InvalidManipulation;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Illuminate\Support\Facades\Date;
use Illuminate\Contracts\Database\Eloquent\Castable;

/**
 * Class Advert
 * @package App\Models
 * @version February 2, 2022
 *
 * @property string city_pick_up
 * @property string city_drop_off
 * @property string place_pick_up
 * @property string place_drop_off
 * @property string receiver_full_name
 * @property string receiver_phone
 * @property boolean is_urgent
 * @property integer quantity
 * @property integer size
 * @property string title
 * @property string description
 * @property double price
 * @property Date date_pick_up
 * @property Date date_drop_off
 * @property User user
 * @property User driver
 * @property Payment payment
 * @property AdvertStatus status
 * @property Date created_at
 * @property Date updated_at
 */
class Advert extends Model implements HasMedia, Castable
{
    use HasMediaTrait {
        getFirstMediaUrl as protected getFirstMediaUrlTrait;
    }

    public $timestamps = true;

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'ref_code' => 'required|integer|max:9',
        'city_pick_up' => 'required|json',
        'city_drop_off' => 'required|json', #|unique:users
        'place_pick_up' => 'required|string|max:255',
        'place_drop_off' => 'required|string|max:255',
        'receiver_full_name' => 'required|string|max:35',
        'receiver_phone' => 'required|string|max:20',
        'is_urgent' => 'required|boolean',
        'quantity' => 'required|integer',
        //'size' => 'required|array|min:3|max:3',
        'size' => 'required|integer',
        'title' => 'required|string|max:155',
        'description' => 'required|string|max:500',
        'price' => 'required|numeric|min:0.01|max:99999999,99',
        'user' => 'nullable|integer|exists:users,id',
        'driver' => 'nullable|integer|exists:users,id',
        'payment' => 'nullable|integer|exists:payments,id',
        'status' => 'required|integer|exists:advert_statuses,id',
        'date_pick_up' => 'required|date_format:Y-m-d',
        'date_drop_off' => 'required|date_format:Y-m-d',
        //'updated_at' => 'required|date_format:Y-m-d H:i:s'
    ];

    public $table = 'adverts';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $fillable = [
        "ref_code",
        "city_pick_up",
        "city_drop_off",
        "place_pick_up",
        "place_drop_off",
        "receiver_fullName",
        "receiver_phone",
        "is_urgent",
        "quantity",
        "size",
        "title",
        "description",
        "price",
        "date_pick_up",
        "date_drop_off",
        "user",
        "driver",
        "payment",
        "status",
        'updated_at'
    ];
    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'ref_code' => 'integer',
        'city_pick_up' => 'string',
        'city_drop_off' => 'string',
        'place_pick_up' => 'string',
        'place_drop_off' => 'string',
        'receiver_fullName' => 'string',
        'receiver_phone' => 'string',
        'is_urgent' => 'boolean',
        'quantity' => 'integer',
        'size' => 'integer',
        'title' => 'string',
        'description' => 'string',
        'price' => 'double',
        'date_pick_up' => 'string',
        'date_drop_off' => 'string',
        'user' => User::class,
        'driver' => User::class,
        'payment' => Payment::class,
        'status' => AdvertStatus::class,
        'created_at' => 'required|date_format:Y-m-d H:i:s',
        'updated_at' => 'required|date_format:Y-m-d H:i:s',
    ];
    /**
     * New Attributes
     *
     * @var array
     */
    protected $appends = [
        'has_media',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * @return CastsAttributes|CastsInboundAttributes|string
     */
    public static function castUsing()
    {
        return AdvertCast::class;
    }

    /**
     * @return BelongsTo
     **/
    public function user()
    {
        return $this->belongsTo(User::class, 'user', 'id');
    }

    /**
     * @return BelongsTo
     **/
    public function driver()
    {
        return $this->belongsTo(User::class, 'driver', 'id');
    }

    /**
     * @return BelongsTo
     **/
    public function payment()
    {
        return $this->belongsTo(Payment::class, 'payment', 'id');
    }

    /**
     * @return BelongsTo
     **/
    public function status()
    {
        return $this->belongsTo(AdvertStatus::class, 'status', 'id');
    }

    public function getTotal(): float
    {
        $total = $this->price;
        $total += $this->getTaxesValue();
        return $total;
    }

    public function getSubtotal(): float
    {
        $total = 0;
        return $total;
    }

    public function getTaxesValue(): float
    {
        $total = $this->getSubtotal();
        $taxValue = $total + 0;
        return $taxValue;
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
    public function getFirstMediaUrl($collectionName = 'advert_media', $conversion = '')
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
            return asset('images/image_default.png');
        }
    }

    /**
     * Add Media to api results
     * @return bool
     */
    public function getHasMediaAttribute(): bool
    {
        return $this->hasMedia('advert_media');
    }

    public function getIsDriverAttribute(): bool
    {
        return (bool) $this->is_driver;
    }

    /**
     * @return BelongsToMany
     **/
    /*public function mediaLibrary(): BelongsToMany
    {
        return $this->belongsToMany(Media::class, 'advert_media_library');
    }*/

}
