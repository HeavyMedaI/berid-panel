@extends('layouts.app')
@push('css_lib')
    <link rel="stylesheet" href="{{asset('vendor/icheck-bootstrap/icheck-bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('vendor/select2/css/select2.min.css')}}">
    <link rel="stylesheet" href="{{asset('vendor/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{asset('vendor/summernote/summernote-bs4.min.css')}}">
    <link rel="stylesheet" href="{{asset('vendor/dropzone/min/dropzone.min.css')}}">
    <link rel="stylesheet" href="{{asset('vendor/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css')}}">
@endpush
@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-bold">{{trans('lang.driver_documents_plural') }}<small class="mx-3">|</small><small>{{trans('lang.driver_documents_desc')}}</small></h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb bg-white float-sm-right rounded-pill px-4 py-2 d-none d-md-flex">
                        <li class="breadcrumb-item"><a href="{{url('/dashboard')}}"><i class="fas fa-tachometer-alt"></i> {{trans('lang.dashboard')}}</a></li>
                        <li class="breadcrumb-item"><a href="{!! route('driver_documents.index') !!}">{{trans('lang.driver_documents_plural')}}</a>
                        </li>
                        <li class="breadcrumb-item active">{{trans('lang.driver_documents_edit')}}</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
    <div class="content d-flex flex-column flex-md-row">
        <div class="col-12 col-md-12 col-xl-12">
            @include('flash::message')
            @include('adminlte-templates::common.errors')
        </div>
    </div>
    <div class="content d-flex flex-column flex-md-row">
        <div class="col-12 col-md-8 col-xl-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    <ul class="nav nav-tabs d-flex flex-row align-items-start card-header-tabs">
                        @can('driver_documents.index')
                            <li class="nav-item">
                                <a class="nav-link" href="{!! route('driver_documents.index') !!}"><i class="fas fa-list mr-2"></i>{{trans('lang.driver_documents_table')}}</a>
                            </li>
                        @endcan
                        <li class="nav-item">
                            <a class="nav-link active" href="{!! url()->current() !!}"><i class="fas fa-pencil mr-2"></i>{{trans('lang.driver_documents_edit')}}</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    {!! Form::model($driverDocuments, ['route' => ['driver_documents.update', $driverDocuments->id], 'method' => 'patch']) !!}
                    <div class="row">
                        @include('driver_documents.fields')
                    </div>
                    {!! Form::close() !!}
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4 col-xl-4">
            <div class="card shadow-sm">
                <div class="card-header text-bold">
                    <div class="card-title">
                        {{__('lang.driver')}}
                    </div>
                    <div class="card-tools">
                        <a target="_blank" class="btn btn-sm btn-default ml-xl-auto my-1 my-xl-0" href="{{route('users.edit',$driver->id)}}"><i class="fas fa-user-alt mx-1"></i>{{__('lang.user_profile')}}</a>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex flex-xl-row flex-column justify-content-between align-items-center align-items-xl-start px-0">
                            {!! getMediaColumn($driver,'avatar','img-circle shadow-sm border') !!}
                            <div class="d-flex flex-column align-items-center align-items-xl-start mx-3 my-1 my-xl-0 my-0">
                                <b>{{$driver->name}}</b>
                                <small>{{$driver->email}}</small>
                                <small>{{$driver->phone_number}}</small>
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
                            <b>{{__('lang.is_ripped')}}:</b><small>@if($driverDocuments->is_ripped) {{__('lang.yes')}} @else {{__('lang.no')}} @endif</small>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <b>{{__('lang.car_brand')}}:</b><small>{{$driverDocuments->car_brand}}</small>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <b>{{__('lang.car_model')}}:</b><small>{{$driverDocuments->car_model}}</small>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <b>{{__('lang.car_plate')}}:</b><small>{{$driverDocuments->car_plate}}</small>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <b>{{__('lang.updated_at')}}:</b><small>{!! getDateColumn($driverDocuments, 'updated_at') !!}</small>
                        </li>
                    </ul>
                </div>
            </div>

        </div>
    </div>
    @include('layouts.media_modal')
@endsection
@push('scripts_lib')
    <script src="{{asset('vendor/select2/js/select2.full.min.js')}}"></script>
    <!--<script src="{{asset('vendor/summernote/summernote.min.js')}}"></script>-->
    <script src="{{asset('vendor/dropzone/min/dropzone.min.js')}}"></script>
    <script src="{{asset('vendor/moment/moment.min.js')}}"></script>
    <script src="{{asset('vendor/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js')}}"></script>
    <script type="text/javascript">
        Dropzone.autoDiscover = false;
        var dropzoneFields = [];
    </script>
@endpush
