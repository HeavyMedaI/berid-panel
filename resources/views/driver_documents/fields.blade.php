@if(isset($customFields))
    <h5 class="col-12 pb-4">{!! trans('lang.main_fields') !!}</h5>
@endif
<div class="d-flex flex-column col-sm-12 col-md-12">

    <!-- Discount Type Field -->
    <div class="form-group align-items-baseline d-flex flex-column flex-md-row">
        {!! Form::label('status', trans("lang.status"),['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
        <div class="col-md-9">
            {!! Form::select('status', [trans('status.driver_docs.pending_confirmation'), trans('status.driver_docs.confirmed'), trans('status.driver_docs.not_fit')], null, ['class' => 'select2 form-control']) !!}
            <div class="form-text text-muted">{{ trans("lang.select_status") }}</div>
        </div>
    </div>

    <!-- Description Field -->
    <div class="form-group align-items-baseline d-flex flex-column flex-md-row">
        {!! Form::label('status_description', trans("lang.status_description"), ['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
        <div class="col-md-9">
            {!! Form::textarea('status_description', null, ['class' => 'form-control','placeholder'=>
             trans("lang.status_description_placeholder"), 'rows' => 3  ]) !!}
            <div class="form-text text-muted">{{ trans("lang.status_description_help") }}</div>
        </div>
    </div>
    <div style="margin-top: 15px;margin-bottom: 30px;" class="dropdown-divider"></div>
    <div class="form-group align-items-baseline d-flex flex-column flex-md-row">
        <div class="d-flex flex-column col-sm-12 col-md-4">
            {!! Form::label('driver_licence_front', trans("lang.driver_licence_front"), ['class' => 'control-label mx-1']) !!}
            <a href="{!! $licence_front !!}" target="_blank"><img style="width: 100%;" src="{!! $licence_front !!}" alt="{{ trans("lang.driver_licence_front") }}"></a>
        </div>
        <div class="d-flex flex-column col-sm-12 col-md-4">
            {!! Form::label('driver_licence_front', trans("lang.driver_licence_back"), ['class' => 'control-label mx-1']) !!}
            <a href="{!! $licence_back !!}" target="_blank"><img style="width: 100%;" src="{!! $licence_back !!}" alt="{{ trans("lang.driver_licence_back") }}"></a>
        </div>
        <div class="d-flex flex-column col-sm-12 col-md-4">
            {!! Form::label('driver_licence_front', trans("lang.driver_permit"), ['class' => 'control-label mx-1']) !!}
            <a href="{!! $permit !!}" target="_blank"><img style="width: 100%;" src="{!! $permit !!}" alt="{{ trans("lang.driver_permit") }}"></a>
        </div>
    </div>

</div>
@if(isset($customFields))
    <div class="clearfix"></div>
    <div class="col-12 custom-field-container">
        <h5 class="col-12 pb-4">{!! trans('lang.custom_field_plural') !!}</h5>
        {!! $customFields !!}
    </div>
@endif
<!-- Submit Field -->
<div class="form-group col-12 d-flex flex-column flex-md-row justify-content-md-end justify-content-sm-center border-top pt-4">
    <button type="submit" class="btn bg-{{setting('theme_color')}} mx-md-3 my-lg-0 my-xl-0 my-md-0 my-2">
        <i class="fas fa-save"></i> {{trans('lang.save')}} {{trans('lang.coupon')}}</button>
    <a href="{!! route('coupons.index') !!}" class="btn btn-default"><i class="fas fa-undo"></i> {{trans('lang.cancel')}}</a>
</div>
