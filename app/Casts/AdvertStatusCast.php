<?php
/*
 * File name: AdvertStatusCast.php
 * Last modified: 2022.03.25 at 22:21:11
 * Author: Musa ATALAY - musaatalay.work@gmail.com
 * Copyright (c) 2022
 */

namespace App\Casts;

use App\Models\AdvertStatus;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use InvalidArgumentException;

/**
 * Class AdvertStatusCast
 * @package App\Casts
 */
class AdvertStatusCast implements CastsAttributes
{

    /**
     * @inheritDoc
     */
    public function get($model, string $key, $value, array $attributes): ?AdvertStatus
    {
        $status = null;
        if (is_int($value)) {
            $status = AdvertStatus::find($value);
            /*if (!empty($status)) {
                return $status;
            }*/
        }else if (is_string($value)){
            $decodedValue = json_decode($value, true);
            $status = new AdvertStatus($decodedValue);
            array_push($status->fillable, 'id');
            $status->id = $decodedValue['id'];
            //return $status;
        }
        return $status;
    }

    /**
     * @inheritDoc
     */
    public function set($model, string $key, $value, array $attributes): array
    {
        if (!is_null($value) &&!$value instanceof AdvertStatus) {
            throw new InvalidArgumentException('The given value is not an AdvertStatus instance.');
        }

        if (!is_null($value)) {
            return [
                'status' => $value->id
            ];
        }
        return [];
    }
}
