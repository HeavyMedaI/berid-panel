<?php
/*
 * File name: Advert.php
 * Last modified: 2022.01.02
 * Author: Musa ATALAY - musaatalay.work@gmail.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Image\Exceptions\InvalidManipulation;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Illuminate\Support\Facades\Date;

/**
 * Class DriverDocuments
 * @package App\Models
 * @version MARCH 24, 2022
 *
 * @property string car_brand
 * @property string car_model
 * @property string car_plate
 * @property boolean is_ripped
 * @property integer user_id
 * @property integer status
 * @property string status_description
 * @property Date created_at
 * @property Date updated_at
 */
class DriverDocuments extends Model implements HasMedia
{
    use HasMediaTrait {
        getFirstMediaUrl as protected getFirstMediaUrlTrait;
    }

    //public $timestamps = true;

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'car_brand' => 'required|string|max:55',
        'car_model' => 'required|string|max:55',
        'car_plate' => 'required|string|max:25',
        'is_ripped' => 'required|boolean',
        'status' => 'integer',
        'status_description' => 'string',
    ];

    public $table = 'driver_documents';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $fillable = [
        "car_brand",
        "car_model",
        "car_plate",
        "is_ripped",
        "user_id",
        "status",
        "status_description",
        'updated_at'
    ];
    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'car_brand' => 'required|string',
        'car_model' => 'required|string',
        'car_plate' => 'required|string',
        'is_ripped' => 'required|boolean',
        'user_id' => 'integer',
        'status' => 'integer',
        'status_description' => 'string',
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
    protected $hidden = [

    ];

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
    public function getFirstMediaUrl($collectionName = 'driver_license_front', $conversion = '')
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
        return $this->hasMedia('driver_license_front')
            || $this->hasMedia('driver_license_back')
            || $this->hasMedia('driver_permit');
    }

    public function getIsRippedAttribute(): bool
    {
        return (bool) $this->attributes["is_ripped"];
    }

    public function removeMedia($collection_name) {
        $this->getMedia($collection_name)->delete();
    }

}
