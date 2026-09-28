@if (@isset($data) && !@empty($data) && count($data) >0 )
@php
$i=1;
@endphp
<div class="table-responsive">
<table class="table text-md-nowrap text-center our-table" id="example2" width="100%" style="border: 2px solid rgba(0,0,0,.3);">
                                        <col style="width:5%;font-weight: bold;">
                                        <col style="width:15%;font-weight: bold;">
                                        <col style="width:10%;font-weight: bold;">
                                        <col style="width:10%;font-weight: bold;">
                                        <col style="width:10%;font-weight: bold;">
                                        <col style="width:10%;font-weight: bold;">
                                        <col style="width:10%;font-weight: bold;">
                                        <col style="width:10%;font-weight: bold;">
                                        <col style="width:10%;font-weight: bold;">
                                        <col style="width:10%;font-weight: bold;">



                                        <thead style="background-color: #eeedf9;">
                                            <tr>
                                                <th style="font-size: 18px;font-weight: bold;" class="border-bottom-0">#</th>
                                                <th style="font-size: 18px;font-weight: bold;" class="border-bottom-0">{{__('home.acount_name')}} </th>
                                                <th style="font-size: 18px;font-weight: bold;" class="border-bottom-0">{{__('home.account_number')}} </th>
                                                <th style="font-size: 18px;font-weight: bold;" class="border-bottom-0">{{__('home.tybe')}} </th>
                                                <th style="font-size: 18px;font-weight: bold;" class="border-bottom-0">{{__('home.Master')}} </th>
                                                <th style="font-size: 18px;font-weight: bold;" class="border-bottom-0">{{__('home.Master_account')}} </th>
                                                <th style="font-size: 18px;font-weight: bold;" class="border-bottom-0">{{__('home.credit')}} </th>
                                                <th style="font-size: 18px;font-weight: bold;" class="border-bottom-0">{{__('home.debit')}}</th>
                                                <th style="font-size: 18px;font-weight: bold;" class="border-bottom-0">{{__('home.current balance')}} </th>
                                                <th style="font-size: 18px;font-weight: bold;" class="border-bottom-0" style="text-align:center">{{__('home.status_active')}}</th>
                                                <th style="font-size: 18px;font-weight: bold;" class="border-bottom-0" style="text-align:center">{{__('home.operations')}}</th>



                                            </tr>
                                        </thead>
                                        <tbody class="">
                                            <?php $i = 0;
                                            $blance_supplier = 0;
                                            $blance_customer = 0;
                                            $blance_response = 0;
                                            $blance_expanese = 0;
                                            foreach (App\Models\financial_accounts::where('parent_account_number', 8)->get() as $account) {
                                                $blance_expanese += $account->current_balance+$account->start_balance;
                                            }
                                            foreach (App\Models\financial_accounts::where('parent_account_number', 3)->get() as $account) {
                                                $blance_response += $account->current_balance+$account->start_balance;
                                            }
                                            foreach (App\Models\financial_accounts::where('parent_account_number', 1)->get() as $account) {
                                                $blance_customer += $account->current_balance+$account->start_balance;
                                            }
                                            foreach (App\Models\financial_accounts::where('parent_account_number', 2)->get() as $account) {
                                                $blance_supplier += $account->current_balance+$account->start_balance;
                                            }





                                            ?>
                                            @foreach($data as $account)
                                            <?php $i++ ?>

                                            <tr>
                                                <td style="font-size: 15px;font-weight: bold;" dir=ltr>{{$i}}</td>
                                                <td style="font-size: 15px;font-weight: bold;" data-target="product_name">{{App::getLocale()=='ar'?$account->name:$account->name_en}}</td>
                                                <td style="font-size: 15px;font-weight: bold;" data-target="numberofpice">{{$account->account_number }}</td>
                                                <td style="font-size: 15px;font-weight: bold;" data-target="numberofpice">{{App::getLocale()=='ar'?$account->acounts_type->name_ar:$account->acounts_type->name_en}}</td>

                                                <td style="font-size: 15px;font-weight: bold;" data-target="numberofpice">{{$account->is_parent==0?__('home.no'):__('home.yes') }}</td>
                                                <td style="font-size: 15px;font-weight: bold;" data-target="numberofpice">{{$account->parent_account_number!=NULL?App::getLocale()=='ar'?$account->parent_account->name :$account->parent_account->name_en:__('home.notfount')}}</td>
                                                                                            <td style="font-size: 15px;font-weight: bold;" data-target="numberofpice">{{$account->debtor_current }}</td>
                                                <td style="font-size: 15px;font-weight: bold;" data-target="numberofpice">{{$account->creditor_current }}</td>

<?php
$account->current_balance=round($account->debtor_current-$account->creditor_current,2);
?>
                                                @if($account->debtor_current-$account->creditor_current ==0)
                                                <td style="font-size: 15px;font-weight: bold;" data-target="numberofpice">{{__('home.Balanced')}}</td>
                                                @elseif($account->debtor_current-$account->creditor_current >0)
                                                <td style="font-size: 15px;font-weight: bold;" data-target="numberofpice">{{__('home.credit')}} ( {{$account->current_balance}} ) {{__('home.SAR')}}</td>
                                                @else
                                                <td style="font-size: 15px;font-weight: bold;" data-target="numberofpice">{{__('home.debit')}} ( {{$account->current_balance*-1}} ) {{__('home.SAR')}}</td>


                                                        @endif

                                              

                                                <td style="font-size: 15px;font-weight: bold;" data-target="product_name">{{$account->active==0?__('users.notactive'):__('users.active')}}</td>
                                                <td style="font-size: 15px;font-weight: bold;" data-target="numberofpice">

                                                    @if($account->orginal_id)

                                                    <center>
                                                        <p style="color:red">{{__('home.not_update_on_screen')}}</p>
                                                    </center>

                                                    @else
                                                    <div class="row" style="justify-content: center;">
                                                        <a style="background-color: grey;" href="" class="btn btn-sm btn-info" title="تعديل">{{__('users.update')}}<i class="las la-pen"></i></a>

                                                        @endif

                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>

                                    <br>
        <div class="justify-content-start" id="ajax_pagination_in_search">
            {{ $data->links() }}
        </div>



        @else
        <div class="alert alert-danger">
            {{__('home.notfounddata')}}
        </div>
        @endif