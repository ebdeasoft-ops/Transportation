<table class="table text-md-nowrap text-center our-table" id="example12" data-page-length="50">
    <thead>
        <tr>
            <th class="border-bottom-0">{{ __('home.Invoice_no') }}</th>
            <th class="border-bottom-0">{{ __('home.clietName') }}</th>
            <th class="border-bottom-0">{{ __('home.date') }}</th>
            <th class="border-bottom-0">{{ __('home.branch') }}</th>
            <th class="border-bottom-0">{{ __('home.operations') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $product)
            <tr id="{{ $product->id }}">
                <td data-target="id">{{ $product->id }}</td>
                <td dir="ltr" data-target="id">{{ $product->customer->name }}</td>
                <td>{{ $product->created_at }}</td>
                <td>{{ $product->branch->name }}</td>
                <td>
                    <div class="d-flex justify-content-center flex-wrap" style="gap: 8px">
                        <a class="btn btn-sm btn-download modal-effect" data-effect="effect-scale"
                            href="{{ url('generate_pdf_qoute/' . $product->id) }}" target="_blank">
                            {{ __('home.dwonloadpdf') }}
                            <i class="fa-solid fa-download ml-1"></i>
                        </a>

                        <form
                            action="{{ url(Mcamara\LaravelLocalization\Facades\LaravelLocalization::getCurrentLocale() . '/' . 'print_order_perice_to_customer') }}"
                            method="POST" role="search" autocomplete="off">
                            {{ csrf_field() }}
                            <input type="hidden" name="OrderNoprint" value="{{ $product->id }}">
                            <button type="submit" class="btn btn-sm btn-show ">
                                {{ __('home.show') }}
                                <i class="fas fa-eye ml-1"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<br>

<div class="justify-content-start mt-3" id="ajax_pagination_in_search">
    {{ $data->links() }}
</div>