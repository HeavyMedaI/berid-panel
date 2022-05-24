<?php
/*
 * File name: UserCast.php
 * Last modified: 2022.03.25 at 22:21:11
 * Author: Musa ATALAY - musaatalay.work@gmail.com
 * Copyright (c) 2022
 */

namespace App\Casts;

use App\Models\User;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use InvalidArgumentException;

/**
 * Class UserCast
 * @package App\Casts
 */
class UserCast implements CastsAttributes
{

    /**
     * @inheritDoc
     */
    public function get($model, string $key, $value, array $attributes): ?User
    {
        /*if (is_null($value)) {
            return $value;
        }*/
        $user = null;
        if (is_numeric($value)) {
            $user = User::find($value);
            /*if (!empty($user)) {
                return $user;
            }*/
        }else if (is_string($value)) {
            $decodedValue = json_decode($value, true);
            $user = new User($decodedValue);
            array_push($user->fillable, 'id');
            $user->id = $decodedValue['id'];
            //return $user;
        }
        return $user;
    }

    /**
     * @inheritDoc
     */
    public function set($model, string $key, $value, array $attributes): array
    {
        if (!is_null($value) && !$value instanceof User) {
            throw new InvalidArgumentException('The given '.$key.'::('.gettype($value).')value is not an User instance.');
        }

        if (!is_null($value)) {
            {
                return [
                    'user' => $value->id
                ];
            }
        }

        return [];
    }
}
