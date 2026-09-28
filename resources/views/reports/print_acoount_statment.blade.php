@extends('layouts.master')
@section('css')
<style>
    @media print {
        #print_Button {
            display: none;
        }
    }

    body {
        font: 13pt Georgia, "Times New Roman", Times, serif;
        line-height: 1.5;
        border-style: solid;

    }

    /* ===== تصفية grouping styles ===== */
    tr.group-header-row td {
        background-color: #e9f3f6;
        font-weight: bold;
        text-align: right;
    }

    tr.group-header-row .group-toggle {
        width: 18px;
        height: 18px;
        vertical-align: middle;
        margin-left: 8px;
        cursor: pointer;
    }

    tr.group-highlighted {
        background-color: #fff3b0 !important;
    }

    tr.group-total-row td {
        background-color: #f3f3f3;
        border-top: 2px solid rgba(0,0,0,.3);
    }

    @media print {
        tr.group-header-row .group-toggle {
            -webkit-print-color-adjust: exact;
        }
    }
</style>
@endsection
@section('title')
{{ __('home.print') }}
@stop
@section('page-header')
<!-- breadcrumb -->
<div class="breadcrumb-header justify-content-between">
</div>
<!-- breadcrumb -->
@endsection
@section('content')
<!-- row -->
<div class="row row-sm">
    <div class="col-md-12 col-xl-12">
        <div class=" main-content-body-invoice" id="print">
            <div class="card card-invoice">
                   <div class="d-flex justify-content-center">
                       <div class="row">


                                  <button class="btn btn-danger print-style float-left mt-3 mr-2" id="print_Button" onclick="printDiv()">
                                {{ __('home.print') }}
                                <i class="mdi mdi-printer ml-1"></i>
                                <br>


                        </div>

                       </div>

                        <br>
                                <div>
                                                <center><a style="background-color: #419BB2;font-size:15px;width: 120px!important;height:30px" href="{{ url('/' . ($page = 'generate_customer_statment_pdf') . '/' .$account_id . '/' . $start_at . '/' . $end_at) }}"
                    class="btn btn-success p-1 px-2 fw-bolder"  id="generate_pdf" target="_blank" >{{ __('home.dwonloadpdf') }}&nbsp;<i class="fa-solid fa-download"></i></i></a>
</center>


                                </div>

                <div class="card-body">
                                           <div class="invoice-header" style="display: flex;justify-content:space-between;width:100%" dir=rtl>




                        <div class="billed-from" style="width:33%;text-align: center;">
                            <br>

                            <span class="thick" style="font-size:18px">{{Namear}}</span>
                            <br>
                            <p class="tx-16 thick"> {{describtionar}}</p>
                                                        <p class="tx-16 thick">{{STar}}</p>




                            <p class="tx-16 thick">{{Taxar}}</p>

                        </div><!-- billed-from -->
                        <div >
                            <?php
                            $logo = camplogo;
                            ?>

                            <a href="https://ebdeasoft.com/"><img src="{{ asset('assets\img\brand').'/'.$logo }}" class="logo-1" alt="logo" style="width: 150px; height: 150px;"></a>



                        </div>

                        <div class="billed-from" style="width:33%;text-align: center;">
                            <br>

                            <span class="thick" style="font-size:19px">{{Nameen}}</span>
                            <br>
                                                            <p class="tx-16 thick" > {{describtionen}} </p>

                            <span class="tx-16 thick">{{STen}} </span>

                            <p class="tx-16 thick"> {{Taxen}} </p>

                        </div>

                    </div><!-- invoice-header -->

                    <div class="card-body">
                        <br>
                        <br>
                        <center>

                       <span style="font-size: 24px;color:black;  font-weight:bold">{{ __('home.account_statement') }}</span>


                        </center>
                        <br>

                                <?php
                                $currentdata = \Carbon\Carbon::now()->addHours(3)->format("Y-m-d H:i:s");

                                ?>


                              <div style="border-radius: 10px" class="card m-3 p-3">
                        <div class="table-padding">

                            <table style="border: 2px solid rgba(0,0,0,.3)" class="table table-striped table-bordered text-center my-2">
                                <thead>

                                    <tr>
                                         <th style="font-size: 13px;color:#419BB2">{{ __('home.account_statement') }}</th>
                                        <th style="font-size: 13px;color:#419BB2">{{ $account_name}}<br>{{$branch_name}}</th>
                                        <th style="font-size: 13px;color:#419BB2">{{ __('report.from') }}</th>
                                        <th style="font-size: 13px;color:#419BB2">{{ $start_at}}</th>
                                        <th style="font-size: 13px;color:#419BB2">{{ __('report.to') }}</th>
                                        <th style="font-size: 13px;color:#419BB2"> {{ $end_at }}</th>
                                        <th style="font-size: 13px;color:#419BB2">{{ __('home.exportTime') }}</th>
                                        <th style="font-size: 13px;color:#419BB2">{{ $currentdata}}</th>
                                    </tr>

                                </thead>
                            </table>
                        </div>
                        <br>
                    </div>
                        <br>
                        <div class="">
                            <div class="card-body">
                                <div class="">
                                    <div class="">
                                        <table class="table table-hover table-bordered table-striped text-cneter ">

                                            <thead>
                                                <tr>
                                                    <th class="border-bottom-0">#</th>
                                                    <th class="border-bottom-0">{{ __('home.decoumentNo') }}</th>
                                                    <th class="border-bottom-0">{{ __('home.exportTime') }}</th>
                                                    <th class="border-bottom-0">{{ __('report.date') }}</th>
                                                    <th class="border-bottom-0"> {{ __('users.branch') }}</th>
                                                    <th class="border-bottom-0"> {{ __('home.employee') }}</th>
                                                    <th class="border-bottom-0">{{ __('accountes.Theamountpaid') }}</th>
                                                    <th class="border-bottom-0">{{ __('home.credit') }}</th>
                                                    <th class="border-bottom-0">{{ __('home.debit') }}</th>
                                                    <th class="border-bottom-0">{{ __('home.current balance') }}</th>
                                                    <th class="border-bottom-0">{{ __('home.notesClient') }}</th>
                                                </tr>

                                            </thead>
                                            <?php
                                            $i = 1;
                                            $total_credit=$credit;
                                            $total_debit=$debit;
                                            $end_blance=0;

                                            // عدد أعمدة الجدول، يستخدم في صف عنوان كل جروب تصفية
                                            $statement_columns_count = 11;

                                            // رقم التصفية الحالي، بيتغيّر كل ما نطلع من جروب تصفية لتصفية تانية
                                            $prevTasfeyaGroup = null;

                                            // مجموع الدائن والمدين لصفوف التصفية الحالية، بيتصفر كل ما نبدأ تصفية جديدة
                                            $groupSumCredit = 0;
                                            $groupSumDebit = 0;

                                            /**
                                             * بيحاول يستخرج رقم التصفية من نص note
                                             * بيشتغل مع أي نص قبل كلمة "تصفية" زي "مرتجع تصفية رقم : 1037"
                                             * وبيرجع null لو النوت مش خاص بتصفية (زي "مشتريات رقم : 1044")
                                             */
                                            function extractTasfeyaGroup($note)
                                            {
                                                if (!$note) {
                                                    return null;
                                                }
                                                // \D*? بتتعامل مع أي فواصل بين "رقم" والرقم نفسه
                                                // زي : أو | أو مسافات، وبتتجاهل تمامًا أي نص قبل كلمة "تصفية"
                                                // (مثال: "مشتريات رقم : 1044 | تصفية رقم | : 1037" هتاخد 1037)
                                                if (preg_match('/تصفية\s*رقم\D*?(\d+)/u', $note, $matches)) {
                                                    return $matches[1];
                                                }
                                                return null;
                                            }
                                            ?>

                                            <tbody>


                                                 <tr>

                                                    <td>{{ $i }}</td>
                                                    <td>-</td>
                                                    <td>-</td>
                                                    <td>-</td>
                                                    <td>-</td>
                                                    <td>-</td>
                                                    <td>-</td>

                                                    <td>{{ round($debit,2)}}</td>
                                                    <td>{{ round($credit,2)}}</td>
                                              @if($total_debit-$total_credit ==0)
                                                <td style="font-size: 15px;font-weight: bold;" data-target="numberofpice">{{__('home.Balanced')}}</td>
                                                @elseif($total_debit-$total_credit >0)
                                                <td style="font-size: 15px;font-weight: bold;" data-target="numberofpice">{{__('home.credit')}} ( {{round($total_debit-$total_credit,2)}} ) {{__('home.SAR')}}</td>
                                                @else
                                                <td style="font-size: 15px;font-weight: bold;" data-target="numberofpice">{{__('home.debit')}} ( {{round(($total_debit-$total_credit)*-1,2)}} ) {{__('home.SAR')}}</td>
                                                @endif
                                                    <td>{{ __('home.oping') }}</td>


                                                </tr>
                                                    @foreach ($data as $invoice)
                                                      <?php

                                                      $total_credit+=$invoice['credit']  ;
                                                      $total_debit+=$invoice['depit']  ;
                                                      $end_blance= round($invoice['current_blance'],2);
                                            $i++;

                                            $currentTasfeyaGroup = extractTasfeyaGroup($invoice['note'] ?? null);
                                            ?>
                                            @if($prevTasfeyaGroup !== null && $currentTasfeyaGroup !== $prevTasfeyaGroup)
                                                {{-- قفلنا جروب التصفية اللي فات، نطلع صف المجموع بتاعه --}}
                                                <tr class="group-total-row" data-group="{{ $prevTasfeyaGroup }}">
                                                    <td colspan="6" style="text-align:right;font-weight:bold;">{{ __('home.settlement_total') }} {{ $prevTasfeyaGroup }}</td>
                                                    <td>-</td>
                                                    <td style="font-weight:bold;">{{ round($groupSumDebit,2) }}</td>
                                                    <td style="font-weight:bold;">{{ round($groupSumCredit,2) }}</td>
                                                    <td>-</td>
                                                    <td>-</td>
                                                </tr>
                                                <?php $groupSumCredit = 0; $groupSumDebit = 0; ?>
                                            @endif
                                            @if($currentTasfeyaGroup !== null && $currentTasfeyaGroup !== $prevTasfeyaGroup)
                                                <tr class="group-header-row">
                                                    <td colspan="{{ $statement_columns_count }}">
                                                        <input type="checkbox" class="group-toggle" id="group-check-{{ $currentTasfeyaGroup }}" data-group-target="{{ $currentTasfeyaGroup }}">
                                                        <label for="group-check-{{ $currentTasfeyaGroup }}" style="margin:0;cursor:pointer;">
                                                            {{ __('home.settlement_no') }} : {{ $currentTasfeyaGroup }}
                                                        </label>
                                                    </td>
                                                </tr>
                                            @endif
                                            <?php
                                            $prevTasfeyaGroup = $currentTasfeyaGroup;
                                            if ($currentTasfeyaGroup !== null) {
                                                $groupSumCredit += $invoice['credit'];
                                                $groupSumDebit += $invoice['depit'];
                                            }
                                            ?>
                                                <tr @if($currentTasfeyaGroup !== null) data-group="{{ $currentTasfeyaGroup }}" @endif>

                                                    <td>{{ $i }}</td>
                                                    <td>{{ $invoice['id']}}</td>
                                                    <td>{{ $invoice['date'] }}</td>
                                                    <td>{{ $invoice['date_export'] }}</td>
                                                    <td>{{ $invoice['branch'] }}</td>
                                                    <td>{{ $invoice['user'] }}</td>
                                                    <td>{{ $invoice['recive_amount'] }}</td>
                                                    <td>{{ round($invoice['depit'],2) }}</td>
                                                    <td>{{ round($invoice['credit'],2) }}</td>
                                            @if(round($total_debit-$total_credit,2) ==0)
                                                <td style="font-size: 15px;font-weight: bold;" data-target="numberofpice">{{__('home.Balanced')}}</td>
                                                @elseif(round($total_debit-$total_credit,2) >0)
                                                <td style="font-size: 15px;font-weight: bold;" data-target="numberofpice">{{__('home.credit')}} ( {{round($total_debit-$total_credit,2)}} ) {{__('home.SAR')}}</td>
                                                @else
                                                <td style="font-size: 15px;font-weight: bold;" data-target="numberofpice">{{__('home.debit')}} ( {{round(($total_debit-$total_credit)*-1,2)}} ) {{__('home.SAR')}}</td>
                                                @endif              <td>{{ $invoice['note'] }}</td>


                                                </tr>
                                                  @endforeach
                                            @if($prevTasfeyaGroup !== null)
                                                {{-- آخر تصفية في الجدول، نطلع صف المجموع بتاعها بعد آخر صف فيها --}}
                                                <tr class="group-total-row" data-group="{{ $prevTasfeyaGroup }}">
                                                    <td colspan="6" style="text-align:right;font-weight:bold;">{{ __('home.settlement_total') }} {{ $prevTasfeyaGroup }}</td>
                                                    <td>-</td>
                                                    <td style="font-weight:bold;">{{ round($groupSumDebit,2) }}</td>
                                                    <td style="font-weight:bold;">{{ round($groupSumCredit,2) }}</td>
                                                    <td>-</td>
                                                    <td>-</td>
                                                </tr>
                                            @endif
           <tr>

                                                    <td>{{ $i }}</td>
                                                    <td>-</td>
                                                    <td>-</td>
                                                    <td>-</td>
                                                    <td>-</td>
                                                    <td>-</td>
                                                    <td>-</td>
                                                    <td>{{ round($total_debit,2)}}</td>
                                                    <td>{{ round($total_credit,2)}}</td>
                                                @if(round($total_debit-$total_credit,2) ==0)
                                                <td style="font-size: 15px;font-weight: bold;" data-target="numberofpice">{{__('home.Balanced')}}</td>
                                                @elseif($total_debit-$total_credit >0)
                                                <td style="font-size: 15px;font-weight: bold;" data-target="numberofpice">{{__('home.credit')}} ( {{round($total_debit-$total_credit,2)}} ) {{__('home.SAR')}}</td>
                                                @else
                                                <td style="font-size: 15px;font-weight: bold;" data-target="numberofpice">{{__('home.debit')}} ( {{round(($total_debit-$total_credit)*-1,2)}} ) {{__('home.SAR')}}</td>
                                                @endif                                                       <td>-</td>


                                                </tr>
                                            </tbody>
                                        </table>
                                        <br>


                                    </div>
                                </div>

                                <br>


                                <br>

                                <hr class="mg-b-40">





                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- COL-END -->
        </div>
        <!-- row closed -->
    </div>
    <!-- Container closed -->
</div>
<!-- main-content closed -->
@endsection
@section('js')
<!--Internal  Chart.bundle js -->
<script src="{{ URL::asset('assets/plugins/chart.js/Chart.bundle.min.js') }}"></script>


<script type="text/javascript">
    function printDiv() {
        var printContents = document.getElementById('print').innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
        location.reload();
    }

    // تظليل كل صفوف تصفية معينة لما تتحدد checkbox التصفية اللي فوقها
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.group-toggle').forEach(function (checkbox) {
            checkbox.addEventListener('change', function () {
                var groupId = this.getAttribute('data-group-target');
                document.querySelectorAll('tr[data-group="' + groupId + '"]').forEach(function (row) {
                    row.classList.toggle('group-highlighted', checkbox.checked);
                });
            });
        });
    });
</script>

@endsection