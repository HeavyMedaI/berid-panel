@can('dashboard')
    <li class="nav-item">
        <a class="nav-link {{ Request::is('dashboard*') ? 'active' : '' }}" href="{!! url('dashboard') !!}">@if($icons)
                <i class="nav-icon fas fa-tachometer-alt"></i>@endif
            <p>{{trans('lang.dashboard')}}</p></a>
    </li>
@endcan

@can('notifications.index')
    <li class="nav-item">
        <a class="nav-link {{ Request::is('notifications*') ? 'active' : '' }}" href="{!! route('notifications.index') !!}">@if($icons)
                <i class="nav-icon fas fa-bell"></i>@endif<p>{{trans('lang.notification_plural')}}</p></a>
    </li>
@endcan
{{--@can('favorites.index')
    <li class="nav-item">
        <a class="nav-link {{ Request::is('favorites*') ? 'active' : '' }}" href="{!! route('favorites.index') !!}">@if($icons)
                <i class="nav-icon fas fa-heart"></i>@endif<p>{{trans('lang.favorite_plural')}}</p></a>
    </li>
@endcan--}}

<li class="nav-header">{{trans('lang.app_management')}}</li>

{{--@can('adverts.index')--}}
<li class="nav-item has-treeview {{ Request::is('adverts*') || Request::is('advertStatuses*') ? 'menu-open' : '' }}">
    <a href="#" class="nav-link {{ Request::is('adverts*') || Request::is('advertStatuses*') ? 'active' : '' }}"> @if($icons)
            <i class="nav-icon fas fa-calendar-check"></i>@endif
        <p>{{trans('lang.advert_plural')}} <i class="right fas fa-angle-left"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">

        @can('adverts.index')
            <li class="nav-item">
                <a class="nav-link {{ Request::is('adverts*') ? 'active' : '' }}" href="{!! route('adverts.index') !!}">@if($icons)
                        <i class="nav-icon fas fa-calendar-check"></i>@endif<p>{{trans('lang.advert_plural')}}</p></a>
            </li>
        @endcan
        @can('advertStatuses.index')
            <li class="nav-item">
                <a class="nav-link {{ Request::is('advertStatuses*') ? 'active' : '' }}" href="{!! route('advertStatuses.index') !!}">@if($icons)
                        <i class="nav-icon fas fa-server"></i>@endif<p>{{trans('lang.advert_status_plural')}}</p></a>
            </li>
        @endcan

    </ul>
</li>
{{--@endcan--}}

{{--@can('bookings.index')--}}

{{--@endcan--}}

@can('coupons.index')
    <li class="nav-item">
        <a class="nav-link {{ Request::is('coupons*') ? 'active' : '' }}" href="{!! route('coupons.index') !!}">@if($icons)
                <i class="nav-icon fas fa-ticket-alt"></i>@endif<p>{{trans('lang.coupon_plural')}} </p></a>
    </li>
@endcan
@can('driver_documents.index')
    <li class="nav-item">
        <a class="nav-link {{ Request::is('driver_documents*') ? 'active' : '' }}" href="{!! route('driver_documents.index') !!}">@if($icons)
                <i class="nav-icon fas fa-id-card"></i>@endif<p>{{trans('lang.driver_documents_plural')}} </p></a>
    </li>
@endcan
@can('faqs.index')
    <li class="nav-item {{ Request::is('faqCategories*') || Request::is('faqs*') ? 'menu-open' : '' }}">
        <a href="#" class="nav-link {{ Request::is('faqs*') || Request::is('faqCategories*') ? 'active' : '' }}"> @if($icons)
                <i class="nav-icon fas fa-question-circle"></i>@endif
            <p>{{trans('lang.faq_plural')}} <i class="right fas fa-angle-left"></i>
            </p>
        </a>
        <ul class="nav nav-treeview">
            @can('faqCategories.index')
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('faqCategories*') ? 'active' : '' }}" href="{!! route('faqCategories.index') !!}">@if($icons)
                            <i class="nav-icon fas fa-folder-open"></i>@endif<p>{{trans('lang.faq_category_plural')}}</p></a>
                </li>
            @endcan

            @can('faqs.index')
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('faqs*') ? 'active' : '' }}" href="{!! route('faqs.index') !!}">@if($icons)
                            <i class="nav-icon fas fa-life-ring"></i>@endif
                        <p>{{trans('lang.faq_plural')}}</p></a>
                </li>
            @endcan
        </ul>
    </li>
@endcan
<li class="nav-header">{{trans('lang.payment_plural')}}</li>
@can('payments.index')
    <li class="nav-item has-treeview {{ Request::is('payments*') || Request::is('paymentMethods*') || Request::is('paymentStatuses*') ? 'menu-open' : '' }}">
        <a href="#" class="nav-link {{ Request::is('payments*') || Request::is('paymentMethods*') || Request::is('paymentStatuses*') ? 'active' : '' }}"> @if($icons)
                <i class="nav-icon fas fa-money-check-alt"></i>@endif
            <p>{{trans('lang.payment_plural')}}<i class="right fas fa-angle-left"></i>
            </p>
        </a>
        <ul class="nav nav-treeview">

            @can('payments.index')
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('payments*') ? 'active' : '' }}" href="{!! route('payments.index') !!}">@if($icons)
                            <i class="nav-icon fas fa-money-check-alt"></i>@endif<p>{{trans('lang.payment_table')}}</p></a>
                </li>
            @endcan
            @can('paymentMethods.index')
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('paymentMethods*') ? 'active' : '' }}" href="{!! route('paymentMethods.index') !!}">@if($icons)
                            <i class="nav-icon fas fa-credit-card"></i>@endif<p>{{trans('lang.payment_method_plural')}}</p></a>
                </li>
            @endcan


            @can('paymentStatuses.index')
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('paymentStatuses*') ? 'active' : '' }}" href="{!! route('paymentStatuses.index') !!}">@if($icons)
                            <i class="nav-icon fas fa-file-invoice-dollar"></i>@endif<p>{{trans('lang.payment_status_plural')}}</p></a>
                </li>
            @endcan

        </ul>
    </li>
@endcan
@can('earnings.index')
    <li class="nav-item">
        <a class="nav-link {{ Request::is('earnings*') ? 'active' : '' }}" href="{!! route('earnings.index') !!}">@if($icons)
                <i class="nav-icon fas fa-money-bill"></i>@endif<p>{{trans('lang.earning_plural')}}  </p></a>
    </li>
@endcan
<li class="nav-header">{{trans('lang.app_setting')}}</li>
@can('medias')
    <li class="nav-item">
        <a class="nav-link {{ Request::is('medias*') ? 'active' : '' }}" href="{!! url('medias') !!}">@if($icons)
                <i class="nav-icon fas fa-photo-video"></i>@endif
            <p>{{trans('lang.media_plural')}}</p></a>
    </li>
@endcan

@can('app-settings')
    <li class="nav-item has-treeview {{
    (Request::is('settings*') ||
     Request::is('users*')) && !Request::is('settings/mobile*')
        ? 'menu-open' : '' }}">
        <a href="#" class="nav-link {{
        (Request::is('settings*') ||
         Request::is('users*')) && !Request::is('settings/mobile*')
          ? 'active' : '' }}"> @if($icons)<i class="nav-icon fas fa-cogs"></i>@endif
            <p>{{trans('lang.app_setting')}} <i class="right fas fa-angle-left"></i>
            </p>
        </a>
        <ul class="nav nav-treeview">
            <li class="nav-item">
                <a href="{!! url('settings/app/globals') !!}" class="nav-link {{  Request::is('settings/app/globals*') ? 'active' : '' }}">
                    @if($icons)<i class="nav-icon fas fa-cog"></i> @endif <p>{{trans('lang.app_setting_globals')}}</p>
                </a>
            </li>

            @can('users.index')
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('users*') ? 'active' : '' }}" href="{!! route('users.index') !!}">@if($icons)
                            <i class="nav-icon fas fa-users"></i>@endif
                        <p>{{trans('lang.user_plural')}}</p></a>
                </li>
            @endcan

            <li class="nav-item has-treeview {{ Request::is('settings/permissions*') || Request::is('settings/roles*') ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{ Request::is('settings/permissions*') || Request::is('settings/roles*') ? 'active' : '' }}">
                    @if($icons)<i class="nav-icon fas fa-user-secret"></i>@endif
                    <p>
                        {{trans('lang.permission_menu')}}
                        <i class="right fas fa-angle-left"></i>
                    </p></a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('settings/permissions') ? 'active' : '' }}" href="{!! route('permissions.index') !!}">
                            @if($icons)<i class="nav-icon fas fa-circle-o"></i>@endif
                            <p>{{trans('lang.permission_table')}}</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('settings/permissions/create') ? 'active' : '' }}" href="{!! route('permissions.create') !!}">
                            @if($icons)<i class="nav-icon fas fa-circle-o"></i>@endif
                            <p>{{trans('lang.permission_create')}}</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('settings/roles') ? 'active' : '' }}" href="{!! route('roles.index') !!}">
                            @if($icons)<i class="nav-icon fas fa-circle-o"></i>@endif
                            <p>{{trans('lang.role_table')}}</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('settings/roles/create') ? 'active' : '' }}" href="{!! route('roles.create') !!}">
                            @if($icons)<i class="nav-icon fas fa-circle-o"></i>@endif
                            <p>{{trans('lang.role_create')}}</p>
                        </a>
                    </li>
                </ul>

            </li>

            <li class="nav-item">
                <a class="nav-link {{ Request::is('settings/customFields*') ? 'active' : '' }}" href="{!! route('customFields.index') !!}">@if($icons)
                        <i class="nav-icon fas fa-list"></i>@endif<p>{{trans('lang.custom_field_plural')}}</p></a>
            </li>

            <li class="nav-item">
                <a href="{!! url('settings/app/localisation') !!}" class="nav-link {{  Request::is('settings/app/localisation*') ? 'active' : '' }}">
                    @if($icons)<i class="nav-icon fas fa-language"></i> @endif <p>{{trans('lang.app_setting_localisation')}}</p></a>
            </li>
            @can('currencies.index')
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('settings/currencies*') ? 'active' : '' }}" href="{!! route('currencies.index') !!}">@if($icons)
                            <i class="nav-icon fas fa-dollar-sign"></i>@endif<p>{{trans('lang.currency_plural')}}</p></a>
                </li>
            @endcan
            @can('taxes.index')
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('settings/taxes*') ? 'active' : '' }}" href="{!! route('taxes.index') !!}">@if($icons)
                            <i class="nav-icon fas fa-coins"></i>@endif
                        <p>{{trans('lang.tax_plural')}}</p></a>
                </li>
            @endcan

            <li class="nav-item">
                <a href="{!! url('settings/payment/payment') !!}" class="nav-link {{  Request::is('settings/payment*') ? 'active' : '' }}">
                    @if($icons)<i class="nav-icon fas fa-credit-card"></i> @endif <p>{{trans('lang.app_setting_payment')}}</p>
                </a>
            </li>

            <li class="nav-item">
                <a href="{!! url('settings/app/social') !!}" class="nav-link {{  Request::is('settings/app/social*') ? 'active' : '' }}">
                    @if($icons)<i class="nav-icon fas fa-globe"></i> @endif <p>{{trans('lang.app_setting_social')}}</p>
                </a>
            </li>

            <li class="nav-item">
                <a href="{!! url('settings/app/notifications') !!}" class="nav-link {{  Request::is('settings/app/notifications*') ? 'active' : '' }}">
                    @if($icons)<i class="nav-icon fas fa-bell"></i> @endif <p>{{trans('lang.app_setting_notifications')}}</p>
                </a>
            </li>

            <li class="nav-item">
                <a href="{!! url('settings/mail/smtp') !!}" class="nav-link {{ Request::is('settings/mail*') ? 'active' : '' }}">
                    @if($icons)<i class="nav-icon fas fa-envelope"></i> @endif <p>{{trans('lang.app_setting_mail')}}</p>
                </a>
            </li>

        </ul>
    </li>
@endcan

