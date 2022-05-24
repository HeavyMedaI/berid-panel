<?php
/**
 * File name: CustomersCriteria.php
 * Last modified: 2021.01.02 at 19:12:31
 * Author: Musa ATALAY - musaatalay.work@gmail.com
 * Copyright (c) 2022
 */

namespace App\Criteria\Users;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class CustomerCriteria.
 *
 * @package namespace App\Criteria\Users;
 */
class DriversCriteria implements CriteriaInterface
{
    /**
     * Apply criteria in query repository
     *
     * @param string              $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
        return $model->whereHas("roles", function($q){ $q->where("name", "driver"); });
    }
}
