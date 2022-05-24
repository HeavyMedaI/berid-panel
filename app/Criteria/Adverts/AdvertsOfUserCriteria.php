<?php
/*
 * File name: AdvertsOfUserCriteria.php
 * Last modified: 2022.02.21 at 14:50:32
 * Author: Musa ATALAY - musaatalay.work@gmail.com
 * Copyright (c) 2022
 */

namespace App\Criteria\Adverts;

use App\Models\User;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class AdvertsOfUserCriteria.
 *
 * @package namespace App\Criteria\Adverts;
 */
class AdvertsOfUserCriteria implements CriteriaInterface
{
    /**
     * @var User
     */
    private $user;

    /**
     * AdvertsOfUserCriteria constructor.
     */
    public function __construct(User $user)
    {
        $this->user = !is_null($user) ? $user : auth()->user();
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
        if ($this->user->hasRole('admin')) {
            return $model;
        } else if ($this->user->hasRole('user')) {
            return $model->where('adverts.user', $this->user->id)
                ->select('adverts.*')
                ->groupBy('adverts.id');
        }
    }
}
