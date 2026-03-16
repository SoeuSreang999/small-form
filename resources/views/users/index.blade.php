@extends('layouts.app')
@section('title', 'Users List')

@section('content')
    <div class="row">
        <div class="col-lg-12 ms-auto d-flex gap-4 align-items-center">
            <div class="col d-flex align-items-center">
                <span class="me-2">Rows</span>
                <select class="form-select border rounded w-auto select-page-length form-select2">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
            </div>
            <div class="col-8 col-md-6 col-lg-4 d-flex align-items-center">
                <div class="input-group search-box me-2" id="filter_div">
                    <button class="btn search-icon-btn" type="button" id="button-addon1">
                        <i class="fas fa-search"></i>
                    </button>
                    <input name="filter_text" type="text" class="form-control search-input"
                        placeholder="Search In Users" aria-label="Search" aria-describedby="button-addon1"
                        autocomplete="off" />

                    <button type="button" class="btn dropdown-toggle rounded-lg rounded-right" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <i class="fas fa-filter"></i>
                    </button>
                    <div class="dropdown-menu p-4">
                        <div class="container-fluid">
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label">Username</label>
                                <div class="col-sm-10">
                                    <input type="text" name="filter_username" id="filter_username" class="form-control"
                                        placeholder="Enter Username" />
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label">Email</label>
                                <div class="col-sm-10">
                                    <input type="text" name="filter_email" id="filter_email" class="form-control"
                                        placeholder="Enter Email" />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12 text-end">
                                    <button type="button" class="btn btn-danger px-4 me-2 filter-reset">
                                        Reset
                                    </button>
                                    <button type="submit" class="btn btn-primary px-4 filter-search">
                                        Search
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-auto d-flex align-items-center">
                <button class="btn btn-outline-primary">
                    <i class="fas fa-plus me-1"></i>
                    <span class="">Add User</span>
                </button>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            {!! $dataTable->table() !!}
            {!! $dataTable->scripts() !!}
        </div>
    </div>
@endsection

@push('scripts')
    @include('users.script')
@endpush
