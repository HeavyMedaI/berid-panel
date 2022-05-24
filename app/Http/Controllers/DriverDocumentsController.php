<?php
/*
 * File name: CouponController.php
 * Last modified: 2021.02.05 at 10:52:12
 * Author: Musa ATALAY - musaatalay.work@gmail.com
 * Copyright (c) 2022
 */

namespace App\Http\Controllers;

use App\Criteria\Coupons\CouponsOfUserCriteria;
use App\Criteria\DriverDocuments\DriverDocumentsOfDriverCriteria;
use App\Criteria\EProviders\AcceptedCriteria;
use App\Criteria\EProviders\EProvidersOfUserCriteria;
use App\Criteria\EServices\EServicesOfUserCriteria;
use App\Criteria\Users\CustomersCriteria;
use App\Criteria\Users\DriversCriteria;
use App\DataTables\CouponDataTable;
use App\DataTables\DriverDocumentsDataTable;
use App\Events\DriverDocumentsChangedEvent;
use App\Http\Requests\CreateCouponRequest;
use App\Http\Requests\UpdateCouponRequest;
use App\Http\Requests\UpdateDriverDocumentsRequest;
use App\Notifications\StatusChangedDriverDocuments;
use App\Repositories\CategoryRepository;
use App\Repositories\CouponRepository;
use App\Repositories\CustomFieldRepository;
use App\Repositories\DiscountableRepository;
use App\Repositories\DriverDocumentsRepository;
use App\Repositories\EProviderRepository;
use App\Repositories\EServiceRepository;
use App\Repositories\UserRepository;
use Flash;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Response;
use Illuminate\View\View;
use Prettus\Repository\Exceptions\RepositoryException;
use Prettus\Validator\Exceptions\ValidatorException;

class DriverDocumentsController extends Controller
{
    /** @var  DriverDocumentsRepository */
    private $driverDocumentsRepository;

    /**
     * @var CustomFieldRepository
     */
    private $customFieldRepository;

    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(DriverDocumentsRepository $driverDocumentsRepo, CustomFieldRepository $customFieldRepo, UserRepository $userRepo)
    {
        parent::__construct();
        $this->driverDocumentsRepository = $driverDocumentsRepo;
        $this->customFieldRepository = $customFieldRepo;
        $this->userRepository = $userRepo;
    }

    /**
     * Display a listing of the DriverDocuments.
     *
     * @param DriverDocumentsDataTable $driverDocumentsDataTable
     * @return Response
     */
    public function index(DriverDocumentsDataTable $driverDocumentsDataTable)
    {
        return $driverDocumentsDataTable->render('driver_documents.index');
    }

    /**
     * Display the specified DriverDocuments.
     *
     * @param int $id
     *
     * @return Application|Factory|Response|View
     */
    public function show(int $id)
    {
        $driverDocs = $this->driverDocumentsRepository->findWithoutFail($id);

        if (empty($driverDocs)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.driver_documents')]));

            return redirect(route('driver_documents.index'));
        }

        return view('driver_documents.show')->with('driver_documents', $driverDocs);
    }

    /**
     * Show the form for editing the specified DriverDocuments.
     *
     * @param int $id
     *
     * @return Application|RedirectResponse|Redirector|Response
     * @throws RepositoryException
     */
    public function edit(int $id)
    {
        $driverDocs = $this->driverDocumentsRepository->findWithoutFail($id);

        if (empty($driverDocs)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.driver_documents')]));

            return redirect(route('driver_documents.index'));
        }


        $driver = $this->userRepository->findWithoutFail($driverDocs->user_id);

        return view('driver_documents.edit')
            ->with('driverDocuments', $driverDocs)
            ->with("driver", $driver)
            ->with('licence_front', $driverDocs->getFirstMediaUrl('driver_license_front', ))
            ->with('licence_back', $driverDocs->getFirstMediaUrl('driver_license_back', ))
            ->with('permit', $driverDocs->getFirstMediaUrl('driver_permit', ));
    }

    /**
     * Update the specified DriverDocuments in storage.
     *
     * @param int $id
     * @param UpdateCouponRequest $request
     *
     * @return Application|RedirectResponse|Redirector|Response
     * @throws RepositoryException
     */
    public function update(int $id, UpdateDriverDocumentsRequest $request)
    {

        $oldDriverDocs = $this->driverDocumentsRepository->all()->firstWhere('id', '=', $id);

        if (empty($oldDriverDocs)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.driver_documents')]));
            return redirect(route('driver_documents.index'));
        }
        $input = $request->all();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->driverDocumentsRepository->model());
        try {
            $driverDocs = $this->driverDocumentsRepository->update($input, $id);

            foreach (getCustomFieldsValues($customFields, $request) as $value) {
                $driverDocs->customFieldsValues()
                    ->updateOrCreate(['custom_field_id' => $value['custom_field_id']], $value);
            }
            if (isset($input['status']) && $input['status'] != $oldDriverDocs->status) {
                event(new DriverDocumentsChangedEvent($driverDocs, auth()->user(), $this->userRepository->findWithoutFail($driverDocs->user_id)));
                if (setting('enable_notifications', false)) {
                    Notification::send([$this->userRepository->findWithoutFail($driverDocs->user_id)], new StatusChangedDriverDocuments($driverDocs));
                }
            }
        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.updated_successfully', ['operator' => __('lang.driver_documents')]));

        return redirect(route('driver_documents.index'));
    }

    /**
     * Remove the specified DriverDocuments from storage.
     *
     * @param int $id
     *
     * @return Application|RedirectResponse|Redirector|Response
     */
    public function destroy(int $id)
    {
        $driverDocs = $this->driverDocumentsRepository->findWithoutFail($id);

        if (empty($driverDocs)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.driver_documents')]));

            return redirect(route('driver_documents.index'));
        }

        $this->driverDocumentsRepository->delete($id);

        Flash::success(__('lang.deleted_successfully', ['operator' => __('lang.driver_documents')]));

        return redirect(route('driver_documents.index'));
    }
}
