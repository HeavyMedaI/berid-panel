<?php
/*
 * File name: AdvertStatus.php
 * Last modified: 2021.04.12 at 09:49:57
 * Author: Musa ATALAY - musaatalay.work@gmail.com
 * Copyright (c) 2022
 */

namespace App\Models;

use App\Casts\AdvertStatusCast;
use App\Traits\HasTranslations;
use Eloquent as Model;

use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class AdvertStatus
 * @package App\Models
 * @version July 24, 2022, 7:18 pm UTC
 *
 * @property string status
 * @property int order
 */
class AdvertStatus extends Model implements Castable
{

    use HasTranslations;

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'status' => 'required|max:127',
        'order' => 'min:0'
    ];

    public $translatable = [
        'status',
    ];
    public $table = 'advert_statuses';
    public $fillable = [
        'status',
        'order'
    ];
    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'status' => 'string'
    ];
    /**
     * New Attributes
     *
     * @var array
     */
    protected $appends = [
        'custom_fields',

    ];

    /**
     * @return CastsAttributes|CastsInboundAttributes|string
     */
    public static function castUsing()
    {
        return AdvertStatusCast::class;
    }

    public function getCustomFieldsAttribute()
    {
        $hasCustomField = in_array(static::class, setting('custom_field_models', []));
        if (!$hasCustomField) {
            return [];
        }
        $array = $this->customFieldsValues()
            ->join('custom_fields', 'custom_fields.id', '=', 'custom_field_values.custom_field_id')
            ->where('custom_fields.in_table', '=', true)
            ->get()->toArray();

        return convertToAssoc($array, 'name');
    }

    public function customFieldsValues()
    {
        return $this->morphMany('App\Models\CustomFieldValue', 'customizable');
    }

    /**
     * @return HasOne
     **/
    public function advert(): HasOne
    {
        return $this->hasOne(Advert::class, 'status', 'id');
    }

}
