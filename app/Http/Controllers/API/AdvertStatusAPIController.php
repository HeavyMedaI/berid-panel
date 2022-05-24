<?php
/*
 * File name: BookingStatusAPIController.php
 * Last modified: 2021.02.12 at 11:06:02
 * Author: Musa ATALAY - musaatalay.work@gmail.com
 * Copyright (c) 2022
 */

namespace App\Http\Controllers\API;


use App\Http\Controllers\Controller;
use App\Models\AdvertStatus;
use App\Repositories\AdvertStatusRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;

/**
 * Class AdvertStatusAPIController
 * @package App\Http\Controllers\API
 */
class AdvertStatusAPIController extends Controller
{
    /** @var  AdvertStatusRepository */
    private $advertStatusRepository;

    public function __construct(AdvertStatusRepository $advertStatusRepository)
    {
        $this->advertStatusRepository = $advertStatusRepository;
    }

    /**
     * Display a listing of the AdvertStatus.
     * GET|HEAD /advertStatuses
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        try{
            $this->advertStatusRepository->pushCriteria(new RequestCriteria($request));
            $this->advertStatusRepository->pushCriteria(new LimitOffsetCriteria($request));
        } catch (RepositoryException $e) {
            return $this->sendError($e->getMessage());
        }
        $advertStatuses = $this->advertStatusRepository->all();
        $this->filterCollection($request, $advertStatuses);

        return $this->sendResponse($advertStatuses->toArray(), 'Advert Statuses retrieved successfully');
    }

    /**
     * Display the specified AdvertStatus.
     * GET|HEAD /advertStatuses/{id}
     *
     * @param int $id
     *
     * @return JsonResponse
     */
    public function show($id)
    {
        /** @var AdvertStatus $advertStatus */
        if (!empty($this->advertStatusRepository)) {
            $advertStatus = $this->advertStatusRepository->findWithoutFail($id);
        }

        if (empty($advertStatus)) {
            return $this->sendError('Advert Status not found');
        }

        return $this->sendResponse($advertStatus->toArray(), 'Advert Status retrieved successfully');
    }
}
