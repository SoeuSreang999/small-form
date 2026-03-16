<?php

namespace App\DataTables\Classes;

use App\Models\Classes\Classes;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Facades\Log;

class ClassesDataTble extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->addColumn('created_at', function($class){
                return date('d, M Y', strtotime($class->created_at));
            })
            ->addColumn('subjects', function ($class) {
                if (!$class->subject_names) return '';
                    $subjects = explode(', ', $class->subject_names);
                    $html = '<div class="d-flex flex-column align-items-start gap-1">';
                    foreach ($subjects as $name) {
                        $html .= '<span class="badge bg-primary-subtle text-primary me-1">' .$name. '</span>';
                    }
                    $html .= '</div>';
                return $html;
            })
            ->addColumn('user_created_by', function($class){
                $user = $class->userCreatedBy;
                return $user?$user->first_name.' '.$user->last_name:null;
            })
            ->addColumn('action', function ($class) {
                return view('classes.actions', compact('class'))->render();
            })
            ->rawColumns(['subjects', 'action']);
    }

    public function query(Classes $model): QueryBuilder
    {
        return $model->newQuery()
            ->leftJoin('class_subjects', function($join) {
                $join->on('class_subjects.class_id', '=', 'classes.id')
                    ->whereNull('class_subjects.deleted_at');
            })
            ->leftJoin('subjects', 'subjects.id', '=', 'class_subjects.subject_id')
            ->select([
                'classes.*',
                \DB::raw('GROUP_CONCAT(DISTINCT subjects.name SEPARATOR ", ") as subject_names'),
            ])
            ->groupBy('classes.id');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('classes-table')
            ->addTableClass('table datatable-table table-hover')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->ajax()
            ->parameters([
                'initComplete' => 'function() {
                    var table = this.api();

                    $("#classes-table thead tr")
                        .clone(true)
                        .addClass("filters")
                        .appendTo("#classes-table thead");
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
            Column::make('name')->title(__('general.name'))->className('text-start'),
            Column::make('description')->title(__('general.description'))->searchable(false)->className('text-start'),
            Column::make('subjects')->title(__('general.subject'))->searchable(false)->className('text-start'),
            Column::make('user_created_by')->title(__('general.created_by'))->searchable(false)->className('text-center'),
            Column::make('created_at')->title(__('general.created_at'))->searchable(false)->className('text-center'),
            Column::computed('action')->title(__('general.action'))->className('text-center')->exportable(false)->printable(false)->width(150),
        ];
    }

    protected function filename(): string
    {
        return 'Classes' . date('YmdHis');
    }
}
