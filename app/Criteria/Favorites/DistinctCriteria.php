<?php
/*
 * File name: DistinctCriteria.php
 * Last modified: 2022.02.11 at 09:26:34
 * Author: Musa ATALAY - musaatalay.work@gmail.com
 * Copyright (c) 2022
 */

namespace App\Criteria\Favorites;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class DistinctCriteria.
 *
 * @package namespace App\Criteria\Favorites;
 */
class DistinctCriteria implements CriteriaInterface
{
    /**
     * Apply criteria in query repository
     *
     * @param string $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
        return $model->groupBy('???');
    }
}
