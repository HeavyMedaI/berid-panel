<?php
/**
 * File name: FilterByUserCriteria.php
 * Last modified: 2021.01.02 at 19:12:31
 * Author: Musa ATALAY - musaatalay.work@gmail.com
 * Copyright (c) 2022
 */

namespace App\Criteria\Users;

use App\Models\User;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class FilterByUserCriteria.
 *
 * @package namespace App\Criteria;
 */
class FilterByUserCriteria implements CriteriaInterface
{
    /**
     * @var User
     */
    private $user;

    /**
     * FilterByUserCriterias constructor.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
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
        return $model->where(["user" => $this->user]);
    }
}
