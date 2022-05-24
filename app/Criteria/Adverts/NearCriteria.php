<?php
/*
 * File name: NearCriteria.php
 * Last modified: 2022.04.18 at 11:59:11
 * Author: Musa ATALAY - musaatalay.work@gmail.com
 * Copyright (c) 2022
 */

namespace App\Criteria\Adverts;

use Illuminate\Http\Request;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class NearCriteria.
 *
 * @package namespace App\Criteria\Adverts;
 */
class NearCriteria implements CriteriaInterface
{
    /**
     * @var array
     */
    private $request;

    /**
     * NearCriteria constructor.
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
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
        if ($this->request->has(['lat', 'lng'])) {
            $lat = $this->request->get('lat');
            $lng = $this->request->get('lng');
            return $model->with(['???' => function ($q) use ($lat, $lng) {
                return $q->near($lat, $lng);
            }]);
        } else {
            return $model;
        }
    }
}
