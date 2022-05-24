<?php
/*
 * File name: WalletTransaction.php
 * Last modified: 2021.08.10 at 18:03:35
 * Author: Musa ATALAY - musaatalay.work@gmail.com
 * Copyright (c) 2022
 */

namespace App\Models;

use App\Events\AdvertTransactionCreatedEvent;
use App\Events\AdvertTransactionCreatingEvent;
use App\Events\WalletTransactionCreatedEvent;
use App\Events\WalletTransactionCreatingEvent;
use App\Events\WalletTransactionEvent;
use App\Traits\Uuids;
use Eloquent as Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Date;

/**
 * Class AdvertTransaction
 * @package App\Models
 * @version August 8, 2021, 3:57 pm CEST
 *
 * @property Advert advert
 * @property User driver
 * @property int status
 * @property Date created_at
 * @property Date updated_at
 */
class AdvertTransaction extends Model
{
    use Uuids;

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'advert' => 'required|integer|exists:users,id',
        'driver' => 'required|integer|exists:users,id',
        'status' => 'required|integer', # 0: Pending, 1: Confirmed, 2: Denied
    ];
    public $table = 'advert_transactions';
    public $fillable = [
        'advert',
        'driver',
        'updated_at',
        'status'
    ];
    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'advert' => Advert::class,
        'driver' => User::class,
        'status' => 'required|integer',
        'created_at' => 'required|date_format:Y-m-d H:i:s',
        'updated_at' => 'required|date_format:Y-m-d H:i:s',
    ];

    /**
     * New Attributes
     *
     * @var array
     */
    protected $appends = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'creating' => AdvertTransactionCreatingEvent::class,
        'created' => AdvertTransactionCreatedEvent::class,
    ];

    /**
     * @return BelongsTo
     **/
    public function advert(): BelongsTo
    {
        return $this->belongsTo(Advert::class, 'advert', 'id');
    }

    /**
     * @return BelongsTo
     **/
    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver', 'id');
    }
}
