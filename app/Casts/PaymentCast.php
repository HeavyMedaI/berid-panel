<?php
/*
 * File name: PaymentCast.php
 * Last modified: 2022.03.25 at 22:21:11
 * Author: Musa ATALAY - musaatalay.work@gmail.com
 * Copyright (c) 2022
 */

namespace App\Casts;

use App\Models\Payment;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use InvalidArgumentException;

/**
 * Class PaymentCast
 * @package App\Casts
 */
class PaymentCast implements CastsAttributes
{

    /**
     * @inheritDoc
     */
    public function get($model, string $key, $value, array $attributes): ?Payment
    {
        $payment = null;
        if (is_int($value)) {
            $payment = Payment::find($value);
            /*if (!empty($payment)) {
                return $payment;
            }*/
        }else if (is_string($value)) {
            $decodedValue = json_decode($value, true);
            $payment = new Payment($decodedValue);
            array_push($payment->fillable, 'id');
            $payment->id = $decodedValue['id'];
            //return $payment;
        }
        return $payment;
    }

    /**
     * @inheritDoc
     */
    public function set($model, string $key, $value, array $attributes): array
    {
        if (!is_null($value) && !$value instanceof Payment) {
            throw new InvalidArgumentException('The given value is not an Payment instance.');
        }

        if (!is_null($value)){
            return ['payment' => $value->id];
        }
        return [];
    }
}
