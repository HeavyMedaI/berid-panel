<?php
/*
 * File name: FavoriteRepository.php
 * Last modified: 2021.01.23 at 12:12:04
 * Author: Musa ATALAY - musaatalay.work@gmail.com
 * Copyright (c) 2022
 */

namespace App\Repositories;

use App\Models\Favorite;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class FavoriteRepository
 * @package App\Repositories
 * @version January 22, 2021, 8:58 pm UTC
 *
 * @method Favorite findWithoutFail($id, $columns = ['*'])
 * @method Favorite find($id, $columns = ['*'])
 * @method Favorite first($columns = ['*'])
 */
class FavoriteRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'e_service_id',
        'user_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Favorite::class;
    }
}
