<?php
/*
 * File name: AdvertCast.php
 * Last modified: 2022.03.25 at 22:21:11
 * Author: Musa ATALAY - musaatalay.work@gmail.com
 * Copyright (c) 2022
 */

namespace App\Casts;

use App\Models\Advert;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use InvalidArgumentException;

/**
 * Class AdvertCast
 * @package App\Casts
 */
class AdvertCast implements CastsAttributes
{

    /**
     * @inheritDoc
     */
    public function get($model, string $key, $value, array $attributes): ?Advert
    {
        $advert = null;
        if (is_int($value)) {
            $advert = Advert::find($value);
            /*if (!empty($advert)) {
                return $advert;
            }*/
        }else if (is_string($value)){
            $decodedValue = json_decode($value, true);
            $advert = new Advert($decodedValue);
            array_push($advert->fillable, 'id');
            $advert->id = $decodedValue['id'];
            //return $status;
        }
        return $advert;
    }

    /**
     * @inheritDoc
     */
    public function set($model, string $key, $value, array $attributes): array
    {
        if (!is_null($value) &&!$value instanceof Advert) {
            throw new InvalidArgumentException('The given value is not an Advert instance.');
        }

        if (!is_null($value)) {
            return [
                'advert' => $value->id
            ];
        }
        return [];
    }
}
