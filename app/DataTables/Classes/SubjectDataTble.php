<?php

namespace App\DataTables\Classes;

use App\Models\Classes\Subjects;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Facades\Log;

class SubjectDataTble extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->addColumn('created_at', function($subject){
                return date('d, M Y', strtotime($subject->created_at));
            })
            ->addColumn('user_created_by', function($subject){
                $user = $subject->userCreatedBy;
                return $user?$user->first_name.' '.$user->last_name:null;
            })
            ->addColumn('action', function ($subject) {
                return view('classes.subject.actions', compact('subject'))->render();
            })
            ->rawColumns(['action']);
    }

    public function query(Subjects $model): QueryBuilder
    {
        return $model->newQuery()->with('userCreatedBy');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('subject-table')
            ->addTableClass('table datatable-table table-hover')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->ajax()
            ->parameters([
                'initComplete' => 'function() {
                    var table = this.api();

                    $("#subject-table thead tr")
                        .clone(true)
                        .addClass("filters")
                        .appendTo("#subject-table thead");
                    table.columns().every(function(index) {
                        var column = this;
                        var isSearchable = column.settings()[0].aoColumns[index].bSearchable;
                        var cell = $(".filters th").eq($(column.header()).index());
                        $(cell).removeClass("dt-orderable-asc dt-orderable-desc dt-ordering-asc dt-ordering-desc");
                        if (isSearchable) {
                            $(cell).html("<input type=\'text\' class=\'form-control form-control-md\' placeholder=\'\' />");
                            var typingTimer;
                            var debounceInterval = 500;
                            $("input", cell)
                                .on("keyup change clear", function() {
                                    var input = this;
                                    clearTimeout(typingTimer);
                                    typingTimer = setTimeout(function() {
                                        if (column.search() !== input.value) {
                                            column.search(input.value).draw();
                                        }
                                    }, debounceInterval);
                                })
                                .on("keydown", function() {
                                    clearTimeout(typingTimer);
                                })
                                .on("click mousedown", function(e) {
                                    e.stopPropagation();
                                });
                        } else {
                            $(cell).html("");
                        }
                    });
                }'
            ])
            ->orderBy(1);
    }

    public function getColumns(): array
    {
        return [
            Column::make('DT_RowIndex')->title(__('general.no'))->className('text-center')->orderable(false)->searchable(false)->className('text-center'),
            Column::make('name')->title(__('general.name')),
            Column::make('description')->title(__('general.description'))->searchable(false)->className('text-start')->orderable(false),
            Column::make('user_created_by')->title(__('general.created_by'))->searchable(false)->className('text-center')->orderable(false),
            Column::make('created_at')->title(__('general.created_at'))->searchable(false)->className('text-center'),
            Column::computed('action')->title(__('general.action'))->className('text-center')->exportable(false)->printable(false)->width(150),
        ];
    }

    protected function filename(): string
    {
        return 'Subjects_' . date('YmdHis');
    }
}
