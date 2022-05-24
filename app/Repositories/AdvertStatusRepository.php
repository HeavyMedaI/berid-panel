<?php
/*
 * File name: AdvertStatusRepository.php
 * Last modified: 2021.01.25 at 22:00:21
 * Author: Musa ATALAY - musaatalay.work@gmail.com
 * Copyright (c) 2022
 */

namespace App\Repositories;

use App\Models\AdvertStatus;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class AdvertStatusRepository
 * @package App\Repositories
 * @version July 24, 2022, 7:18 pm UTC
 *
 * @method AdvertStatus findWithoutFail($id, $columns = ['*'])
 * @method AdvertStatus find($id, $columns = ['*'])
 * @method AdvertStatus first($columns = ['*'])
 */
class AdvertStatusRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'status',
        'order'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return AdvertStatus::class;
    }
}
