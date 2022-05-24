<?php
/*
 * File name: DriverDocumentsOfDriverCriteria.php
 * Last modified: 2022.01.25 at 19:19:41
 * Author: Musa ATALAY - musaatalay.work@gmail.com
 * Copyright (c) 2022
 */

namespace App\Criteria\DriverDocuments;

use App\Models\User;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class DriverDocumentsOfDriverCriteria.
 *
 * @package namespace App\Criteria\DriverDocuments;
 */
class DriverDocumentsOfDriverCriteria implements CriteriaInterface
{
    /**
     * @var User
     */
    private $user;

    /**
     * CouponsOfUserCriteria constructor.
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
        return $model->where("user_id", $this->user->id);
    }
}
