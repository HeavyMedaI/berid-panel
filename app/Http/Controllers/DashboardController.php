<?php
/*
 * File name: DashboardController.php
 * Last modified: 2021.08.08 at 12:38:46
 * Author: Musa ATALAY - musaatalay.work@gmail.com
 * Copyright (c) 2022
 */

namespace App\Http\Controllers;

use App\Repositories\AdvertRepository;
use App\Repositories\EarningRepository;
use App\Repositories\EProviderRepository;
use App\Repositories\UserRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Response;
use Illuminate\View\View;

class DashboardController extends Controller
{

    /** @var  AdvertRepository */
    private $advertRepository;


    /**
     * @var UserRepository
     */
    private $userRepository;

    /** @var  EarningRepository */
    private $earningRepository;

    public function __construct(AdvertRepository $advertRepo, UserRepository $userRepo, EarningRepository $earningRepository)
    {
        parent::__construct();
        $this->advertRepository = $advertRepo;
        $this->userRepository = $userRepo;
        $this->earningRepository = $earningRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|Response|View
     */
    public function index()
    {
        $advertsCount = $this->advertRepository->count();
        $membersCount = $this->userRepository->count();
        $earning = $this->earningRepository->all()->sum('total_earning');
        $ajaxEarningUrl = route('payments.byMonth', ['api_token' => auth()->user()->api_token]);
        return view('dashboard.index')
            ->with("ajaxEarningUrl", $ajaxEarningUrl)
            ->with("advertsCount", $advertsCount)
            ->with("membersCount", $membersCount)
            ->with("earning", $earning);
    }
}
