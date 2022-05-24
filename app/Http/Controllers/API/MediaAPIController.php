<?php
/*
 * File name: CategoryAPIController.php
 * Last modified: 2021.03.24 at 21:33:26
 * Author: Musa ATALAY - musaatalay.work@gmail.com
 * Copyright (c) 2022
 */

namespace App\Http\Controllers\API;


use App\Criteria\Categories\NearCriteria;
use App\Criteria\Categories\ParentCriteria;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateAdvertRequest;
use App\Http\Requests\UpdateAdvertRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Advert;
use App\Models\Category;
use App\Models\User;
use App\Repositories\AdvertRepository;
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
 * Class CategoryController
 * @package App\Http\Controllers\API
 */
class MediaAPIController extends Controller
{
    private $uploadRepository;

    public function __construct(UploadRepository $uploadRepository)
    {
        $this->uploadRepository = $uploadRepository;
    }

    /**
     * Display a listing of the Category.
     * GET|HEAD /categories
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        try{
        } catch (RepositoryException $e) {
            return $this->sendError($e->getMessage());
        }
        $adverts = $this->advertRepository->all();

        return $this->sendResponse($adverts->toArray(), 'Adverts retrieved successfully');
    }

    /**
     * Display the specified Advert.
     * GET|HEAD /adverts/{id}
     *
     * @param int $id
     *
     * @return JsonResponse
     */
    public function show($id)
    {
        /** @var Advert $advert */
        if (!empty($this->advertRepository)) {
            $advert = $this->advertRepository->findWithoutFail($id);
        }
        unset($advert->user_id);
        unset($advert->updated_at);
        unset($advert->updated_at);

        if (empty($advert)) {
            return $this->sendError('Advert not found');
        }

        return $this->sendResponse($advert->toArray(), 'Advert retrieved successfully');
    }

    /**
     * Create a new advert instance
     *
     * @param array $data
     * @return
     */
    function store(CreateAdvertRequest $request)
    {
        try {
            //$this->validate($request, Advert::$rules);
            $request->validate($request->rules());

            $input = $request->post();

            //var_dump($request); exit;

            $images = $input["images"];
            unset($input["images"]);

            $advert = new Advert();
            $advert->user_id = auth()->id();
            $advert->updated_at = date("Y-m-d H:i:s");
            foreach ($input as $key => $value) {
                $advert->$key = $value;
            }
            $advert->save();

            if (!is_null($images) && is_array($images) && count($images) > 0) {
                foreach ($images as $m_index => $media) {
                    if (is_null($media) || $media == "null" || strpos($media, "id") === false) {
                        continue;
                    }
                    $media = json_decode($media, true);
                    $cacheUpload = $this->uploadRepository->getByUuid($media["id"]);
                    $mediaItem = $cacheUpload->getMedia('advert_media')->first();
                    $mediaItem->copy($advert, 'advert_media');
                    //$mediaItem->delete();
                }
            }
        } catch (ValidationException $e) {
            return $this->sendError(array_values($e->errors()));
        } catch (Exception $e) {
            return $this->sendError($e->getMessage(), 200);
        }

        //$advert->media = $images;
        $advert->medias = gettype($images);


        return $this->sendResponse($advert, 'Advert stored successfully');
    }

    /**
     * Update the specified User in storage.
     *
     * @param int $id
     * @param UpdateAdvertRequest $request
     *
     * @return Application|RedirectResponse|Redirector|Response
     */
    public function update(int $id, UpdateAdvertRequest $request)
    {
        $advert = $this->advertRepository->findWithoutFail($id);
        if (empty($advert)) {
            return $this->sendError(__('lang.not_found', ['operator' => __('lang.user')]), 200);
        }

        if ($advert->user_id != auth()->id()) {
            return $this->sendError('Permission denied', 200);
        }

        $input = $request->all();

        try {
            if (isset($input['media']) && $input['media']) {
                foreach ($input["media"] as $media) {
                    $cacheUpload = $this->uploadRepository->getByUuid($media);
                    $mediaItem = $cacheUpload->getMedia('media')->first();
                    $mediaItem->copy($advert, 'advert_media');
                    //$mediaItem->delete();
                }
            }

            $advert = $this->advertRepository->update($input, $id);
        } catch (ValidatorException $e) {
            return $this->sendError($e->getMessage(), 200);
        }


        return $this->sendResponse($advert, 'Advert updated successfully');

    }
}
