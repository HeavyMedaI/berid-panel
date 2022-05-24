<h5 class="col-12 pb-4">{!! trans('lang.app_setting_iyzico_credentials') !!}</h5>
<div class="d-flex flex-column col-sm-12 col-md-6">
    <!-- Route Field -->
    <div class="form-group align-items-baseline d-flex flex-column flex-md-row">
        {!! Form::label('iyzico_api_token', trans("lang.app_setting_iyzico_api_token"), ['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
        <div class="col-md-9">
            {!! Form::text('iyzico_api_token', setting('iyzico_api_token'),  ['class' => 'form-control','placeholder'=>  trans("lang.app_setting_iyzico_api_token_placeholder")]) !!}
            <div class="form-text text-muted">
                {{ trans("lang.app_setting_iyzico_api_token_help") }}
            </div>
        </div>
    </div>
    <!-- Route Field -->
    <div class="form-group align-items-baseline d-flex flex-column flex-md-row">
        {!! Form::label('iyzico_api_url', trans("lang.app_setting_iyzico_api_url"), ['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
        <div class="col-md-9">
            {!! Form::text('iyzico_api_url', setting('iyzico_api_url'),  ['class' => 'form-control','placeholder'=>  trans("lang.app_setting_iyzico_api_url_placeholder")]) !!}
            <div class="form-text text-muted">
                {{ trans("lang.app_setting_iyzico_api_url_help") }}
            </div>
        </div>
    </div>
</div>
<div class="d-flex flex-column col-sm-12 col-md-6">
    <!-- Route Field -->
    <div class="form-group align-items-baseline d-flex flex-column flex-md-row">
        {!! Form::label('iyzico_secret_key', trans("lang.app_setting_iyzico_secret_key"), ['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
        <div class="col-md-9">
            {!! Form::text('iyzico_secret_key', setting('iyzico_secret_key'),  ['class' => 'form-control','placeholder'=>  trans("lang.app_setting_iyzico_secret_key_placeholder")]) !!}
            <div class="form-text text-muted">
                {{ trans("lang.app_setting_iyzico_secret_key_help") }}
            </div>
        </div>
    </div>
    <!-- Boolean Enabled Field -->
    <div class="form-group align-items-baseline d-flex flex-column flex-md-row">
        {!! Form::label('enable_iyzico', trans("lang.app_setting_enable_iyzico"),['class' => 'col-md-3 control-label text-md-right mx-1']) !!}
        {!! Form::hidden('enable_iyzico', 0, ['id'=>"hidden_enable_iyzico"]) !!}
        <div class="col-9 icheck-{{setting('theme_color')}}">
            {!! Form::checkbox('enable_iyzico', 1, setting('enable_iyzico')) !!}
            <label for="enable_iyzico"></label>
        </div>
    </div>
</div>
