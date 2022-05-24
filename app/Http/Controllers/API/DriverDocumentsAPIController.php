<?php
/*
 * File name: CategoryAPIController.php
 * Last modified: 2021.03.24 at 21:33:26
 * Author: Musa ATALAY - musaatalay.work@gmail.com
 * Copyright (c) 2022
 */

namespace App\Http\Controllers\API;


use App\Criteria\Adverts\NearCriteria;
use App\Criteria\Adverts\ParentCriteria;
use App\Criteria\Adverts\AdvertsOfUserCriteria;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateDriverDocumentsRequest;
use App\Http\Requests\UpdateDriverDocumentsRequest;
use App\Models\DriverDocuments;
use App\Repositories\DriverDocumentsRepository;
use App\Repositories\UploadRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * Class DriverDocumentsAPIController
 * @package App\Http\Controllers\API
 */
class DriverDocumentsAPIController extends Controller
{
    /** @var  DriverDocumentsRepository */
    private $driverDocumentsRepository;

    private $uploadRepository;

    public function __construct(DriverDocumentsRepository $driverDocumentsRepo, UploadRepository $uploadRepo)
    {
        $this->driverDocumentsRepository = $driverDocumentsRepo;
        $this->uploadRepository = $uploadRepo;
    }

    /**
     * Display a listing of the DriverDocuments.
     * GET|HEAD /driver_documents
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        //var_dump($_GET);
        //exit;
        try{
            $this->driverDocumentsRepository->pushCriteria(new RequestCriteria($request));
            $this->driverDocumentsRepository->pushCriteria(new ParentCriteria($request));
            $this->driverDocumentsRepository->pushCriteria(new NearCriteria($request));
            $this->driverDocumentsRepository->pushCriteria(new LimitOffsetCriteria($request));
            $this->driverDocumentsRepository->pushCriteria(new AdvertsOfUserCriteria(auth()->user()));
        } catch (RepositoryException $e) {
            return $this->sendError($e->getMessage());
        }
        $driverDocuments = $this->driverDocumentsRepository->all();

        return $this->sendResponse($driverDocuments->toArray(), 'Driver documents retrieved successfully');
    }

    /**
     * Display the specified DriverDocuments.
     * GET|HEAD /driver_documents/{id}
     *
     * @param int $id
     *
     * @return JsonResponse
     */
    public function show($id)
    {
        /** @var DriverDocuments $driverDocuments */
        if (!empty($this->driverDocumentsRepository)) {
            $driverDocuments = $this->driverDocumentsRepository->findWithoutFail($id);
        }
        unset($driverDocuments->user_id);
        unset($driverDocuments->created_at);
        unset($driverDocuments->updated_at);

        if (empty($driverDocuments)) {
            return $this->sendError('Driver documents not found');
        }

        return $this->sendResponse($driverDocuments->toArray(), 'Driver documents retrieved successfully');
    }

    /**
     * Display a listing of the DriverDocuments.
     * GET|HEAD /driver_documents
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function showByUserId(Request $request)
    {
        /** @var DriverDocuments $driverDocuments */
        if (!empty($this->driverDocumentsRepository)) {
            $driverDocuments = $this->driverDocumentsRepository->orderBy("id", "desc")->findByField("user_id", auth()->id())->first();
        }
        //unset($driverDocuments->user_id);
        unset($driverDocuments->created_at);
        unset($driverDocuments->updated_at);

        if (empty($driverDocuments)) {
            return $this->sendError('Driver documents not found');
        }

        return $this->sendResponse($driverDocuments->toArray(), 'Driver documents retrieved successfully');
    }

    /**
     * Create a new DriverDocuments instance
     *
     * @param CreateDriverDocumentsRequest $request
     * @return
     */
    function store(CreateDriverDocumentsRequest $request)
    {
        try {
            $request->validate($request->rules());

            $input = $request->post();

            //var_dump($request); exit;

            $images = $input["images"];
            unset($input["images"]);

            $driverDocuments = new DriverDocuments();
            $driverDocuments->user_id = auth()->id();
            $driverDocuments->updated_at = date("Y-m-d H:i:s");
            foreach ($input as $key => $value) {
                if ($key != "user_id") {
                    $driverDocuments->$key = $value;
                }
            }
            $driverDocuments->save();

            if (!is_null($images) && is_array($images) && count($images) > 0) {
                foreach ($images as $m_index => $media) {
                    if (is_null($media) || $media == "null" || strpos($media, "uuid") === false) {
                        continue;
                    }
                    $media = json_decode($media, true);
                    if ($media["uuid"] == null) {
                        continue;
                    }
                    $cacheUpload = $this->uploadRepository->getByUuid($media["uuid"]);
                    $mediaItem = $cacheUpload->getMedia($m_index)->first();
                    $mediaItem->copy($driverDocuments, $m_index);
                    //$mediaItem->delete();
                }
            }
        } catch (ValidationException $e) {
            return $this->sendError(array_values($e->errors()));
        } catch (Exception $e) {
            return $this->sendError($e->getMessage(), 200);
        }

        //$advert->media = $images;
        $driverDocuments->medias = gettype($images);


        return $this->sendResponse($driverDocuments, 'Driver documents stored successfully');
    }

    /**
     * Update the specified User in storage.
     *
     * @param UpdateDriverDocumentsRequest $request
     *
     * @return Application|RedirectResponse|Redirector|Response
     */
    public function update(UpdateDriverDocumentsRequest $request)
    {
        $request->validate($request->rules());

        $input = $request->post();

        $id = $input["id"];
        unset($input["id"]);

        $driverDocuments = $this->driverDocumentsRepository->findWithoutFail($id);
        if (empty($driverDocuments)) {
            return $this->sendError(__('lang.not_found', ['operator' => __('lang.user')]), 200);
        }

        if ($driverDocuments->user_id != auth()->id()) {
            return $this->sendError('Permission denied', 200);
        }

        $images = $input["images"];
        unset($input["images"]);

        try {
            /*if (isset($input['media']) && $input['media']) {
                foreach ($input["media"] as $media) {
                    $cacheUpload = $this->uploadRepository->getByUuid($media);
                    $mediaItem = $cacheUpload->getMedia('media')->first();
                    $mediaItem->copy($advert, 'advert_media');
                    //$mediaItem->delete();
                }
            }*/

            $driverDocuments = $this->driverDocumentsRepository->update($input, $id);

            if (!is_null($images) && is_array($images) && count($images) > 0) {
                foreach ($images as $m_index => $media) {
                    if (is_null($media) || $media == "null" || strpos($media, "uuid") === false) {
                        continue;
                    }
                    $media = json_decode($media, true);
                    if ($media["uuid"] == null) {
                        continue;
                    }
                    //$driverDocuments->removeMedia($m_index);
                    $cacheUpload = $this->uploadRepository->getByUuid($media["uuid"]);
                    $mediaItem = $cacheUpload->getMedia($m_index)->first();
                    $mediaItem->copy($driverDocuments, $m_index);
                    //$mediaItem->delete();
                }
            }
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 200);
        }


        return $this->sendResponse($driverDocuments, 'Driver documents updated successfully');

    }
}
