@if(isset($data) && $data->count() > 0)

@php
    $current_balance = $previous_balance; // الرصيد الابتدائي للصفحة
@endphp

<div class="table-responsive">
    <table class="table text-md-nowrap text-center our-table" width="100%" style="border: 2px solid rgba(0,0,0,.3);">
        <thead>
            <tr>
                <th>{{ __('home.no_liquidation') }}</th>
                <th>{{ __('users.username') }}</th>
                <th>{{ __('home.date') }}</th>
                <th>{{ __('home.branch') }}</th>
                <th>{{ __('home.type_liquition') }}</th>
                <th>{{ __('home.status_liquidation') }}</th>
                <th>{{ __('home.Historyـofـproductـsales') }}</th>
                <th>{{ __('home.amount_liquition') }}</th>
                <th>{{ __('home.current balance') }}</th>
                <th>{{ __('home.operations') }}</th>
            </tr>
        </thead>
        <tbody>

        @php
            $total_before = 0;
            $total = 0;
        @endphp

        @foreach ($data as $product)
            @php
                $inside = in_array($product->type,[3,7,9]) ? $product->price_filtering : 0;
                $outside = !in_array($product->type,[3,7,9]) ? $product->price_filtering : 0;

                // الرصيد الحالي للصف

                // تحديث الرصيد للصف التالي
                $current_balance += $inside - $outside;
                $display_balance = $current_balance;

                // تحديث المجاميع للإجمالي
                $total_before += $inside;
                $total += $outside;
            @endphp

            <tr>
                <td>{{ $product->id }}</td>
                <td>{{ $product->user->name ?? '-' }}</td>
                <td>{{ $product->created_at }}</td>
                <td>{{ $product->branch->name ?? '-' }}</td>

                {{-- نوع العملية --}}
                <td>
                    @switch($product->type)
                        @case(1)<span class="text-warning fw-bold">{{ __('home.liquidation_shipments') }}</span>@break
                        @case(2)<span class="text-success fw-bold">{{ __('home.liquidation_purchase') }}</span>@break
                        @case(3)<span class="text-success fw-bold">{{ __('home.Historyـofـproductـsales') }}</span>@break
                        @case(7)@case(8)<span class="text-success fw-bold">{{ __('home.Daily_record') }}</span>@break
                        @case(9)@case(10)<span class="text-success fw-bold">{{ __('home.Opening_entry') }}</span>@break
                        @default - 
                    @endswitch
                </td>

                {{-- الحالة --}}
                <td>
                    @if($product->status == 1)
                        <span class="text-warning fw-bold">{{ __('home.data_complete') }}</span>
                    @elseif($product->status == 2)
                        <span class="text-success fw-bold">{{ __('home.confirm_done') }}</span>
                    @elseif($product->status == 3)
                        <span class="text-info fw-bold">{{ __('home.pratiail_of_review') }}</span>
                    @endif
                </td>

                {{-- الداخل --}}
                <td>{{ number_format($inside,2) }}</td>

                {{-- الخارج --}}
                <td>{{ number_format($outside,2) }}</td>

                {{-- الرصيد الحالي --}}
                <td>{{ number_format($display_balance,2) }}</td>

                {{-- العمليات --}}
                <td>
                    {{-- Show --}}
                    @if($product->type == 1)
                        <a class="btn btn-danger btn-sm mb-1" href="{{ url('print_transfers_after_full/'.$product->id) }}">{{ __('home.show') }}</a>
                    @elseif($product->type == 2)
                        <a class="btn btn-danger btn-sm mb-1" href="{{ url('print_full_purchase/'.$product->id) }}">{{ __('home.show') }}</a>
                    @endif

                    {{-- Update --}}
                    @if($product->type == 1 && $product->status != 2)
                        <a class="btn btn-danger btn-sm mb-1" href="{{ url('update_datials__liquidation/'.$product->id) }}">{{ __('home.update_datials__liquidation') }}</a>
                    @elseif($product->type == 2 && $product->status != 2)
                        <a class="btn btn-danger btn-sm mb-1" href="{{ url('update_data_purchase/'.$product->id) }}">{{ __('home.update_data_purchase') }}</a>
                    @endif

                    {{-- Cancel Confirm و Delete --}}
                    {{-- [مراجعة] إلغاء التأكيد للشحنات/المشتريات بس (على التحويل كان بيعكس حساب العهدة بالغلط)،
                         والحذف مش متاح للقيد الافتتاحي من هنا --}}
                    @if(Auth()->user()->branchs_id == 9)
                        @if(in_array($product->type, [1, 2]) && in_array($product->status, [2, 3]))
                            <a class="btn btn-sm btn-danger mb-1" data-toggle="modal" data-effect="effect-scale" href="#cancel_confirm_modolla" data-id="{{ $product->id }}">{{ __('home.cancel_confirm') }}</a>
                        @endif
                        @if(!in_array($product->type, [9, 10]) && ($product->status == 2 || in_array($product->type, [1, 2])))
                            <a class="btn btn-sm btn-danger mb-1" data-toggle="modal" data-effect="effect-scale" href="#delete_quotation" data-id="{{ $product->id }}">{{ __('home.delete') }}</a>
                        @endif
                    @endif

                    {{-- Print للنوع 3 --}}
                    @if($product->type == 3)
                        <form action="{{ url('print_transfers') }}" method="POST" autocomplete="off">
                            @csrf
                            <input type="hidden" name="id" value="{{ $product->id_trasction }}">
                            <button type="submit" class="btn btn-success btn-sm mt-1">{{ __('home.print') }}</button>
                        </form>
                    @endif
                </td>
            </tr>
        @endforeach


        </tbody>
    </table>

    <div class="justify-content-start" id="ajax_pagination_in_search">
        {{ $data->links() }}
    </div>
</div>

@else
<div class="alert alert-danger">
    {{ __('home.notfounddata') }}
</div>
@endif
