<?php
/*
 * File name: PaidAdvertsCriteria.php
 * Last modified: 2022.02.22 at 14:23:36
 * Author: Musa ATALAY - musaatalay.work@gmail.com
 * Copyright (c) 2022
 */

namespace App\Criteria\Bookings;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class PaidAdvertsCriteria.
 *
 * @package namespace App\Criteria\Adverts;
 */
class PaidAdvertsCriteria implements CriteriaInterface
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
        return $model->join('payments', 'payments.id', '=', 'adverts.payment')
            ->where('payments.payment_status_id', '2') // Paid Id
            ->groupBy('adverts.id')
            ->select('adverts.*');

    }
}
