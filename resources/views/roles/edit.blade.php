@extends('layouts.master')
@section('css')
    <!--Internal  Font Awesome -->
    <link href="{{ URL::asset('assets/plugins/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <!--Internal  treeview -->
    <link href="{{ URL::asset('assets/plugins/treeview/treeview-rtl.css') }}" rel="stylesheet" type="text/css" />
@section('title')
    {{ __('roles.Permission_update') }}
@stop
@endsection
@section('page-header')
<div class="main-parent">
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between parent-heading">
        <div class="my-auto">
            <div class="d-flex">
                <h5 style="color: white" class="content-title mb-0 my-auto">{{ __('roles.Permission_update') }}</h5>
            </div>
        </div>
    </div>
    <!-- breadcrumb -->
@endsection
@section('content')

    @if (count($errors) > 0)
        <div class="alert alert-danger">
            <button aria-label="Close" class="close" data-dismiss="alert" type="button">
                <span aria-hidden="true">&times;</span>
            </button>
            <strong>خطا</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    {!! Form::model($role, ['method' => 'PATCH', 'route' => ['roles.update', $role->id]]) !!}
    <!-- row -->
    <div class="row">
        <div class="col-md-12">
            <div class="card mg-b-20 px-3 pt-5">
                <div class="card-body">
                    <div class="main-content-label mg-b-5">
                        <div class="form-group">
                            <p>{{ __('roles.name_permission') }}</p>
                            {!! Form::text('name', null, ['placeholder' => 'Name', 'class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="row">
                        <!-- col -->
                        <div class="col-lg-4">
                            <ul id="treeview1">
                                <li><a style="background-color: #419BB2" class="btn btn-danger py-1" href="#">{{ __('roles.permissions') }}</a>
                                    <ul>
                                        <li>
                                            @foreach ($permission as $value)
                                                <label style="font-size: 14px;color:#419BB2">{{ Form::checkbox('permission[]', $value->id, in_array($value->id, $rolePermissions) ? true : false, ['class' => 'name']) }}
                                                    {{ app()->getLocale() == 'ar' ? $value->name_ar : $value->name }}</label>
                                                <br />
                                            @endforeach
                                        </li>

                                    </ul>
                                </li>
                            </ul>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                            <button type="submit" class="btn btn-main-primary print-style">{{ __('roles.update') }}</button>
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
{!! Form::close() !!}
@endsection
@section('js')
<!-- Internal Treeview js -->
<script src="{{ URL::asset('assets/plugins/treeview/treeview.js') }}"></script>
<script>
        $(document).ready(function() {

            $(function() {
var timeout = 4000; // in miliseconds (3*1000)
$('.alert').delay(timeout).fadeOut(500);
});
    
        });
    </script>
@endsection
