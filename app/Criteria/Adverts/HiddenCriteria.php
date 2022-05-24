<?php
/**
 * File name: HiddenCriteria.php
 * Last modified: 2022.01.02 at 19:09:36
 * Author: Musa ATALAY - musaatalay.work@gmail.com
 * Copyright (c) 2022
 */

namespace App\Criteria\Adverts;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class HiddenCriteria.
 *
 * @package namespace App\Criteria\Adverts;
 */
class HiddenCriteria implements CriteriaInterface
{
    private $hidden = [];

    /**
     * HiddenCriteria constructor.
     * @param array $hidden
     */
    public function __construct(array $hidden)
    {
        $this->hidden = $hidden;
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
        return $repository->hidden($this->hidden);
    }
}
