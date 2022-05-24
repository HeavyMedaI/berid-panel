<?php
/**
 * File name: UserRepository.php
 * Last modified: 2021.01.03 at 15:29:51
 * Author: Musa ATALAY - musaatalay.work@gmail.com
 * Copyright (c) 2022
 */

namespace App\Repositories;

use App\Models\DriverDocuments;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class DriverDocumentsRepository
 * @package App\Repositories
 * @version July 10, 2018, 11:44 am UTC
 *
 * @method DriverDocuments findWithoutFail($id, $columns = ['*'])
 * @method DriverDocuments find($id, $columns = ['*'])
 * @method DriverDocuments first($columns = ['*'])
 */
class DriverDocumentsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        "car_brand",
        "car_model",
        "car_plate",
        "is_ripped",
        "user_id",
        "status",
        'created_at',
        'updated_at'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return DriverDocuments::class;
    }
}
