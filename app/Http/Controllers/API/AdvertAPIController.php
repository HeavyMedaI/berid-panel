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
use App\Http\Requests\CreateAdvertRequest;
use App\Http\Requests\UpdateAdvertRequest;
use App\Models\Advert;
use App\Models\Payment;
use App\Repositories\AdvertRepository;
use App\Repositories\AdvertStatusRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\UploadRepository;
use App\Repositories\UserRepository;
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
 * Class AdvertAPIController
 * @package App\Http\Controllers\API
 */
class AdvertAPIController extends Controller
{
    /** @var  AdvertRepository */
    private $advertRepository;

    /** @var  UserRepository */
    private $userRepository;

    /** @var  AdvertStatusRepository */
    private $advertStatusRepository;

    /** @var  PaymentRepository */
    private $paymentRepository;

    private $uploadRepository;

    public function __construct(AdvertRepository $advertRepo, UploadRepository $uploadRepo, UserRepository $userRepo, AdvertStatusRepository $advertStatusRepo, PaymentRepository $paymentRepo)
    {
        $this->advertRepository = $advertRepo;
        $this->uploadRepository = $uploadRepo;
        $this->userRepository = $userRepo;
        $this->advertStatusRepository = $advertStatusRepo;
        $this->paymentRepository = $paymentRepo;
    }

    /**
     * Display a listing of the Advert.
     * GET|HEAD /adverts
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        //var_dump($_GET);
        //exit;
        try{
            $this->advertRepository->pushCriteria(new RequestCriteria($request));
            $this->advertRepository->pushCriteria(new ParentCriteria($request));
            $this->advertRepository->pushCriteria(new NearCriteria($request));
            $this->advertRepository->pushCriteria(new LimitOffsetCriteria($request));
            $this->advertRepository->pushCriteria(new AdvertsOfUserCriteria(auth()->user()));
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
        unset($advert->user);
        unset($advert->created_at);
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

            $input["ref_code"] = $this->generateUniqueCode();

            //var_dump($request); exit;

            $images = $input["images"];
            unset($input["images"]);

            $advert = new Advert();
            $advert->user = auth()->user();
            $advert->updated_at = date("Y-m-d H:i:s");
            foreach ($input as $key => $value) {
                if ($value == null) {
                    #$advert->$key = $value;
                }else{
                    switch ($key) {
                        case "receiver_fullName":
                            $advert->$key = Str::ucfirst($value);
                            break;
                        case "user":
                            break;
                        case "driver":
                            $advert->$key = $this->userRepository->findWithoutFail($value);
                            break;
                        case "status":
                            $advert->$key = $this->advertStatusRepository->findWithoutFail($value);
                            break;
                        case "payment":
                            $advert->$key = $this->paymentRepository->findWithoutFail($value);
                            break;
                        default:
                            $advert->$key = $value;
                            break;
                    }
                }
            }

            #var_dump("user__id:: " . $advert->user->id);
            $advert->save();

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
                    $mediaItem = $cacheUpload->getMedia('advert_media')->first();
                    $mediaItem->copy($advert, 'advert_media');
                    //$mediaItem->delete();
                }
            }
        } catch (ValidationException $e) {
            return $this->sendError(array_values($e->getMessage()));
        }
        catch (\InvalidArgumentException $e){
            var_dump("here inv arg exc::");
            return $this->sendError($e->getMessage(), 200);
        }
        catch (Exception $e) {
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
            return $this->sendError(__('lang.not_found', ['operator' => __('lang.advert')]), 200);
        }

        if ($advert->user->id != auth()->id()) {
            return $this->sendError('Permission denied', 200);
        }

        $input = $request->all();

        $images = $input["images"];
        unset($input["images"]);
        unset($input["user"]);
        unset($input["driver"]);
        unset($input["api_token"]);

        try {
            /*if (isset($input['media']) && $input['media']) {
                foreach ($input["media"] as $media) {
                    $cacheUpload = $this->uploadRepository->getByUuid($media);
                    $mediaItem = $cacheUpload->getMedia('media')->first();
                    $mediaItem->copy($advert, 'advert_media');
                    //$mediaItem->delete();
                }
            }*/

            $advert->updated_at = date("Y-m-d H:i:s");
            foreach ($input as $key => $value) {
                if ($value == null) {
                    $advert->$key = $value;
                }else{
                    switch ($key) {
                        case "status":
                            $advert->$key = $this->advertStatusRepository->findWithoutFail($value);
                            break;
                        case "payment":
                            if ($advert->$key != null) {
                                $advert->$key = $this->paymentRepository->findWithoutFail($value);
                            }else{
                                $advert->$key = new Payment();
                            }
                            break;
                        default:
                            $advert->$key = $value;
                            break;
                    }
                }
            }
            $advert->save();

            #$advert = $this->advertRepository->update($input, $id);

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
                    $mediaItem = $cacheUpload->getMedia('advert_media')->first();
                    $mediaItem->copy($advert, 'advert_media');
                    //$mediaItem->delete();
                }
            }
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 200);
        }


        return $this->sendResponse($advert, 'Advert updated successfully');

    }

    /**
     * Remove the specified Advert from storage.
     *
     * @param int $id
     *
     * @return JsonResponse
     */
    public function destroy($id)
    {
        try {
            /*$advert = $this->advertRepository->findWithoutFail($id);
            if (empty($advert)) {
                return $this->sendError(__('lang.not_found', ['operator' => __('lang.user')]), 200);
            }

            if ($advert->user_id != auth()->id()) {
                return $this->sendError('Permission denied' . ", user_id: " . $advert->user_id . "auth_id: " . auth()->id(), 200);
            }*/

            $this->advertRepository->pushCriteria(new AdvertsOfUserCriteria(auth()->user()));
            $delete = $this->advertRepository->deleteWhere(["id" => $id]) > 0;
            return $this->sendResponse($delete, __('lang.deleted_successfully', ['operator' => __('lang.advert')]));
        } catch (Exception $e) {
            return $this->sendError('Advert not found');
        }
    }

    /**
     *
     * @return integer
     */
    private function generateUniqueCode():int
    {
        do {
            $referal_code = random_int(000000001, 999999999);
        } while (Advert::where("ref_code", "=", $referal_code)->first());

        return $referal_code;
    }
}
