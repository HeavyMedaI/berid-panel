<?php
/*
 * File name: ParentPaymentController.php
 * Last modified: 2021.06.09 at 17:20:26
 * Author: Musa ATALAY - musaatalay.work@gmail.com
 * Copyright (c) 2022
 */

namespace App\Http\Controllers;

use App\Events\AdvertChangedEvent;
use App\Models\Advert;
use App\Models\Booking;
use App\Notifications\NewAdvert;
use App\Repositories\AdvertRepository;
use App\Repositories\AdvertStatusRepository;
use App\Repositories\BookingRepository;
use App\Repositories\NotificationRepository;
use App\Repositories\PaymentMethodRepository;
use App\Repositories\PaymentRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Prettus\Validator\Exceptions\ValidatorException;

abstract class ParentPaymentController extends Controller
{
    /** @var  AdvertRepository */
    protected $advertRepository;
    /** @var  AdvertStatusRepository */
    protected $advertStatusRepository;
    /** @var  PaymentRepository */
    protected $paymentRepository;
    /** @var  PaymentMethodRepository */
    protected $paymentMethodRepository;
    /** @var  NotificationRepository */
    protected $notificationRepository;
    /** @var Advert */
    protected $advert;
    /** @var int */
    protected $paymentMethodId;

    /**
     * @param AdvertRepository $advertRepo
     * @param PaymentRepository $paymentRepo
     * @param NotificationRepository $notificationRepo
     */
    public function __construct(AdvertRepository $advertRepo, AdvertStatusRepository $advertStatusRepo, PaymentRepository $paymentRepo, PaymentMethodRepository $paymentMethodRepo, NotificationRepository $notificationRepo)
    {
        parent::__construct();
        $this->advertRepository = $advertRepo;
        $this->advertStatusRepository = $advertStatusRepo;
        $this->paymentRepository = $paymentRepo;
        $this->paymentMethodRepository = $paymentMethodRepo;
        $this->notificationRepository = $notificationRepo;
        $this->advert = new Advert();

        $this->__init();
    }

    abstract public function __init();

    protected function startTransaction()
    {
        try {
            $payment = $this->createPayment();
            if ($payment != null) {
                #$this->advertRepository->update(['payment' => $payment], $this->advert->id);
                $this->advert->payment = $payment;
                $this->advert->save();
                event(new AdvertChangedEvent($this->advert->user, $this->advert->driver));
                $this->sendNotificationToUsers();
            }
        } catch (ValidatorException $e) {
            Log::error($e->getMessage());
        }
    }

    /**
     * @return mixed
     * @throws ValidatorException
     */
    protected function createPayment()
    {
        if ($this->advert != null && $this->paymentMethodId != null) {
            $input['amount'] = $this->advert->getTotal();
            $input['description'] = trans("lang.pending");
            $input['payment_status_id'] = 1;
            $input['payment_method_id'] = $this->paymentMethodId;
            $input['user_id'] = $this->advert->user->id;
            try {
                return $this->paymentRepository->create($input);
            } catch (ValidatorException $e) {
                Log::error($e->getMessage());
            }
        }
        return null;
    }

    protected function sendNotificationToUsers()
    {
        //Notification::send($this->advert->user, new NewAdvert($this->advert));
    }

}
