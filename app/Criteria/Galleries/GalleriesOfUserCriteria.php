<?php
/*
 * File name: GalleriesOfUserCriteria.php
 * Last modified: 2022.02.21 at 14:50:32
 * Author: Musa ATALAY - musaatalay.work@gmail.com
 * Copyright (c) 2022
 */

namespace App\Criteria\Galleries;

use App\Models\User;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class GalleriesOfUserCriteria.
 *
 * @package namespace App\Criteria\Galleries;
 */
class GalleriesOfUserCriteria implements CriteriaInterface
{
    /**
     * @var User
     */
    private $userId;

    /**
     * GalleriesOfUserCriteria constructor.
     */
    public function __construct($userId)
    {
        $this->userId = $userId;
    }

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
        if (auth()->user()->hasRole('admin')) {
            return $model;
        } elseif (auth()->user()->hasRole('provider')) {
            return $model->join('e_provider_users', 'e_provider_users.e_provider_id', '=', 'galleries.e_provider_id')
                ->groupBy('galleries.id')
                ->select('galleries.*')
                ->where('e_provider_users.user_id', $this->userId);
        } else {
            return $model;
        }
    }
}
