<?php
/*
 * File name: UnReadCriteria.php
 * Last modified: 2021.02.10 at 18:04:02
 * Author: Musa ATALAY - musaatalay.work@gmail.com
 * Copyright (c) 2022
 */

namespace App\Criteria\Notifications;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class UnReadCriteria.
 *
 * @package namespace App\Criteria\Notifications;
 */
class UnReadCriteria implements CriteriaInterface
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
        return $model->where('read_at', null);
    }
}
