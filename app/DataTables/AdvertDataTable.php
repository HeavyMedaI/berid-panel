<?php
/*
 * File name: AdvertDataTable.php
 * Last modified: 2021.06.10 at 20:38:02
 * Author: Musa ATALAY - musaatalay.work@gmail.com
 * Copyright (c) 2022
 */

namespace App\DataTables;

use App\Models\Advert;
use App\Models\CustomField;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTableAbstract;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\Services\DataTable;

class AdvertDataTable extends DataTable
{
    /**
     * custom fields columns
     * @var array
     */
    public static $customFields = [];

    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return DataTableAbstract
     */
    public function dataTable($query)
    {
        $dataTable = new EloquentDataTable($query);
        $columns = array_column($this->getColumns(), 'data');
        $dataTable = $dataTable
            ->editColumn('id', function ($advert) {
                return "#" . $advert->id;
            })
            ->editColumn('ref_code', function ($advert) {
                return $advert->ref_code;
            })
            ->editColumn('user.name', function ($advert) {
                return getLinksColumnByRouteName([$advert->user], 'users.edit', 'id', 'name');
            })
            ->editColumn('driver.name', function ($advert) {
                return getLinksColumnByRouteName([$advert->driver], 'users.edit', 'id', 'name');
            })
            ->editColumn('city_pick_up', function ($advert) {
                $city = json_decode($advert->city_pick_up);
                return get_political_address($city->address_components);
            })
            ->editColumn('city_drop_off', function ($advert) {
                $city = json_decode($advert->city_drop_off);
                return get_political_address($city->address_components);
            })
            ->editColumn('advert_status.status', function ($advert) {
                if (isset($advert->status)){
                    $color = setting('theme_color');
                    if ($advert->status->status == "Ready") {
                        $color = "success";
                    }else if ($advert->status->status == "In Progress") {
                        $color = "warning";
                    }
                    return "<span class='badge px-2 py-1 bg-" . $color . "'>" . $advert->status->status . "</span>";
                }
                return "-";
            })
            ->editColumn('payment.payment_status.status', function ($advert) {
                if (isset($advert->payment)) {
                    $color = setting('theme_color');
                    if ($advert->payment->paymentStatus->status == "Paid") {
                        $color = "success";
                    }else if ($advert->payment->paymentStatus->status == "Pending") {
                        $color = "warning";
                    }
                    return "<span class='badge px-2 py-1 bg-" . $color . "'>" . $advert->payment->paymentStatus->status . "</span>";
                } else {
                    return '-';
                }
            })
            ->editColumn('total', function ($advert) {
                return "<span class='text-bold text-success'>" . getPrice($advert->getTotal()) . "</span>";
            })
            ->editColumn('is_urgent', function ($advert) {
                $advertSizes = ["", "Zarf", "Koltuğa Sığar", "Bagaja Sığar"];
                return "<span class='text-primary'>" . ($advert->is_urgent ? "Evet" : "Hayır") . "</span>";
            })
            ->editColumn('size', function ($advert) {
                $advertSizes = ["", "Zarf", "Koltuğa Sığar", "Bagaja Sığar"];
                return "<span class='text-primary'>" . $advertSizes[$advert->size] . "</span>";
            })
            ->editColumn('created_at', function ($advert) {
                return getDateColumn($advert, 'created_at');
            })
            ->editColumn('date_pick_up', function ($advert) {
                return date("d.m.Y", strtotime($advert->date_pick_up));
            })
            ->editColumn('date_drop_off', function ($advert) {
                return date("d.m.Y", strtotime($advert->date_drop_off));
            })
            ->setRowClass(function ($advert) {
                if (isset($advert->status)){
                    if ($advert->status->status == "Canceled") {
                        return $advert->cancel ? 'row-cancel' : '';
                    }
                }
            })
            ->addColumn('action', 'adverts.datatables_actions')
            ->rawColumns(array_merge($columns, ['action']));

        return $dataTable;
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        $columns = [
            [
                'data' => 'id',
                'title' => trans('lang.advert_id'),
            ],
            [
                'data' => 'title',
                'title' => trans('lang.title'),
            ],
            [
                'data' => 'ref_code',
                'name' => 'ref_code',
                'title' => trans('lang.ref_code'),

            ],
            [
                'data' => 'user.name',
                'name' => 'user.name',
                'title' => trans('lang.advert_user_id'),
            ],
            [
                'data' => 'driver.name',
                'name' => 'driver.name',
                'title' => trans('lang.advert_driver_id'),
            ],
            [
                'data' => 'receiver_full_name',
                'name' => 'receiver_full_namee',
                'title' => trans('lang.receiver_full_name'),
            ],
            [
                'data' => 'description',
                'name' => 'description',
                'title' => trans('lang.advert_description'),
            ],
            [
                'data' => 'city_pick_up',
                'name' => 'city_pick_up',
                'title' => trans('lang.pickup_city'),
            ],
            [
                'data' => 'city_drop_off',
                'name' => 'city_drop_off',
                'title' => trans('lang.drop_city'),
            ],
            [
                'data' => 'advert_status.status',
                'name' => 'advertStatus.status',
                'title' => trans('lang.advert_advert_status_id'),
            ],
            [
                'data' => 'payment.payment_status.status',
                'name' => 'payment.paymentStatus.status',
                'title' => trans('lang.payment_payment_status_id'),
            ],
            [
                'data' => 'total',
                'title' => trans('lang.advert_total'),
                'searchable' => false,
                'orderable' => true,

            ],
            [
                'data' => 'is_urgent',
                'title' => trans('lang.advert_is_urgent'),
                'searchable' => false,
                'orderable' => false,

            ],
            [
                'data' => 'size',
                'title' => trans('lang.advert_size'),
                'searchable' => true,
                'orderable' => false,

            ],
            [
                'data' => 'quantity',
                'title' => trans('lang.quantity'),
                'searchable' => true,
                'orderable' => false,

            ],
            [
                'data' => 'date_pick_up',
                'title' => trans('lang.advert_date_pick_up'),

            ],
            [
                'data' => 'date_drop_off',
                'title' => trans('lang.advert_date_drop_off'),

            ],
            [
                'data' => 'created_at',
                'title' => trans('lang.advert_created_at'),

            ],
        ];

        $hasCustomField = in_array(Advert::class, setting('custom_field_models', []));
        if ($hasCustomField) {
            $customFieldsCollection = CustomField::where('custom_field_model', Advert::class)->where('in_table', '=', true)->get();
            foreach ($customFieldsCollection as $key => $field) {
                array_splice($columns, $field->order - 1, 0, [[
                    'data' => 'custom_fields.' . $field->name . '.view',
                    'title' => trans('lang.advert_' . $field->name),
                    'orderable' => false,
                    'searchable' => false,
                ]]);
            }
        }
        return $columns;
    }

    /**
     * Get query source of dataTable.
     *
     * @param Advert $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Advert $model)
    {
        return $model->newQuery()->select('adverts.*');
        if (auth()->user()->hasRole('admin')) {
            #return $model->newQuery()->with("user")->with("advertStatus")->with("payment")->with("payment.paymentStatus")->select('adverts.*');
            return $model->newQuery()->with("user")->with("advertStatus")->with("payment")->with("payment.paymentStatus")->select('adverts.*');
        } else if (auth()->user()->hasRole('provider')) {
            $eProviderId = DB::raw("json_extract(e_provider, '$.id')");
            return $model->newQuery()->with("user")->with("advertStatus")->with("payment")->with("payment.paymentStatus")->join("e_provider_users", "e_provider_users.e_provider_id", "=", $eProviderId)
                ->where('e_provider_users.user_id', auth()->id())
                ->groupBy('adverts.id')
                ->select('adverts.*');

        } else if (auth()->user()->hasRole('customer')) {
            return $model->newQuery()->with("user")->with("advertStatus")->with("payment")->with("payment.paymentStatus")->where('adverts.user', auth()->id())
                ->select('adverts.*')
                ->groupBy('adverts.id');
        } else {
            #return $model->newQuery()->with("user")->with("advertStatus")->with("payment")->with("payment.paymentStatus")->select('adverts.*');
            return $model->newQuery()->with("user")->with("advertStatus")->with("payment")->with("payment.paymentStatus")->select('adverts.*');
        }
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return Builder
     */
    public function html()
    {
        return $this->builder()
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->addAction(['title' => trans('lang.actions'), 'width' => '80px', 'printable' => false, 'responsivePriority' => '100'])
            ->parameters(array_merge(
                config('datatables-buttons.parameters'), [
                    'language' => json_decode(
                        file_get_contents(base_path('resources/lang/' . app()->getLocale() . '/datatable.json')
                        ), true)
                ]
            ));
    }

    /**
     * Export PDF using DOMPDF
     * @return mixed
     */
    public function pdf()
    {
        $data = $this->getDataForPrint();
        $pdf = PDF::loadView($this->printPreview, compact('data'));
        return $pdf->download($this->filename() . '.pdf');
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'adverts_' . time();
    }
}
