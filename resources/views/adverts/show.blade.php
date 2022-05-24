@extends('layouts.app')
@push('css_lib')
    <link rel="stylesheet" href="{{asset('vendor/bs-stepper/css/bs-stepper.min.css')}}">
@endpush
@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-md-6">
                    <h1 class="m-0 text-bold">{{trans('lang.advert_plural')}} <small class="mx-3">|</small><small>{{trans('lang.advert_desc')}}</small></h1>
                </div><!-- /.col -->
                <div class="col-md-6">
                    <ol class="breadcrumb bg-white float-sm-right rounded-pill px-4 py-2 d-none d-md-flex">
                        <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="fas fa-tachometer-alt"></i> {{trans('lang.dashboard')}}</a></li>
                        <li class="breadcrumb-item active"><a href="{!! route('adverts.index') !!}">{{trans('lang.advert_plural')}}</a>
                        </li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
    <div class="content d-flex flex-column flex-md-row">
        <div class="col-12 col-md-8 col-xl-9">
            <div class="card shadow-sm">
                <div class="card-header">
                    <ul class="nav nav-tabs align-items-end card-header-tabs w-100">
                        <li class="nav-item">
                            <a class="nav-link" href="{!! route('adverts.index') !!}"><i class="fa fa-list mr-2"></i>{{trans('lang.advert_table')}}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="{!! route('adverts.show',$advert->id) !!}"><i class="fas fa-calendar-check mr-2"></i>{{trans('lang.advert_details')}}
                            </a>
                        </li>
                        {{--@can('adverts.edit')
                            <li class="nav-item">
                                <a class="nav-link" href="{!! route('adverts.edit',$advert->id) !!}"><i class="fas fa-edit mr-2"></i>{{trans('lang.advert_edit')}}
                                </a>
                            </li>
                        @endcan--}}
                    </ul>
                </div>
                <div class="card-body p-0">

                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center bg-light">
                            <b>{{__('lang.advert_status')}}</b>
                            @if($advert->cancel)
                                <span class="badge bg-danger px-2 py-2">{{__('lang.advert_cancel')}}</span>
                            @endif
                        </li>
                        <li class="bs-stepper list-group-item" style="overflow: scroll;overflow-x: auto;overflow-y: hidden;">
                            <div class="bs-stepper-header" role="tablist">
                                @foreach($advertStatuses as $advertStatus)
                                    <div class="step @if($advertStatus->id == $advert->status->id) {{'active'}} @endif">
                                        <span role="tab @if($advertStatus->id == $advert->status->id) {{'active'}} @endif">
                                            <span class="bs-stepper-circle @if($advertStatus->id == $advert->status->id) bg-{{setting('theme_color')}} @endif">{{$advertStatus->order}}</span>
                                            <span class="bs-stepper-label">{{__('status.advert.' . $advertStatus->status)}}</span> </span>
                                    </div>
                                    @if (!$loop->last)
                                        <div class="line"></div>
                                    @endif
                                @endforeach
                            </div>
                        </li>
                        <li class="list-group-item bg-light">
                            <b>{{__('lang.advert_ref')}} #{{$advert->ref_code}}</b>
                        </li>
                        @if(isset($advert->driver) && !empty($advert->driver))
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {!! getMediaColumn($advert->driver,'advert_media','rounded shadow-sm border') !!}
                                <div class="d-flex flex-column mx-3">
                                    <small>{{__('lang.driver')}}:</small>
                                    <span><b>{{$advert->driver->name}}</b><small class="mx-3">{{$advert->driver->name}}</small></span>
                                </div>
                                <div class="d-flex ml-xl-auto flex-column mx-3">
                                    <small>{{__('lang.receiver')}}:</small>
                                    <span><b>{{ ucwords($advert->receiver_full_name) }}</b>, <small>{{__('lang.user_phone_number')}}: <a href="tel:{{ $advert->receiver_phone }}">{{ $advert->receiver_phone }}</a></small></span>
                                </div>
                                <div class="d-flex ml-xl-auto flex-column mx-3">
                                    @php
                                        $drop_city = json_decode($advert->city_drop_off);
                                        $drop_coords = get_location($drop_city);
                                        $drop_address = get_political_address($drop_city->address_components);
                                    @endphp
                                    <small>{{__('lang.drop_address')}}:</small>
                                    <span>{!! $drop_city->formatted_address !!}</span>
                                </div>
                                <div class="text-bold ml-xl-auto my-1 my-xl-0">
                                    <a target="_blank" class="btn btn-sm btn-default" href="{{'https://www.google.com/maps/@'.$drop_coords->latitude .','.$drop_coords->longitude.',14z'}}"><i class="fas fa-directions mx-1"></i>{{__('lang.address_google_maps')}}</a>
                                </div>
                            </li>
                        @endif
                    </ul>

                    <div class="d-flex flex-column flex-md-row">
                        <ul class="list-group list-group-flush col-12 col-lg-12 p-0">
                            <span class="list-group-item py-0"></span>
                            <li class="list-group-item bg-light">
                                <b>{{__('lang.payment')}}</b>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <b>{{__('lang.price')}}</b>
                                <small class="badge badge-light px-2 py-1">{{number_format($advert->price, 2)}} {{setting('default_currency')}}</small>
                            </li>
                            @if(isset($advert->payment) && !empty($advert->payment))
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <b>{{__('lang.payment_status')}}</b>
                                <small class="badge badge-light px-2 py-1">{{empty(!$advert->payment) ? __('status.payment.' . $advert->payment->paymentStatus->status) : '-'}}</small>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <b>{{__('lang.payment_method')}}</b>
                                <small class="badge badge-light px-2 py-1">{{empty(!$advert->payment) ? $advert->payment->paymentMethod->name : '-'}}</small>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <b>{{__('lang.payment_description')}}</b> <small>{{$advert->payment->description}}</small>
                            </li>
                            @else
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <small>{{__('lang.awaiting_payment')}}</small>
                                </li>
                            @endif
                        </ul>
                    </div>

                    <!-- Back Field -->
                    <div class="form-group col-12 d-flex flex-column flex-md-row justify-content-md-end justify-content-sm-center border-top pt-4">
                        <a href="#" class="btn btn-default mx-md-2 my-md-0 my-2"> <i class="fas fa-print"></i> {{trans('lang.print')}}
                        </a>
                        <!--<a href="{!! route('adverts.edit', $advert->id) !!}" class="btn btn-default mx-md-2">
                            <i class="fas fa-edit"></i> {{trans('lang.edit')}}
                        </a> -->
                        <a href="{!! route('adverts.index') !!}" class="btn btn-default mx-md-2">
                            <i class="fas fa-list"></i> {{trans('lang.advert_table')}}
                        </a>

                    </div>

                </div>
            </div>
        </div>
        <div class="col-12 col-md-4 col-xl-3">
            <div class="card shadow-sm">
                <div class="card-header text-bold">
                    <div class="card-title">
                        {{__('lang.user')}}
                    </div>
                    <div class="card-tools">
                        <a target="_blank" class="btn btn-sm btn-default ml-xl-auto my-1 my-xl-0" href="{{route('users.edit',$advert->user->id)}}"><i class="fas fa-user-alt mx-1"></i>{{__('lang.user_profile')}}</a>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex flex-xl-row flex-column justify-content-between align-items-center align-items-xl-start px-0">
                            {!! getMediaColumn($advert->user,'avatar','img-circle shadow-sm border') !!}
                            <div class="d-flex flex-column align-items-center align-items-xl-start mx-3 my-1 my-xl-0 my-0">
                                <b>{{$advert->user->name}}</b>
                                <small>{{$advert->user->email}}</small>
                                <small>{{$advert->user->phone_number}}</small>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-header text-bold">
                    {{__('lang.meta_data')}}
                </div>
                <div class="card-body px-0 py-1">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <b>{{__('lang.is_urgent')}}:</b><small>@if($advert->is_urgent) {{__('lang.yes')}} @else {{__('lang.no')}} @endif</small>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <b>{{__('lang.title')}}:</b><small>{{$advert->title}}</small>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <small>{{$advert->description}}</small>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @php
                                $advertSizes = ["", "Zarf", "Koltuğa Sığar", "Bagaja Sığar"];
                            @endphp
                            <b>{{__('lang.size')}}:</b><small>{{$advertSizes[$advert->size]}}</small>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <b>{{__('lang.quantity')}}:</b><small>{{$advert->quantity}} {{__('lang.quantity')}}</small>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <b>{{__('lang.created_at')}}:</b> <small>{!! getDateColumn($advert, 'created_at') !!}</small>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <b>{{__('lang.updated_at')}}:</b> <small>{!! getDateColumn($advert, 'updated_at') !!}</small>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <b>{{__('lang.date.pickup')}}:</b> <small>{{ date("d.m.Y", strtotime($advert->date_pick_up)) }}</small>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <b>{{__('lang.date.dropoff')}}:</b> <small>{{ date("d.m.Y", strtotime($advert->date_drop_off)) }}</small>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-header text-bold">
                    {{__('lang.pickup_address')}}
                </div>
                @php
                    $pickup_city = json_decode($advert->city_pick_up);
                    $pickup_coords = get_location($pickup_city);
                    $pickup_address = get_political_address($pickup_city->address_components);
                @endphp
                <div class="card-body  px-0 py-1">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <small>{{$pickup_address}}</small>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <small>{{$advert->place_drop_off}}</small>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <b>{{__('lang.address_open_with')}}</b>
                            <a target="_blank" class="btn btn-sm btn-default" href="{{'https://www.google.com/maps/@'.$pickup_coords->latitude .','.$pickup_coords->longitude.',14z'}}"><i class="fas fa-directions mx-1"></i>{{__('lang.address_google_maps')}}
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

        </div>

    </div>
    <!-- /.modal -->
@endsection
@push('scripts_lib')
    {{--    <script src="{{asset('vendor/bs-stepper/js/bs-stepper.min.js')}}"></script>--}}
    <script type="application/javascript">
        $(function (global) {
            $(function (global) {
                $('.bs-stepper').animate({
                    scrollLeft: $('.bs-stepper .step.active').offset().left - $('.bs-stepper').offset().left
                }, 2000);
            });
        });
    </script>
@endpush
