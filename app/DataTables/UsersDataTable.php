<?php

namespace App\DataTables;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Facades\Auth;

class UsersDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        if (request()->filled('filter_username')) {
            $query->where('username', 'like', '%' . request('filter_username') . '%');
        }
        if (request()->filled('filter_email')) {
            $query->where('email', 'like', '%' . request('filter_email') . '%');
        }

        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->addColumn('date', function($user){
                return date('d, M Y', strtotime($user->date_of_birth));
            })
            ->addColumn('profile_image', function($user){
                $default_image = 'backend/images/users/avatar-6.jpg';
                $imagePath = $user->profile ? asset($user->profile) : asset($default_image);
                return '<img src="'.$imagePath.'" class="thumb-md shadow-sm rounded-circle text-center avatar-image" alt="avatar">';
            })
            ->addColumn('action', function ($user) {
                return view('users.actions', compact('user'))->render();
            })
            ->rawColumns(['profile_image', 'action']);
    }

    public function query(User $model): QueryBuilder
    {
        return $model->newQuery()->where('id', '!=', Auth::id());
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('users-table')
            ->addTableClass('table datatable-table table-hover')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->ajax([
                'data' => 'function(d) {
                    d.filter_username = $("#filter_username").val();
                    d.filter_email = $("#filter_email").val();
                }'
            ])
            ->parameters([
                'initComplete' => 'function() {
                    var table = this.api();

                    $("#users-table thead tr")
                        .clone(true)
                        .addClass("filters")
                        .appendTo("#users-table thead");
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
            Column::make('DT_RowIndex')->title('No')->className('text-center')->orderable(false)->searchable(false)->className('text-center'),
            Column::make('profile_image')->searchable(false)->className('text-center')->orderable(false)->width(150),
            Column::make('username'),
            Column::make('first_name'),
            Column::make('last_name'),
            Column::make('email'),
            Column::make('phone')->searchable(false)->className('text-center'),
            Column::make('date')->searchable(false)->className('text-center'),
            Column::computed('action')->className('text-center')->exportable(false)->printable(false)->width(150),
        ];
    }

    protected function filename(): string
    {
        return 'Users_' . date('YmdHis');
    }
}
