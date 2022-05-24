<?php
/*
 * File name: DriverDocumentsDataTable.php
 * Last modified: 2021.04.12 at 10:04:50
 * Author: Musa ATALAY - musaatalay.work@gmail.com
 * Copyright (c) 2022
 */

namespace App\DataTables;

use App\Models\CustomField;
use App\Models\DriverDocuments;
use App\Models\Post;
use App\Models\User;
use Barryvdh\DomPDF\Facade as PDF;
use Yajra\DataTables\DataTableAbstract;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\Services\DataTable;

class DriverDocumentsDataTable extends DataTable
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
            ->editColumn('user.name', function ($driverDocs) {
                return getLinksColumnByRouteName([User::find($driverDocs->user_id)], 'users.edit', 'id', 'name');
            })
            ->editColumn('is_ripped', function ($driverDocs) {
                return getBooleanColumn($driverDocs, 'is_ripped');
            })
            ->editColumn('status', function ($driverDocs) {
                if ($driverDocs->status == 0)  {
                    return '<span class="badge badge-primary">'.trans('status.driver_docs.pending_confirmation').'</span>';;
                }elseif ($driverDocs->status == 1) {
                    return '<span class="badge badge-success">'.trans('status.driver_docs.confirmed').'</span>';
                }elseif ($driverDocs->status == 2)  {
                    return '<span class="badge badge-warning">'.trans('status.driver_docs.not_fit').'</span>';
                }
            })
            ->editColumn('updated_at', function ($driverDocs) {
                return getDateColumn($driverDocs, 'updated_at');
            })
            ->addColumn('action', 'driver_documents.datatables_actions')
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
                'data' => 'car_brand',
                'title' => trans('lang.car_brand'),

            ],
            [
                'data' => 'car_model',
                'title' => trans('lang.car_model'),

            ],
            [
                'data' => 'car_plate',
                'title' => trans('lang.car_plate'),

            ],
            [
                'data' => 'is_ripped',
                'title' => trans('lang.is_ripped'),

            ],
            [
                'data' => 'user.name',
                'name' => 'user.name',
                'title' => trans('lang.driver'),
            ],
            [
                'data' => 'status_description',
                'title' => trans('lang.status_description'),

            ],
            [
                'data' => 'status',
                'title' => trans('lang.status'),

            ],
            [
                'data' => 'updated_at',
                'title' => trans('lang.updated_at'),
                'searchable' => false,
            ]
        ];

        $hasCustomField = in_array(DriverDocuments::class, setting('custom_field_models', []));
        if ($hasCustomField) {
            $customFieldsCollection = CustomField::where('custom_field_model', DriverDocuments::class)->where('in_table', '=', true)->get();
            foreach ($customFieldsCollection as $key => $field) {
                array_splice($columns, $field->order - 1, 0, [[
                    'data' => 'custom_fields.' . $field->name . '.view',
                    'title' => trans('lang.driver_documents_' . $field->name),
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
     * @param DriverDocuments $model
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder
     */
    public function query(DriverDocuments $model)
    {
        if (auth()->user()->hasRole('admin')) {
            return $model->newQuery();
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
                        ), true),
                    'order' => [[5, 'desc']],
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
        return 'driver_documents_' . time();
    }
}
