<?php
/*
 * File name: AdvertsOfDriverCriteria.php
 * Last modified: 2022.02.21 at 14:50:32
 * Author: Musa ATALAY - musaatalay.work@gmail.com
 * Copyright (c) 2022
 */

namespace App\Criteria\Adverts;

use App\Models\User;
use Illuminate\Http\Request;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class AdvertsOfUserCriteria.
 *
 * @package namespace App\Criteria\Adverts;
 */
class AdvertsOfDriverCriteria implements CriteriaInterface
{
    /**
     * @var User
     */
    private $driver;

    /**
     * AdvertsOfUserCriteria constructor.
     */
    public function __construct(User $driver = null)
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
        } else if (auth()->user()->hasRole('driver')) {
            return $model->where('adverts.driver', $this->driver->id)
                ->select('adverts.*')
                ->groupBy('adverts.id');
        }
    }
}
