<?php
/*
 * File name: UpdateAdvertEarningTable.php
 * Last modified: 2021.06.10 at 20:37:20
 * Author: Musa ATALAY - musaatalay.work@gmail.com
 * Copyright (c) 2022
 */

namespace App\Listeners;

use App\Criteria\Adverts\AdvertsOfDriverCriteria;
use App\Criteria\Bookings\BookingsOfProviderCriteria;
use App\Criteria\Bookings\PaidAdvertsCriteria;
use App\Criteria\Bookings\PaidBookingsCriteria;
use App\Repositories\AdvertRepository;
use App\Repositories\BookingRepository;
use App\Repositories\EarningRepository;
use Prettus\Repository\Exceptions\RepositoryException;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * Class UpdateAdvertEarningTable
 * @package App\Listeners
 */
class UpdateAdvertEarningTable
{
    /**
     * @var EarningRepository
     */
    private $earningRepository;

    /**
     * @var AdvertRepository
     */
    private $advertRepository;

    /**
     * Create the event listener.
     *
     * @param EarningRepository $earningRepository
     * @param AdvertRepository  $advertRepository
     */
    public function __construct(EarningRepository $earningRepository, AdvertRepository $advertRepository)
    {
        $this->earningRepository = $earningRepository;
        $this->advertRepository = $advertRepository;
    }

    /**
     * Handle the event.
     * oldAdvert
     * updatedAdvert
     * @param object $event
     * @return void
     */
    public function handle($event)
    {
        try {
            $this->advertRepository->pushCriteria(new AdvertsOfDriverCriteria($event->driver->id));
            $this->advertRepository->pushCriteria(new PaidAdvertsCriteria());
            $adverts = $this->advertRepository->all();
            $advertsCount = $adverts->count();

            $advertsTotals = $adverts->map(function ($advert) {
                return $advert->getTotal();
            })->toArray();

            $advertsTaxes = $adverts->map(function ($advert) {
                return $advert->getTaxesValue();
            })->toArray();

            $total = array_reduce($advertsTotals, function ($total1, $total2) {
                return $total1 + $total2;
            }, 0);

            $tax = array_reduce($advertsTaxes, function ($tax1, $tax2) {
                return $tax1 + $tax2;
            }, 0);
            $this->earningRepository->updateOrCreate(['e_provider_id' => $event->eProvider->id], [
                    'total_bookings' => $advertsCount,
                    'total_earning' => $total - $tax,
                    'taxes' => $tax,
                    'admin_earning' => ($total - $tax) * (100 - $event->eProvider->eProviderType->commission) / 100,
                    'e_provider_earning' => ($total - $tax) * $event->eProvider->eProviderType->commission / 100,
                ]
            );
        } catch (ValidatorException | RepositoryException $e) {
        } finally {
            $this->advertRepository->resetCriteria();
        }
    }
}
