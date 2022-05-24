<?php
/*
 * File name: EarningOfDriverCriteria.php
 * Last modified: 2022.02.21 at 14:50:32
 * Author: Musa ATALAY - musaatalay.work@gmail.com
 * Copyright (c) 2022
 */

namespace App\Criteria\Earnings;

use App\Models\User;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class EarningOfDriverCriteria.
 *
 * @package namespace App\Criteria\Earnings;
 */
class EarningOfDriverCriteria implements CriteriaInterface
{
    /**
     * @var User
     */
    private $driver;

    /**
     * EarningOfDriverCriteria constructor.
     * @param User $driver
     */
    public function __construct(User $driver)
    {
        $this->driver = !is_null($driver) ? $driver : auth()->user();
    }


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
        if ($this->driver->hasRole('admin')) {
            return $model;
        }else if($this->driver->hasRole('driver')){
            return $model->join("users", "users.id", "=", "earnings.driver")
                ->groupBy('earnings.id')
                ->where('users.id', $this->driver->id);
        }else{
            return $model;
        }
    }
}
