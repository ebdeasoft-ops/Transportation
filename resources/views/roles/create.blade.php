@extends('layouts.master')
@section('css')
    <!--Internal  Font Awesome -->
    <link href="{{ URL::asset('assets/plugins/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <!--Internal  treeview -->
    <link href="{{ URL::asset('assets/plugins/treeview/treeview-rtl.css') }}" rel="stylesheet" type="text/css" />
@section('title')
    {{ __('roles.add_permisssion') }}
@stop

@endsection
@section('page-header')
<div class="main-parent">
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between parent-heading">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ __('roles.add_permisssion') }}
                </h4>
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




    {!! Form::open(['route' => 'roles.store', 'method' => 'POST']) !!}
    <!-- row -->
    <div class="row">
        <div class="col-md-12">
            <div class="card mg-b-20 pt-5">
                <div class="card-body">
                    <div class="main-content-label mg-b-5">
                        <div class="col-xs-7 col-sm-7 col-md-7">
                            <div class="form-group">
                                <p> {{ __('roles.name_permission') }}</p>
                                {!! Form::text('name', null, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <!-- col -->
                        {{-- <div class="col-lg-4">
                            <ul id="treeview1">
                                <li><a href="#">{{ __('roles.permissions') }}</a>
                                    <ul>
                                </li>
                                @foreach ($permission as $value) --}}
                                    
                                            
                                                        
                    
                    
          
                                                            
                                        {{-- {{ Form::checkbox('permission[]', $value->id, false, ['class' => 'name']) }}
                                        {{ app()->getLocale() == 'ar' ? $value->name_ar : $value->name }} --}}


                                        <div class="col-lg-4">
                                            <ul id="treeview1">
                                                <li><a style="background-color: #419BB2" class="btn btn-danger py-1" href="#">{{ __('roles.permissions') }}</a>
                                                    <ul class="ffff">

                                                        <li>
                                                            @foreach ($permission as $value)
                                                            @if($value->name!='Create a new branch'&&$value->name!='Create a vendor')

                                                                <label style="font-size: 14px;color:#419BB2">{{ Form::checkbox('permission[]', $value->id, false, ['class' => 'name']) }}
                                                                    {{ app()->getLocale() == 'ar' ? $value->name_ar : $value->name }}</label>
                                                                <br />
                                                                @endif                

                                                            @endforeach
                                                        </li>
                                                        
                                                     
                                                        
                                                        

                                                        
                
                                                    </ul>
                                                </li>
                                            </ul>
                                        </div>
    
                                {{-- @endforeach
                                </li>

                            </ul>
                            </li>
                            </ul>
                        </div> --}}
                        <!-- /col -->
                        <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                            <button type="submit" class="btn btn-main-primary print-style p-1">
                                {{ __('home.Add') }}
                                <svg style="width: 20px" class="svg-icon-buttons" viewBox="0 0 20 20">
                                    <path fill="none" d="M7.629,14.566c0.125,0.125,0.291,0.188,0.456,0.188c0.164,0,0.329-0.062,0.456-0.188l8.219-8.221c0.252-0.252,0.252-0.659,0-0.911c-0.252-0.252-0.659-0.252-0.911,0l-7.764,7.763L4.152,9.267c-0.252-0.251-0.66-0.251-0.911,0c-0.252,0.252-0.252,0.66,0,0.911L7.629,14.566z"></path>
                                </svg>
                            </button>
                        </div>

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



$('#checkall:checkbox').change(function () {
   if($(this).attr("checked")) $('input:checkbox').attr('checked','checked');
   else $('input:checkbox').removeAttr('checked');
});​


</script>
<script>
        $(document).ready(function() {

            $(function() {
var timeout = 4000; // in miliseconds (3*1000)
$('.alert').delay(timeout).fadeOut(500);
});
    
        });
    </script>



@endsection
