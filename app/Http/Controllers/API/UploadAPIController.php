<?php
/*
 * File name: UploadAPIController.php
 * Last modified: 2021.06.10 at 20:38:02
 * Author: Musa ATALAY - musaatalay.work@gmail.com
 * Copyright (c) 2022
 */

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadRequest;
use App\Repositories\UploadRepository;
use Illuminate\Http\Request;
use Exception;
use Prettus\Validator\Exceptions\ValidatorException;

class UploadAPIController extends Controller
{
    private $uploadRepository;

    /**
     * UploadController constructor.
     * @param UploadRepository $uploadRepository
     */
    public function __construct(UploadRepository $uploadRepository)
    {
        parent::__construct();
        $this->uploadRepository = $uploadRepository;
    }

    /**
     * Display the specified Media.
     * GET|HEAD /upload/{uuid}
     *
     * @param int $id
     *
     * @return JsonResponse
     */
    public function show($uuid, Request $request)
    {
        $collection_name = "advert_media";

        if ($request->input("field") && $request->input("field") != "") {
            $collection_name = $request->input("field");
        }

        $cacheUpload = $this->uploadRepository->getByUuid($uuid);
        //var_dump($cacheUpload);
        return $this->sendResponse($cacheUpload->getMedia($collection_name)->first()->toArray(), 'Advert retrieved successfully');
        /*$allMedias = $this->uploadRepository->allMedia($collection);
        if (!auth()->user()->hasRole('admin')) {
            $allMedias = $allMedias->filter(function ($element) {
                if (isset($element['custom_properties']['user_id'])) {
                    return $element['custom_properties']['user_id'] == auth()->id();
                }
                return false;
            });
        }
        return $allMedias->toJson();*/
    }

    /**
     * @param UploadRequest $request
     */
    public function store(UploadRequest $request)
    {
        $input = $request->all();
        try {
            $upload = $this->uploadRepository->create($input);
            $upload->addMedia($input['file'])
                ->withCustomProperties(['uuid' => $input['uuid'], 'user_id' => auth()->id()])
                ->toMediaCollection($input['field']);
            return $this->sendResponse($input['uuid'], "Uploaded Successfully");
        } catch (ValidatorException $e) {
            return $this->sendResponse(true, $e->getMessage());
        }
    }

    /**
     * clear cache from Upload table
     */
    public function clear(UploadRequest $request)
    {
        $input = $request->all();
        if (!isset($input['uuid'])) {
            return $this->sendResponse(false, 'Media not found');
        }
        try {
            if (is_array($input['uuid'])) {
                $result = $this->uploadRepository->clearWhereIn($input['uuid']);
            } else {
                $result = $this->uploadRepository->clear($input['uuid']);
            }
            return $this->sendResponse($result, 'Media deleted successfully');
        } catch (Exception $e) {
            return $this->sendResponse(false, 'Error when delete media');
        }

    }
}
