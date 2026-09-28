@extends('layouts.master')
@section('css')
    <!--Internal  Font Awesome -->
    <link href="{{ URL::asset('assets/plugins/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <!--Internal  treeview -->
    <link href="{{ URL::asset('assets/plugins/treeview/treeview-rtl.css') }}" rel="stylesheet" type="text/css" />



@section('title')
    {{ __('roles.Viewـpermissions') }}
@stop


@endsection
@section('page-header')
<div class="main-parent">
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between parent-heading">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ __('roles.Viewـpermissions') }}
            </div>
        </div>
    </div>
    <!-- breadcrumb -->


    <!-- row -->
    <div class="row">
        <div class="col-md-12">
            <div class="card mg-b-20 py-5">
                <div class="card-body">
                    <div class="main-content-label mg-b-5">
                        <div class="pull-right">
                            <a style="background-color: #23395D;font-size:14px" class="btn btn-primary btn-sm"
                                href="{{ route('roles.index') }}">
                                {{ __('users.back') }}
                                
                            </a>
                        </div>
                    </div>
                    <div class="row">
                        <!-- col -->
                        <div class="col-lg-4 mt-4">
                            <ul id="treeview1">
                                <li><a style="background-color: #419BB2" class="btn btn-danger py-1" href="#">
                                    {{ $role->name }}
                                    <svg style="width:15px !important" class="svg-icon-buttons" viewBox="0 0 20 20">
                                        <path d="M17.927,5.828h-4.41l-1.929-1.961c-0.078-0.079-0.186-0.125-0.297-0.125H4.159c-0.229,0-0.417,0.188-0.417,0.417v1.669H2.073c-0.229,0-0.417,0.188-0.417,0.417v9.596c0,0.229,0.188,0.417,0.417,0.417h15.854c0.229,0,0.417-0.188,0.417-0.417V6.245C18.344,6.016,18.156,5.828,17.927,5.828 M4.577,4.577h6.539l1.231,1.251h-7.77V4.577z M17.51,15.424H2.491V6.663H17.51V15.424z"></path>
                                    </svg>
                                </a>
                                    <ul>
                                        @if (!empty($rolePermissions))
                                            @foreach ($rolePermissions as $v)
                                                <li style="font-size: 14px;color:#419BB2">{{ app()->getLocale() == 'ar' ? $v->name_ar : $v->name }}</li>
                                            @endforeach
                                        @endif
                                    </ul>
                                </li>
                            </ul>
                        </div>
                        <!-- /col -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- row closed -->
</div>
<!-- Container closed -->
</div>
<!-- main-content closed -->
</div>
@endsection
@section('js')
<script src="{{ URL::asset('assets/plugins/treeview/treeview.js') }}"></script>

@endsection
