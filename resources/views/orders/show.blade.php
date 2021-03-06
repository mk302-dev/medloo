@extends('layouts.app')

@section('content')

    <div class="panel" xmlns="http://www.w3.org/1999/html">
        <div class="panel-body">
            <div class="invoice-masthead">
                <div class="invoice-text">
                    <h3 class="h1 text-thin mar-no text-primary">{{ __('Order Details') }}</h3>
                </div>
            </div>
            <div class="row">
                @php
                    $delivery_status = $order->orderDetails->first() ? $order->orderDetails->first()->delivery_status : "pending";
                    $payment_status = $order->orderDetails->first() ? $order->orderDetails->first()->payment_status : "unpaid";
                @endphp
                <div class="col-lg-offset-6 col-lg-3">
                    <label for=update_payment_status"">{{__('Payment Status')}}</label>
                    <select class="form-control demo-select2" data-minimum-results-for-search="Infinity"
                            id="update_payment_status">
                        <option value="paid" @if ($payment_status == 'paid') selected @endif>{{__('Paid')}}</option>
                        <option value="unpaid"
                                @if ($payment_status == 'unpaid') selected @endif>{{__('Unpaid')}}</option>
                    </select>
                </div>
                <div class="col-lg-3">
                    <label for=update_delivery_status"">{{__('Delivery Status')}}</label>
                    <select class="form-control demo-select2" data-minimum-results-for-search="Infinity"
                            id="update_delivery_status">
                        <option value="pending"
                                @if ($delivery_status == 'pending') selected @endif>{{__('Pending')}}</option>
                        <option value="on_review"
                                @if ($delivery_status == 'on_review') selected @endif>{{__('On review')}}</option>
                        <option value="on_delivery"
                                @if ($delivery_status == 'on_delivery') selected @endif>{{__('On delivery')}}</option>
                        <option value="delivered"
                                @if ($delivery_status == 'delivered') selected @endif>{{__('Delivered')}}</option>
                    </select>
                </div>
            </div>
            <hr>

            @if($order->orderDetails->first())

                <div class="row" id="ProcessShipping"
                     style="@if ($delivery_status != 'on_delivery') display: none;  @endif">

                    @php
                        $order_shipment_id = $order->orderDetails()->first()->order_shipment_id;
                        $dimension = App\OrderShipment::find($order_shipment_id);
                    @endphp
                    <h4 class="panel-body">Set Courier Dimension</h4>
                    <div class="col-lg-10">
                        <div class="col-lg-3">
                            <label>Length</label>
                            <input type="text" name="length" required class="form-control"
                                   value="{{ $dimension['length'] ?? 0 }}">
                        </div>

                        <div class="col-lg-3">
                            <label>Breadth</label>
                            <input type="text" name="breadth" required class="form-control"
                                   value="{{ $dimension['breadth'] ?? 0 }}">
                        </div>

                        <div class="col-lg-3">
                            <label>Height</label>
                            <input type="text" name="height" required class="form-control"
                                   value="{{ $dimension['height'] ?? 0 }}">
                        </div>

                        <div class="col-lg-3">
                            <label>Weight</label>
                            <input type="text" name="weight" required class="form-control"
                                   value="{{ $dimension['weight'] ?? 0 }}">
                        </div>
                    </div>

                    <div class="col-lg-2">
                        <label for=update_payment_status""></label>
                        <input type="button" onclick="ProcessShipping()" name="length" class="form-control btn btn-info"
                               value="Process Shipping" @if($order_shipment_id) disabled @endif">
                    </div>
                </div>
            @endif

            <hr>
            <div class="invoice-bill row">
                <div class="col-sm-6 text-xs-center">
                    <address>
                        <strong class="text-main">{{ json_decode($order->shipping_address)->name }}</strong><br>
                        {{ json_decode($order->shipping_address)->email }}<br>
                        {{ json_decode($order->shipping_address)->phone }}<br>
                        {{ json_decode($order->shipping_address)->address }}
                        , {{ json_decode($order->shipping_address)->city }}
                        , {{ json_decode($order->shipping_address)->country }}
                    </address>
                    @if ($order->manual_payment && is_array(json_decode($order->manual_payment_data, true)))
                        <br>
                        <strong class="text-main">{{ __('Payment Information') }}</strong><br>
                        Name: {{ json_decode($order->manual_payment_data)->name }},
                        Amount: {{ single_price(json_decode($order->manual_payment_data)->amount) }}, TRX
                        ID: {{ json_decode($order->manuinvoice-detailsal_payment_data)->trx_id }}
                        <br>
                        <a href="{{ asset(json_decode($order->manual_payment_data)->photo) }}" target="_blank"><img
                                src="{{ asset(json_decode($order->manual_payment_data)->photo) }}" alt="" height="100"></a>
                    @endif
                </div>
                <div class="col-sm-6 text-xs-center">
                    <table class="invoice-details">
                        <tbody>
                        <tr>
                            <td class="text-main text-bold">
                                {{__('Order #')}}
                            </td>
                            <td class="text-right text-info text-bold">
                                {{ $order->code }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-main text-bold">
                                {{__('Order Status')}}
                            </td>
                            @php
                                $status = $order->orderDetails->first() ? $order->orderDetails->first()->delivery_status : "pending";
                            @endphp
                            <td class="text-right">
                                @if($status == 'delivered')
                                    <span
                                        class="badge badge-success">{{ ucfirst(str_replace('_', ' ', $status)) }}</span>
                                @else
                                    <span class="badge badge-info">{{ ucfirst(str_replace('_', ' ', $status)) }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-main text-bold">
                                {{__('Order Date')}}
                            </td>
                            <td class="text-right">
                                {{ date('d-m-Y h:i A', $order->date) }} (UTC)
                            </td>
                        </tr>
                        <tr>
                            <td class="text-main text-bold">
                                {{__('Total amount')}}
                            </td>
                            <td class="text-right">
                                {{ single_price($order->orderDetails->sum('price') + $order->orderDetails->sum('tax')) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-main text-bold">
                                {{__('Payment method')}}
                            </td>
                            <td class="text-right">
                                {{ ucfirst(str_replace('_', ' ', $order->payment_type)) }}
                            </td>
                        </tr>

                        @if($order->prescriptionOrder()->first())
                            @php
                                $action_id = $order->prescriptionOrder()->first()->action_id;
                            @endphp
                            <tr>
                                <td class="text-main text-bold">
                                    {{__('Prescription Action')}}
                                </td>
                                <td class="text-right">
                                    @if($action_id == '1')
                                        {{ __('Order everything as per prescription') }}
                                    @elseif($action_id == '2')
                                        {{ __('Search and add medicines to cart') }}
                                    @else
                                        {{ __('Call me for details') }}
                                    @endif

                                </td>
                            </tr>

                            @if($action_id == "1")
                                <tr>
                                    <td class="text-main text-bold">
                                        {{__('Duration')}}
                                    </td>
                                    <td class="text-right">
                                        {{ $order->prescriptionOrder()->first()->duration }}
                                    </td>
                                </tr>
                            @endif
                        @endif

                        </tbody>
                    </table>
                </div>
            </div>
            <hr class="new-section-sm bord-no">

            @if($prescriptionOrder = $order->prescriptionOrder()->first())
                <h4>Prescription images</h4>
                <div class="row">
                    <div class="col-lg-12 table-responsive">
                        <table class="table table-bordered invoice-summary">
                            <thead>
                            <tr class="bg-trans-dark">
                                <th class="min-col">#</th>
                                <th width="10%">
                                    {{__('Photo')}}
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach(unserialize($prescriptionOrder->images) as $key => $image)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>
                                        <a href="{{ $prescriptionOrder->setImagePath($image) }}"
                                           target="_blank"><img height="50"
                                                                src={{ $prescriptionOrder->setImagePath($image) }}></a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>



            @endif


            @if($order->orderDetails()->count() > 0)
                <h4>Product details</h4>
                <div class="row">
                    <div class="col-lg-12 table-responsive">
                        <table class="table table-bordered invoice-summary">
                            <thead>
                            <tr class="bg-trans-dark">
                                <th class="min-col">#</th>
                                <th width="10%">
                                    {{__('Photo')}}
                                </th>
                                <th class="text-uppercase">
                                    {{__('Description')}}
                                </th>
                                <th class="text-uppercase">
                                    {{__('Delivery Type')}}
                                </th>
                                <th class="min-col text-center text-uppercase">
                                    {{__('Qty')}}
                                </th>
                                <th class="min-col text-center text-uppercase">
                                    {{__('Price')}}
                                </th>
                                <th class="min-col text-right text-uppercase">
                                    {{__('Total')}}
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @php
                                $admin_user_id = \App\User::where('user_type', 'admin')->first()->id;
                            @endphp
                            @foreach ($order->orderDetails->where('seller_id', $admin_user_id) as $key => $orderDetail)
                                <tr>
                                    <td>{{ $key+1 }}</td>
                                    <td>
                                        @if ($orderDetail->product != null)
                                            <a href="{{ route('product', $orderDetail->product->slug) }}"
                                               target="_blank"><img height="50"
                                                                    src={{ asset($orderDetail->product->thumbnail_img) }}/></a>
                                        @else
                                            <strong>{{ __('N/A') }}</strong>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($orderDetail->product != null)
                                            <strong><a
                                                    href="{{ route('product', $orderDetail->product->slug) }}"
                                                    target="_blank">{{ $orderDetail->product->name }}</a></strong>
                                            <small>{{ $orderDetail->variation }}</small>
                                        @else
                                            <strong>{{ __('Product Unavailable') }}</strong>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($orderDetail->shipping_type != null && $orderDetail->shipping_type == 'home_delivery')
                                            {{ __('Home Delivery') }}
                                        @elseif ($orderDetail->shipping_type == 'pickup_point')
                                            @if ($orderDetail->pickup_point != null)
                                                {{ $orderDetail->pickup_point->name }} ({{ __('Pickup Point') }}
                                                )
                                            @else
                                                {{ __('Pickup Point') }}
                                            @endif
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        {{ $orderDetail->quantity }}
                                    </td>
                                    <td class="text-center">
                                        {{ single_price($orderDetail->price/$orderDetail->quantity) }}
                                    </td>
                                    <td class="text-center">
                                        {{ single_price($orderDetail->price) }}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="clearfix">
                    <table class="table invoice-total">
                        <tbody>
                        <tr>
                            <td>
                                <strong>{{__('Sub Total')}} :</strong>
                            </td>
                            <td>
                                {{ single_price($order->orderDetails->where('seller_id', $admin_user_id)->sum('price')) }}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong>{{__('Tax')}} :</strong>
                            </td>
                            <td>
                                {{ single_price($order->orderDetails->where('seller_id', $admin_user_id)->sum('tax')) }}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong>{{__('Shipping')}} :</strong>
                            </td>
                            <td>
                                {{ single_price($order->orderDetails->where('seller_id', $admin_user_id)->sum('shipping_cost')) }}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong>{{__('TOTAL')}} :</strong>
                            </td>
                            <td class="text-bold h4">
                                {{ single_price($order->orderDetails->where('seller_id', $admin_user_id)->sum('price') + $order->orderDetails->where('seller_id', $admin_user_id)->sum('tax') + $order->orderDetails->where('seller_id', $admin_user_id)->sum('shipping_cost')) }}
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="text-right no-print">
                    <a href="{{ route('seller.invoice.download', $order->id) }}" class="btn btn-default"><i
                            class="demo-pli-printer icon-lg"></i></a>
                </div>
            @endif
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">

        $('#update_delivery_status').on('change', function () {
            var order_id = {{ $order->id }};
            var status = $('#update_delivery_status').val();

            if (status == "on_delivery") {
                $('#ProcessShipping').show();
                showAlert('info', 'Set courier dimension for delivery');
                return true;
            } else {
                $('#ProcessShipping').hide();
            }


            $.post('{{ route('orders.update_delivery_status') }}', {
                _token: '{{ @csrf_token() }}',
                order_id: order_id,
                status: status
            }, function (data) {
                showAlert('success', 'Delivery status has been updated');
            });
        });

        function ProcessShipping() {
            var order_id = {{ $order->id }};
            var status = $('#update_delivery_status').val();

            //set courier dimension
            var length = $('input[name=length]').val();
            if (!length) {
                showAlert('danger', 'Length field is required');
            }
            var breadth = $('input[name=breadth]').val();
            if (!breadth) {
                showAlert('danger', 'Breadth field is required');
            }
            var height = $('input[name=height]').val();
            if (!height) {
                showAlert('danger', 'Height field is required');
            }
            var weight = $('input[name=weight]').val();
            if (!weight) {
                showAlert('danger', 'Weight field is required');
            }

            if (!length || !breadth || !height || !weight) {
                return true;
            }

            $.post('{{ route('orders.update_delivery_status') }}', {
                _token: '{{ @csrf_token() }}',
                order_id: order_id,
                status: status,
                dimension: {
                    length: length,
                    breadth: breadth,
                    height: height,
                    weight: weight
                }
            }, function (data) {
                showAlert('success', 'Delivery status has been updated');
            });
        }

        $('#update_payment_status').on('change', function () {
            var order_id = {{ $order->id }};
            var status = $('#update_payment_status').val();
            $.post('{{ route('orders.update_payment_status') }}', {
                _token: '{{ @csrf_token() }}',
                order_id: order_id,
                status: status
            }, function (data) {
                showAlert('success', 'Payment status has been updated');
            });
        });
    </script>
@endsection
