<?php
/*
 * File name: AdvertStatusController.php
 * Last modified: 2021.01.25 at 22:00:21
 * Author: Musa ATALAY - musaatalay.work@gmail.com
 * Copyright (c) 2022
 */

namespace App\Http\Controllers;

use App\DataTables\AdvertStatusDataTable;
use App\Http\Requests\CreateAdvertStatusRequest;
use App\Http\Requests\UpdateAdvertStatusRequest;
use App\Repositories\AdvertStatusRepository;
use App\Repositories\CustomFieldRepository;
use Exception;
use Flash;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\View\View;
use Prettus\Validator\Exceptions\ValidatorException;

class AdvertStatusController extends Controller
{
    /** @var  AdvertStatusRepository */
    private $advertStatusRepository;

    /**
     * @var CustomFieldRepository
     */
    private $customFieldRepository;



    public function __construct(AdvertStatusRepository $advertStatusRepo, CustomFieldRepository $customFieldRepo )
    {
        parent::__construct();
        $this->advertStatusRepository = $advertStatusRepo;
        $this->customFieldRepository = $customFieldRepo;

    }

    /**
     * Display a listing of the AdvertStatus.
     *
     * @param AdvertStatusDataTable $advertStatusDataTable
     * @return Response
     */
    public function index(AdvertStatusDataTable $advertStatusDataTable)
    {
        return $advertStatusDataTable->render('advert_statuses.index');
    }

    /**
     * Show the form for creating a new AdvertStatus.
     *
     * @return Application|Factory|Response|View
     */
    public function create()
    {


        $hasCustomField = in_array($this->advertStatusRepository->model(),setting('custom_field_models',[]));
            if($hasCustomField){
                $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->advertStatusRepository->model());
                $html = generateCustomField($customFields);
            }
        return view('advert_statuses.create')->with("customFields", isset($html) ? $html : false);
    }

    /**
     * Store a newly created AdvertStatus in storage.
     *
     * @param CreateAdvertStatusRequest $request
     *
     * @return Application|RedirectResponse|Redirector|Response
     */
    public function store(CreateAdvertStatusRequest $request)
    {
        $input = $request->all();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->advertStatusRepository->model());
        try {
            $advertStatus = $this->advertStatusRepository->create($input);
            $advertStatus->customFieldsValues()->createMany(getCustomFieldsValues($customFields,$request));

        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.saved_successfully',['operator' => __('lang.advert_status')]));

        return redirect(route('advertStatuses.index'));
    }

    /**
     * Display the specified AdvertStatus.
     *
     * @param int $id
     *
     * @return Application|Factory|Response|View
     */
    public function show($id)
    {
        $advertStatus = $this->advertStatusRepository->findWithoutFail($id);

        if (empty($advertStatus)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.advert_status')]));
            return redirect(route('advertStatuses.index'));
        }
        return view('advert_statuses.show')->with('advertStatus', $advertStatus);
    }

    /**
     * Show the form for editing the specified AdvertStatus.
     *
     * @param int $id
     *
     * @return Application|RedirectResponse|Redirector|Response
     */
    public function edit($id)
    {
        $advertStatus = $this->advertStatusRepository->findWithoutFail($id);


        if (empty($advertStatus)) {
            Flash::error(__('lang.not_found',['operator' => __('lang.advert_status')]));

            return redirect(route('advertStatuses.index'));
        }
        $customFieldsValues = $advertStatus->customFieldsValues()->with('customField')->get();
        $customFields =  $this->customFieldRepository->findByField('custom_field_model', $this->advertStatusRepository->model());
        $hasCustomField = in_array($this->advertStatusRepository->model(),setting('custom_field_models',[]));
        if($hasCustomField) {
            $html = generateCustomField($customFields, $customFieldsValues);
        }
        return view('advert_statuses.edit')->with('advertStatus', $advertStatus)->with("customFields", isset($html) ? $html : false);
    }

    /**
     * Update the specified AdvertStatus in storage.
     *
     * @param int $id
     * @param UpdateAdvertStatusRequest $request
     *
     * @return Application|RedirectResponse|Redirector|Response
     */
    public function update($id, UpdateAdvertStatusRequest $request)
    {
        $advertStatus = $this->advertStatusRepository->findWithoutFail($id);

        if (empty($advertStatus)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.advert_status')]));
            return redirect(route('advertStatuses.index'));
        }
        $input = $request->all();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->advertStatusRepository->model());
        try {
            $advertStatus = $this->advertStatusRepository->update($input, $id);


            foreach (getCustomFieldsValues($customFields, $request) as $value){
                $advertStatus->customFieldsValues()
                    ->updateOrCreate(['custom_field_id'=>$value['custom_field_id']],$value);
            }
        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }
        Flash::success(__('lang.updated_successfully',['operator' => __('lang.advert_status')]));
        return redirect(route('advertStatuses.index'));
    }

    /**
     * Remove the specified AdvertStatus from storage.
     *
     * @param int $id
     *
     * @return Application|RedirectResponse|Redirector|Response
     */
    public function destroy($id)
    {
        $advertStatus = $this->advertStatusRepository->findWithoutFail($id);

        if (empty($advertStatus)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.advert_status')]));

            return redirect(route('advertStatuses.index'));
        }

        $this->advertStatusRepository->delete($id);

        Flash::success(__('lang.deleted_successfully',['operator' => __('lang.advert_status')]));
        return redirect(route('advertStatuses.index'));
    }

        /**
     * Remove Media of AdvertStatus
     * @param Request $request
     */
    public function removeMedia(Request $request)
    {
        $input = $request->all();
        $advertStatus = $this->advertStatusRepository->findWithoutFail($input['id']);
        try {
            if ($advertStatus->hasMedia($input['collection'])) {
                $advertStatus->getFirstMedia($input['collection'])->delete();
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }

}
