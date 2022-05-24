<?php
/*
 * File name: AdvertController.php
 * Last modified: 2021.06.09 at 16:09:33
 * Author: Musa ATALAY - musaatalay.work@gmail.com
 * Copyright (c) 2022
 */

namespace App\Http\Controllers;

use App\Criteria\Addresses\AddressesOfUserCriteria;
use App\Criteria\Adverts\AdvertsOfUserCriteria;
use App\Criteria\Bookings\BookingsOfUserCriteria;
use App\DataTables\AdvertDataTable;
use App\DataTables\BookingDataTable;
use App\Events\AdvertChangedEvent;
use App\Events\BookingChangedEvent;
use App\Http\Requests\UpdateBookingRequest;
use App\Notifications\StatusChangedAdvert;
use App\Notifications\StatusChangedBooking;
use App\Repositories\AddressRepository;
use App\Repositories\AdvertRepository;
use App\Repositories\AdvertStatusRepository;
use App\Repositories\BookingRepository;
use App\Repositories\BookingStatusRepository;
use App\Repositories\CouponRepository;
use App\Repositories\CustomFieldRepository;
use App\Repositories\EProviderRepository;
use App\Repositories\EServiceRepository;
use App\Repositories\NotificationRepository;
use App\Repositories\OptionRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\PaymentStatusRepository;
use App\Repositories\TaxRepository;
use App\Repositories\UserRepository;
use Flash;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Response;
use Illuminate\View\View;
use Prettus\Repository\Exceptions\RepositoryException;
use Prettus\Validator\Exceptions\ValidatorException;

class AdvertController extends Controller
{
    /** @var  AdvertRepository */
    private $advertRepository;
    /**
     * @var AdvertStatusRepository
     */
    private $advertStatusRepository;

    /**
     * @var CustomFieldRepository
     */
    private $customFieldRepository;

    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var PaymentRepository
     */
    private $paymentRepository;
    /**
     * @var PaymentStatusRepository
     */
    private $paymentStatusRepository;
    /**
     * @var NotificationRepository
     */
    private $notificationRepository;

    public function __construct(AdvertRepository $advertRepo, AdvertStatusRepository $advertStatusRepo, CustomFieldRepository $customFieldRepo, UserRepository $userRepo
        , PaymentRepository $paymentRepo, PaymentStatusRepository $paymentStatusRepository, NotificationRepository $notificationRepo)
    {
        parent::__construct();
        $this->advertRepository = $advertRepo;
        $this->advertStatusRepository = $advertStatusRepo;
        $this->customFieldRepository = $customFieldRepo;
        $this->userRepository = $userRepo;
        $this->paymentRepository = $paymentRepo;
        $this->paymentStatusRepository = $paymentStatusRepository;
        $this->notificationRepository = $notificationRepo;
    }

    /**
     * Display a listing of the Advert.
     *
     * @param AdvertDataTable $advertDataTable
     * @return Response
     */
    public function index(AdvertDataTable $advertDataTable)
    {
        try {
            return $advertDataTable->render('adverts.index');
        }catch (\Exception $e) {
            var_dump($e->getMessage());
            exit;
        }
    }

    /**
     * Display the specified Advert.
     *
     * @param int $id
     *
     * @return Application|Factory|Response|View
     * @throws RepositoryException
     */
    public function show(int $id)
    {
        $this->advertRepository->pushCriteria(new AdvertsOfUserCriteria(auth()->user()));
        $advert = $this->advertRepository->findWithoutFail($id);
        if (empty($advert)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.advert')]));
            return redirect(route('adverts.index'));
        }
        $advertStatuses = $this->advertStatusRepository->orderBy('order')->all();
        return view('adverts.show')->with('advert', $advert)->with('advertStatuses', $advertStatuses);
    }

    /**
     * Show the form for editing the specified Advert.
     *
     * @param int $id
     *
     * @return Application|RedirectResponse|Redirector|Response
     * @throws RepositoryException
     */
    public function edit(int $id)
    {
        $this->advertRepository->pushCriteria(new AdvertsOfUserCriteria(auth()->id()));
        $advert = $this->advertRepository->findWithoutFail($id);
        if (empty($advert)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.advert')]));
            return redirect(route('adverts.index'));
        }
        array_push($booking->fillable, ['address_id', 'payment_status_id']);
        $advert->address_id = $booking->address->id;
        $advertStatus = $this->advertStatusRepository->orderBy('order')->pluck('status', 'id');
        if (!empty($advert->payment)) {
            $advert->payment->payment_status_id = $booking->payment->payment_status_id;
            $paymentStatuses = $this->paymentStatusRepository->pluck('status', 'id');
        } else {
            $paymentStatuses = null;
        }
        $addresses = $this->addressRepository->getByCriteria(new AddressesOfUserCriteria($booking->user_id))->pluck('address', 'id');

        $customFieldsValues = $booking->customFieldsValues()->with('customField')->get();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->bookingRepository->model());
        $hasCustomField = in_array($this->bookingRepository->model(), setting('custom_field_models', []));
        if ($hasCustomField) {
            $html = generateCustomField($customFields, $customFieldsValues);
        }
        return view('adverts.edit')->with('advert', $booking)->with("customFields", isset($html) ? $html : false)->with("advertStatus", $advertStatus)->with("addresses", $addresses)->with("paymentStatuses", $paymentStatuses);
    }

    /**
     * Update the specified Advert in storage.
     *
     * @param int $id
     * @param UpdateBookingRequest $request
     *
     * @return Application|RedirectResponse|Redirector|Response
     */
    public function update(int $id, UpdateBookingRequest $request)
    {
        $oldAdvert = $this->advertRepository->findWithoutFail($id);
        if (empty($oldAdvert)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.advert')]));
            return redirect(route('adverts.index'));
        }
        $input = $request->all();
        $address = $this->addressRepository->findWithoutFail($input['address_id']);
        $input['address'] = $address;
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->advertRepository->model());
        try {
            $advert = $this->advertRepository->update($input, $id);
            if (isset($input['payment_status_id'])) {
                $this->paymentRepository->update(
                    ['payment_status_id' => $input['payment_status_id']],
                    $advert->payment->id
                );
                event(new AdvertChangedEvent($advert, $advert->user, $advert->driver));
            }
            if (setting('enable_notifications', false)) {
                if (isset($input['advert_status_id']) && $input['advert_status_id'] != $oldAdvert->status->id) {
                    if ($advert->status->order < 40) {
                        Notification::send([$advert->user], new StatusChangedAdvert($advert));
                    } else {
                        Notification::send([$advert->user, $advert->driver], new StatusChangedAdvert($advert));
                    }
                }
            }
            foreach (getCustomFieldsValues($customFields, $request) as $value) {
                $advert->customFieldsValues()
                    ->updateOrCreate(['custom_field_id' => $value['custom_field_id']], $value);
            }
        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }
        Flash::success(__('lang.updated_successfully', ['operator' => __('lang.advert')]));
        return redirect(route('adverts.index'));
    }

    /**
     * Remove the specified Advert from storage.
     *
     * @param int $id
     *
     * @return Application|RedirectResponse|Redirector|Response
     */
    public function destroy($id)
    {
        $this->advertRepository->pushCriteria(new AdvertsOfUserCriteria(auth()->id()));
        $advert = $this->advertRepository->findWithoutFail($id);

        if (empty($advert)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.booking')]));

            return redirect(route('adverts.index'));
        }

        $this->advertRepository->delete($id);

        Flash::success(__('lang.deleted_successfully', ['operator' => __('lang.advert')]));

        return redirect(route('adverts.index'));
    }

}
