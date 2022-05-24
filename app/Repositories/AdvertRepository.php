<?php
/**
 * File name: UserRepository.php
 * Last modified: 2021.01.03 at 15:29:51
 * Author: Musa ATALAY - musaatalay.work@gmail.com
 * Copyright (c) 2022
 */

namespace App\Repositories;

use App\Models\Advert;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class AdvertRepository
 * @package App\Repositories
 * @version July 10, 2018, 11:44 am UTC
 *
 * @method Advert findWithoutFail($id, $columns = ['*'])
 * @method Advert find($id, $columns = ['*'])
 * @method Advert first($columns = ['*'])
 */
class AdvertRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
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
        "user_id",
        "driver_id",
        "payment_id",
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Advert::class;
    }
}
