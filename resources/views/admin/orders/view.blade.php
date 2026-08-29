@extends('admin.layout.master')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/libs/swiper/swiper-bundle.min.css') }}">
@endpush
@section('content')

    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <a class="btn btn-dark" href="{{ route('admin-orders.index') }}">Back</a>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin-admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Order Details</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-9">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <h5 class="card-title flex-grow-1 mb-0">Order #{{ $order->order_number }}</h5>
                        <div class="flex-shrink-0">
                            @can('generate_invoice_order')
                                <a href="javascript:void(0);" class="btn btn-info btn-sm print-invoice-btn"
                                    data-id="{{ $order->id }}" title="Generate Invoice"><i
                                        class="ri-download-2-fill align-middle me-1"></i> Invoice</a>
                                {{-- <a href="{{ route('admin-orders.generate.invoice', $order->id) }}"
                                        class="btn btn-info btn-sm" target="_blank" title="Generate Invoice"><i
                                            class="ri-download-2-fill align-middle me-1"></i> Invoice</a> --}}
                            @endcan
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive table-card">
                        <?php
                        $SubTotal = '0';
                        $statuss = getOrderStatuss();
                        $cancelRequest = orderCancellationRequest($order->id);
                        $refundRequest = orderRefundRequest($order->id);
                        ?>
                        <table class="table table-nowrap align-middle table-borderless mb-0">
                            <thead class="table-light text-muted">
                                <tr>
                                    <th scope="col">
                                        <input type="checkbox" class="print-checkbox-select-all"> Product Details
                                    </th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Unit Price</th>
                                    <th scope="col">Quantity</th>
                                    <th scope="col">Discount</th>
                                    <th scope="col">GST</th>


                                    <th scope="col" class="text-end">Total Amount <br> (Including GST)</th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($order->items as $index => $item)
                                    @php
                                        $vSku = '';
                                        if (!empty($item->product_variant_combination_id)) {
                                            $productV = getProductVariantSku($item->product_variant_combination_id);
                                            $vSku = $productV->sku;
                                            //prx($orderstatushistory->where('order_item_id',111)->firstWhere('order_status_id', 2));
                                            $statusHistory = $orderstatushistory
                                                ->where('order_item_id', $item->id)
                                                ->firstWhere('order_status_id', $item->order_status_id);
                                            // prx($statusHistory);
                                        }
                                    @endphp
                                    <tr>
                                        <td>

                                            <div class="d-flex">
                                                <div class="p-2">
                                                    <input type="checkbox" data-id="{{ $item->id }}"
                                                        class="print-checkbox">
                                                </div>
                                                <div class="flex-shrink-0 avatar-md bg-light rounded p-1">
                                                    @php
                                                        $isCancelRequest = !empty($cancelRequest[$item->id])
                                                            ? (array) $cancelRequest[$item->id]
                                                            : [];
                                                        $isRefundRequest = !empty($refundRequest[$item->id])
                                                            ? (array) $refundRequest[$item->id]
                                                            : [];

                                                        $combo = json_decode($item->combination, true);
                                                        $productimage = getProductImages($item->product_id, $combo);
                                                    @endphp
                                                    @if ($productimage && !empty($productimage->graphic))
                                                        <img src="{{ env('WEBSITE_URL') . '/uploads/products/' . $productimage->graphic }}"
                                                            alt="{{ $item->product->name }}" class="img-fluid d-block"
                                                            style="width:70px; height:70px;">
                                                    @else
                                                        {{-- Fallback image --}}
                                                        <img src="{{ asset('images/no-image.png') }}" alt="No image"
                                                            class="img-fluid d-block" style="width:70px; height:70px;">
                                                    @endif
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <h5 class="fs-14"><a href="#"
                                                            class="text-body">{{ isset($item->product) ? $item->product->name : 'N/A' }}</a>
                                                    </h5>

                                                    Colour: {{ isset($combo['colour']) ? $combo['colour'] : 'Null' }}
                                                    <br>
                                                    Size: {{ isset($combo['size']) ? $combo['size'] : 'Null' }}
                                                    <br>
                                                    @if (!empty($vSku))
                                                        SKU: {{ $item->product->sku . '_' . $vSku }}
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @php
                                                $statussSlug = '';
                                                if(!empty($statuss[$item->order_status_id]['slug'])){
                                                    $statussSlug = $statuss[$item->order_status_id]['slug'];    
                                                }
                                                $statusArr = [
                                                    'cancelled',
                                                    'delivered',
                                                    'refunded',
                                                    'cancelled_by_customer',
                                                ];
                                            @endphp
                                            @if (in_array($statussSlug, $statusArr))
                                                <p class="d-flex align-items-center gap-2">
                                                    <span>{{ $statussSlug == 'cancelled' ? 'Cancelled by Admin' : $statuss[$item->order_status_id]['name'] }}
                                                    </span>
                                                    @if (!empty($statusHistory->remark))
                                                        @php
                                                            $updatedBy = !empty($item->updatedBy)
                                                                ? 'Updated by: ' .
                                                                    $item->updatedBy->name .
                                                                    '<br>(' .
                                                                    $item->updatedBy->email .
                                                                    ')'
                                                                : '';
                                                        @endphp
                                                        <i style="padding: 0px 7px; background: #000;color: #fff;border-radius: 10px; font-weight: bold;cursor: pointer;display:block;height:18px;width:18px"
                                                            title="Admin Remark: {{ $statusHistory->remark }}<hr class='m-1'>{{ $updatedBy }} <hr class='m-1'>{{ $statusHistory->created_at->format('d M, Y h:i a') }}"
                                                            data-bs-html="true" data-bs-toggle="tooltip">i</i>
                                                    @endif

                                                    @if (!empty($isCancelRequest['reason']))
                                                        @php
                                                            $cancelReqStatus =
                                                                $isCancelRequest['status'] == 2
                                                                    ? 'Rejected'
                                                                    : 'Accepted';
                                                            $cancelUpdatedDateTime = !empty(
                                                                $isCancelRequest['updated_at']
                                                            )
                                                                ? date(
                                                                    'd M, Y h:i A',
                                                                    strtotime($isCancelRequest['updated_at']),
                                                                )
                                                                : '';
                                                            $updatedBy = !empty($isCancelRequest['admin'])
                                                                ? $isCancelRequest['admin']['name'] .
                                                                    ' (' .
                                                                    $isCancelRequest['admin']['email'] .
                                                                    ')<br>' .
                                                                    $cancelUpdatedDateTime
                                                                : 'Admin';
                                                        @endphp
                                                        <i style="padding: 0px 7px; background: #000;color: #fff;border-radius: 10px; font-weight: bold;cursor: pointer;display:block;height:18px;width:18px"
                                                            title="<strong>Cancel Request: {{ $cancelReqStatus }}</strong><br>User Remark: {{ $isCancelRequest['reason'] }} {{ !empty($isCancelRequest['admin_remark']) ? ' <hr class="py-1 pb-0 my-0"> Admin Remark: ' . $isCancelRequest['admin_remark'] : '' }} {{ !empty($isCancelRequest['refund_status']) ? ' <hr class="py-1 pb-0 my-0"> Payment: ' . $isCancelRequest['refund_status'] : '' }} {{ '<hr class="py-1 pb-0 my-0">Updated By: ' . $updatedBy }}"
                                                            data-bs-html="true" data-bs-toggle="tooltip">i</i>
                                                    @endif
                                                    @if (!empty($isRefundRequest['admin_remark']))
                                                        @php
                                                            $refundReqStatus =
                                                                $isRefundRequest['status'] == 2
                                                                    ? 'Rejected'
                                                                    : 'Accepted';
                                                            $returnUpdatedDateTime = !empty(
                                                                $isRefundRequest['updated_at']
                                                            )
                                                                ? '<br>' .
                                                                    date(
                                                                        'd M, Y h:i A',
                                                                        strtotime($isRefundRequest['updated_at']),
                                                                    )
                                                                : '';
                                                            $updatedBy = !empty($isRefundRequest['admin'])
                                                                ? $isRefundRequest['admin']['name'] .
                                                                    ' (' .
                                                                    $isRefundRequest['admin']['email'] .
                                                                    ')'
                                                                : 'Admin';
                                                        @endphp
                                                        <i style="padding: 0px 7px; background: #000;color: #fff;border-radius: 10px; font-weight: bold;cursor: pointer;display:block;height:18px;width:18px"
                                                            title="<strong>Refund Request: {{ $refundReqStatus }}</strong><br>{{ 'User Remark: ' . $isRefundRequest['refund_reason'] . '<hr class="py-1 pb-0 my-0">Admin Remark: ' . $isRefundRequest['admin_remark'] . '<hr class="py-1 pb-0 my-0"> Updated By: ' . $updatedBy . $returnUpdatedDateTime }}"
                                                            data-bs-html="true" data-bs-toggle="tooltip">i</i>
                                                    @endif
                                                </p>
                                            @elseif(isset($isCancelRequest['status']) && $isCancelRequest['status'] == 0)
                                                <p class="d-flex align-items-center gap-2">
                                                    <span>Cancel Request</span>
                                                    <i style="padding: 2px 8px; background: #000;color: #fff;border-radius: 10px; font-weight: bold;cursor: pointer;"
                                                        title="{{ $isCancelRequest['details'] }}"
                                                        data-bs-toggle="tooltip">i</i>
                                                </p>

                                                <div class="d-flex gap-2">
                                                    <button type="button" data-id="{{ $isCancelRequest['id'] }}"
                                                        data-status="1" data-request_type="cancel"
                                                        data-order_item_id="{{ $item->id }}"
                                                        class="btn btn-sm btn-success cancelRequestAction">Accept</button>

                                                    <button type="button" data-id="{{ $isCancelRequest['id'] }}"
                                                        data-status="2" data-request_type="cancel"
                                                        data-order_item_id="{{ $item->id }}"
                                                        class="btn btn-sm btn-danger cancelRequestAction">Reject</button>
                                                </div>
                                            @elseif(isset($isRefundRequest['status']) && $isRefundRequest['status'] == 0)
                                                <p class="d-flex align-items-center gap-2">
                                                    <span>Return Request</span>
                                                    <i style="padding: 2px 8px; background: #000;color: #fff;border-radius: 10px; font-weight: bold;cursor: pointer;"
                                                        title="{{ $isRefundRequest['refund_reason'] }}"
                                                        data-bs-toggle="tooltip">i</i>
                                                </p>

                                                <div class="d-flex gap-2">
                                                    <button type="button" data-id="{{ $isRefundRequest['id'] }}"
                                                        data-status="1" data-request_type="refund"
                                                        data-order_item_id="{{ $item->id }}"
                                                        class="btn btn-sm btn-success refundRequestAction">Accept</button>

                                                    <button type="button" data-id="{{ $isRefundRequest['id'] }}"
                                                        data-status="2" data-request_type="refund"
                                                        data-order_item_id="{{ $item->id }}"
                                                        class="btn btn-sm btn-danger refundRequestAction">Reject</button>
                                                </div>
                                            @else
                                                <div class="d-flex align-items-center gat-2">
                                                    @php
                                                    $odrStatus = 'pending';
                                                    if(is_numeric($item->order_status_id)){  
                                                        $odrStatus = $item->status;
                                                    } 
                                                    if(!empty($getStatusAccrdingtoOrderstatusForAdmin[$odrStatus])) {
                                                    @endphp
                                                    <select class="form-control itemstatus" data-order="{{ $item->id }}" data-status="{{ $item->order_status_id }}">  
                                                        <option value="">Select Status</option>
                                                        @foreach ($getStatusAccrdingtoOrderstatusForAdmin[$odrStatus] as $key => $value)
                                                            <option value="{{ $key }}" <?php if($odrStatus===$key) { echo 'selected'; } else { ''; } ?> >{{ $value }}</option>
                                                        @endforeach           
                                                    </select>
                                                    @php
                                                    } else {
                                                        echo  $odrStatus;
                                                    }
                                                    @endphp

                                                    @if (!empty($isRefundRequest['admin_remark']))
                                                        @php
                                                            $refundReqStatus =
                                                                $isRefundRequest['status'] == 2
                                                                    ? 'Rejected'
                                                                    : 'Accepted';
                                                            $refundUpdatedDateTime = !empty(
                                                                $isRefundRequest['updated_at']
                                                            )
                                                                ? '<br>' .
                                                                    date(
                                                                        'd M, Y h:i A',
                                                                        strtotime($isRefundRequest['updated_at']),
                                                                    )
                                                                : '';

                                                            $updatedBy = !empty($isRefundRequest['admin'])
                                                                ? $isRefundRequest['admin']['name'] .
                                                                    ' (' .
                                                                    $isRefundRequest['admin']['email'] .
                                                                    ').'
                                                                : 'Admin';
                                                        @endphp
                                                        <i style="padding: 0px 7px; background: #000;color: #fff;border-radius: 10px; font-weight: bold;cursor: pointer;display:block;height:18px;width:18px"
                                                            title="<strong>Refund Request: {{ $refundReqStatus }}</strong><br>{{ 'User Remark: ' . $isRefundRequest['refund_reason'] . '<hr class="py-1 pb-0 my-0">Admin Remark: ' . $isRefundRequest['admin_remark'] . '<hr class="py-1 pb-0 my-0"> Updated By: ' . $updatedBy . $refundUpdatedDateTime }}"
                                                            data-bs-html="true" data-bs-toggle="tooltip">i</i>
                                                    @elseif(!empty($isCancelRequest['reason']))
                                                        @php
                                                            $cancelReqStatus =
                                                                $isCancelRequest['status'] == 2
                                                                    ? 'Rejected'
                                                                    : 'Accepted';
                                                            $cancelUpdatedDateTime = !empty(
                                                                $isCancelRequest['updated_at']
                                                            )
                                                                ? '<br>' .
                                                                    date(
                                                                        'd M, Y h:i A',
                                                                        strtotime($isCancelRequest['updated_at']),
                                                                    )
                                                                : '';

                                                            $updatedBy = !empty($isCancelRequest['admin'])
                                                                ? $isCancelRequest['admin']['name'] .
                                                                    ' (' .
                                                                    $isCancelRequest['admin']['email'] .
                                                                    ').'
                                                                : 'Admin';
                                                        @endphp
                                                        <i style="padding: 0px 7px; background: #000;color: #fff;border-radius: 10px; font-weight: bold;cursor: pointer;display:block;height:18px;width:18px"
                                                            title="<strong>Cancel Request: {{ $cancelReqStatus }}</strong><br>User Remark: {{ $isCancelRequest['reason'] }} {{ !empty($isCancelRequest['admin_remark']) ? ' <hr class="py-1 pb-0 my-0"> Admin Remark: ' . $isCancelRequest['admin_remark'] : '' }} {{ !empty($isCancelRequest['refund_status']) ? ' <hr class="py-1 pb-0 my-0"> Payment: ' . $isCancelRequest['refund_status'] : '' }} {{ '<hr class="py-1 pb-0 my-0">Updated By: ' . $updatedBy . $cancelUpdatedDateTime }}"
                                                            data-bs-html="true" data-bs-toggle="tooltip">i</i>
                                                    @endif
                                                </div>
                                            @endif
                                        </td>
                                        <td>₹ {{ $item->selling_price }}</td>
                                        <td>{{ $item->qty }}</td>
                                        <td>₹ {{ $item->discount_amount ?? 0.0 }}</td>
                                        <td>₹ {{ $item->tax_amount ?? 0.0 }}</td>
                                        <td class="fw-medium text-end">
                                            ₹ {{ $item->total }} @php  $SubTotal += $item->total;  @endphp
                                        </td>
                                    </tr>
                                @endforeach
                                <tr class="border-top border-top-dashed">
                                    <td colspan="4"></td>
                                    <td colspan="2" class="fw-medium p-0">
                                        <table class="table table-borderless mb-0">
                                            <tbody>
                                                <tr>
                                                    <td>Sub Total :</td>
                                                    <td class="text-end">₹ {{ $SubTotal }}</td>
                                                </tr>
                                                @if (!$order->coupon_name == '')
                                                    <tr>
                                                        <td>Coupon Discount :({{ $order->coupon_name }})</td>
                                                        <td class="text-end">- ₹ {{ $order->coupon_discount }}</td>
                                                    </tr>
                                                @endif
                                                @if (!empty($order->shippingcharge) && $order->shippingcharge > 0)
                                                    <tr class="shipping-totals shipping">
                                                        <td>Shipping</td>
                                                        <td data-title="Shipping" class="text-end">₹
                                                            {{ $order->shippingcharge }}</td>
                                                    </tr>
                                                @else
                                                    <tr class="shipping-totals shipping">
                                                        <td>Shipping</td>
                                                        <td data-title="Shipping" class="text-end">Free</td>
                                                    </tr>
                                                @endif

                                                <tr class="border-top border-top-dashed">
                                                    <th scope="row">Total (INR) :</th>
                                                    <th class="text-end">₹ {{ $order->total }}</th>

                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!--end card-->
            <div class="card">
                <div class="card-header">
                    <div class="d-sm-flex align-items-center">
                        <h5 class="card-title flex-grow-1 mb-0">Order Status</h5>
                        <!-- <div class="flex-shrink-0 mt-2 mt-sm-0">
                                <a href="javascript:void(0);" class="btn btn-soft-info btn-sm mt-2 mt-sm-0"><i class="ri-map-pin-line align-middle me-1"></i> Change Address</a>
                                <a href="javascript:void(0);" class="btn btn-soft-danger btn-sm mt-2 mt-sm-0"><i class="mdi mdi-archive-remove-outline align-middle me-1"></i> Cancel Order</a>
                            </div> -->
                    </div>
                </div>
                <div class="card-body">
                    <div class="profile-timeline">
                        <div class="accordion accordion-flush" id="accordionFlushExample">
                            @if (!empty($orderstatushistory))
                                <div class="accordion" id="accordionExample">
                                    @foreach ($orderstatushistory as $key => $item)
                                        <div class="accordion-item border-0">
                                            <div class="accordion-header" id="heading{{ $key }}">
                                                <a class="accordion-button p-2 shadow-none" data-bs-toggle="collapse"
                                                    href="#collapse{{ $key }}"
                                                    aria-expanded="{{ $item->order_status == $order->order_status_id ? 'true' : 'false' }}"
                                                    aria-controls="collapse{{ $key }}">
                                                    <div class="d-flex align-items-center">
                                                        <div class="flex-shrink-0 avatar-xs">
                                                            <div class="avatar-title bg-success rounded-circle">
                                                                <i class="ri-shopping-bag-line"></i>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1 ms-3">
                                                            <h5 class="fs-14 mb-0" style="font-weight:bold">Order <span
                                                                    class="fw-high">{{ strtoupper($item->order_status) }}
                                                                    -
                                                                    {{ $item->updated_at->format('D, d M Y h:i A') }}</span>
                                                            </h5>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                            <div id="collapse{{ $key }}"
                                                class="accordion-collapse collapse @if ($item->order_status == $order->order_status_id) show @endif"
                                                aria-labelledby="heading{{ $key }}"
                                                data-bs-parent="#accordionExample">
                                                <div class="accordion-body ms-2 ps-5 pt-3">
                                                    <h6 class="mb-1">Status Update:</h6>
                                                    <p class="text-muted">{{ $item->updated_at->format('D, d M Y') }}</p>
                                                    @if (!empty($item->cancel_reason))
                                                        <p class="cart-title">Cancel Reason: {{ $item->cancel_reason }}
                                                        </p>
                                                    @endif


                                                    @if (!empty($item->awb_number))
                                                        <p class="cart-title">Awb Number: {{ $item->awb_number }}</p>
                                                    @endif
                                                    @if (!empty($item->tracking_url))
                                                        <p class="cart-title">Tracking Url: <a
                                                                href="{{ $item->tracking_url }}" target="_blank">View
                                                            </a></p>
                                                    @endif
                                                    @if (!empty($item->delivery_partner_name))
                                                        <p class="cart-title">Delivery Partner Name:
                                                            {{ $item->delivery_partner_name }}</p>
                                                    @endif
                                                    @if (!empty($item->refund_reason))
                                                        <p class="cart-title">Refund Reason: {{ $item->refund_reason }}
                                                        </p>
                                                    @endif
                                                    @if (!empty($item->exchanged_reason))
                                                        <p class="cart-title">Exchanged Reason:
                                                            {{ $item->exchanged_reason }}</p>
                                                    @endif
                                                    @if (!empty($item->payment_option))
                                                        <p class="cart-title">Payment Option: {{ $item->payment_option }}
                                                        </p>
                                                    @endif
                                                    @if (!empty($item->account_number))
                                                        <p class="cart-title">Account Number: {{ $item->account_number }}
                                                        </p>
                                                    @endif
                                                    @if (!empty($item->confirm_account_number))
                                                        <p class="cart-title">Confirm Account Number:
                                                            {{ $item->confirm_account_number }}</p>
                                                    @endif
                                                    @if (!empty($item->ifsc_code))
                                                        <p class="cart-title">Ifsc Code: {{ $item->ifsc_code }}</p>
                                                    @endif
                                                    @if (!empty($item->account_type))
                                                        <p class="cart-title">Account Type: {{ $item->account_type }}</p>
                                                    @endif
                                                    @if (!empty($item->bank_name))
                                                        <p class="cart-title">Bank Name: {{ $item->bank_name }}</p>
                                                    @endif
                                                    @if (!empty($item->remark))
                                                        <p class="cart-title">Remark: {{ $item->remark }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                        </div>
                        <!--end accordion-->
                    </div>
                </div>
            </div>
            <!--end card-->
        </div>
        <!--end col-->
        <div class="col-xl-3">
            <!-- <div class="card">
                        <div class="card-header">
                            <div class="d-flex">
                                <h5 class="card-title flex-grow-1 mb-0"><i class="mdi mdi-truck-fast-outline align-middle me-1 text-muted"></i> Logistics Details</h5>
                                <div class="flex-shrink-0">
                                    <a href="javascript:void(0);" class="badge bg-primary-subtle text-primary fs-11">Track Order</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="text-center">
                                <lord-icon src="https://cdn.lordicon.com/uetqnvvg.json" trigger="loop" colors="primary:#405189,secondary:#0ab39c" style="width:80px;height:80px"></lord-icon>
                                <h5 class="fs-16 mt-2">RQK Logistics</h5>
                                <p class="text-muted mb-0">ID: MFDS1400457854</p>
                                <p class="text-muted mb-0">Payment Mode : Debit Card</p>
                            </div>
                        </div>
                    </div> -->
            <!--end card-->

            <div class="card">
                <div class="card-header">
                    <div class="d-flex">
                        <h5 class="card-title flex-grow-1 mb-0">Customer Details</h5>
                        <div class="flex-shrink-0">
                            <a href="javascript:void(0);" class="link-secondary">View Profile</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0 vstack gap-3">
                        <li>
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <img src="assets/images/users/avatar-3.jpg" alt="" class="avatar-sm rounded">
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="fs-14 mb-1">{{ @$order->user->name }}</h6>
                                    <p class="text-muted mb-0">Customer</p>
                                </div>
                            </div>
                        </li>
                        <li><i class="ri-mail-line me-2 align-middle text-muted fs-16"></i>{{ @$order->user->email }}</li>
                        <li><i
                                class="ri-phone-line me-2 align-middle text-muted fs-16"></i>{{ @$order->user->phone_number }}
                        </li>
                    </ul>
                </div>
            </div>
            <!--end card-->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="ri-map-pin-line align-middle me-1 text-muted"></i> Billing
                        Address</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled vstack gap-2 fs-13 mb-0">
                        <?php
                        $address = json_decode($order->billing_address, true); // true => associative array
                        ?>

                        <li class="fw-medium fs-14">
                            {{ $address['billing_customer_name'] }},
                            {{ $address['billing_address'] }},
                            {{ $address['billing_city'] }} - {{ $address['billing_pincode'] }},
                            {{ $address['billing_state'] }},
                            {{ $address['billing_country'] }},<br>
                            {{ $address['billing_email'] }}<br>
                            {{ $address['billing_phone'] }}
                        </li>


                    </ul>
                </div>
            </div>
            <!--end card-->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="ri-map-pin-line align-middle me-1 text-muted"></i> Shipping
                        Address</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled vstack gap-2 fs-13 mb-0">
                        <?php
                        $shipping_address = json_decode($order->shipping_address, true); // true => associative array
                        ?>

                        <li class="fw-medium fs-14">
                            {{ $shipping_address['shipping_customer_name'] }},
                            {{ $shipping_address['shipping_address'] }},
                            {{ $shipping_address['shipping_city'] }} - {{ $shipping_address['shipping_pincode'] }},
                            {{ $shipping_address['shipping_state'] }},
                            {{ $shipping_address['shipping_country'] }},<br>
                            {{ $shipping_address['shipping_email'] }}<br>
                            {{ $shipping_address['shipping_phone'] }}
                        </li>
                    </ul>
                </div>
            </div>
            <!--end card-->

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="ri-secure-payment-line align-bottom me-1 text-muted"></i>
                        Payment Details</h5>
                </div>
                <div class="card-body">

                    <div class="d-flex align-items-center mb-2">
                        <div class="flex-shrink-0">
                            <p class="text-muted mb-0">Payment Method:</p>
                        </div>
                        <div class="flex-grow-1 ms-2">
                            <h6 class="mb-0">{{ $order->payment_method }}</h6>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                        <div class="flex-shrink-0">
                            <p class="text-muted mb-0">Transactions:</p>
                        </div>
                        <div class="flex-grow-1 ms-2">
                            <h6 class="mb-0">{{ $order->transaction_id }}</h6>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                        <div class="flex-shrink-0">
                            <p class="text-muted mb-0">Payment Status:</p>
                        </div>
                        <div class="flex-grow-1 ms-2">
                            <h6 class="mb-0">{{ $order->payment_status }}</h6>
                        </div>
                    </div>

                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <p class="text-muted mb-0">Total Amount:</p>
                        </div>
                        <div class="flex-grow-1 ms-2">
                            <h6 class="mb-0">₹ {{ $order->total }}</h6>
                        </div>
                    </div>
                </div>
            </div>
            <!--end card-->
        </div>
        <!--end col-->
    </div>

    <!-- Modal -->
    <div class="modal fade" id="cancelRequestModal" tabindex="-1" aria-labelledby="cancelRequestModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-body">
                    <form>
                        <div class="mb-3">
                            <label for="feedback-text" class="form-label">Remark</label>
                            <textarea class="form-control" id="feedback-text" rows="4" placeholder="Type your remark here..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary cancelRequestSubmit">Submit</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <!-- Custom-Switcher JS -->
    <script src="{{ asset('assets/js/custom-switcher.min.js') }}"></script>

    <!-- Swiper JS -->
    <script src="{{ asset('assets/libs/swiper/swiper-bundle.min.js') }}"></script>

    <script src="{{ asset('assets/js/product-details.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $('.print-checkbox-select-all').on('change', function() {
                if ($(this).prop('checked')) {
                    $('.print-checkbox').prop('checked', true);
                } else {
                    $('.print-checkbox').prop('checked', false);
                }
            });

            $('.print-checkbox').on('change', function() {
                if ($('.print-checkbox:checked').length) {
                    if ($('.print-checkbox:checked').length < $('.print-checkbox').length) {
                        $('.print-checkbox-select-all').prop('indeterminate', true);
                    } else {
                        $('.print-checkbox-select-all').prop('indeterminate', false);
                        $('.print-checkbox-select-all').prop('checked', true);
                    }
                } else {
                    $('.print-checkbox-select-all').prop('indeterminate', false);
                    $('.print-checkbox-select-all').prop('checked', false);
                }
            });

            $('.print-invoice-btn').click(function() {
                if ($('.print-checkbox:checked').length) {
                    var itemIds = [];
                    var orderId = $(this).data('id');
                    $('.print-checkbox:checked').each(function(i, e) {
                        id = $(e).data('id');
                        itemIds.push(id);

                    });
                    console.log(itemIds);
                    $.ajax({
                        url: '{{ route('admin-orders.generate.items.invoice') }}',
                        data: {
                            ids: itemIds,
                            id: orderId
                        },
                        dataType: 'json',
                        method: 'post',
                        success: function(res) {

                            let byteChars = atob(res.file);
                            let byteNumbers = new Array(byteChars.length);
                            for (let i = 0; i < byteChars.length; i++) {
                                byteNumbers[i] = byteChars.charCodeAt(i);
                            }
                            let byteArray = new Uint8Array(byteNumbers);
                            let blob = new Blob([byteArray], {
                                type: "application/pdf"
                            });

                            let link = document.createElement('a');
                            link.href = window.URL.createObjectURL(blob);
                            link.download = res.filename;
                            link.click();

                        },
                        error: function(xhr) {
                            Swal.fire('Error!', 'Something went wrong', 'error');
                        }
                    });
                } else {
                    Swal.fire('Error!', 'Please select item', 'error');
                }
            });

            const referralLinkInput = document.getElementById('referralLink');
            const copyReferralLinkButton = document.getElementById('copyReferralLink');
            if (copyReferralLinkButton) {
                copyReferralLinkButton.addEventListener('click', function() {
                    referralLinkInput.select();
                    document.execCommand('copy');
                });
            }

            let id = 0;
            let order_item_id = 0;
            let status = 0;
            let requestType = 'cancel';

            $('.refundRequestAction, .cancelRequestAction').click(function() {
                id = $(this).data('id');
                order_item_id = $(this).data('order_item_id');
                status = $(this).data('status');
                requestType = $(this).data('request_type');
                $('#cancelRequestModal').modal('show');
            });

            $('.cancelRequestSubmit').click(function() {
                let statusText = (status == 1) ? 'Accept' : 'Reject';
                let remark = $('#feedback-text').val().trim();
                if (!remark) {
                    Swal.fire('Error', 'Please enter remark', 'error');
                    return;
                }
                if (confirm('Are you sure you want to ' + statusText + '?')) {
                    if (id && status) {
                        let requestUpdateUrl = (requestType == 'cancel') ?
                            '{{ route('admin-order.update-cancel-request') }}' :
                            '{{ route('admin-order.update-return-request') }}';
                        $('#cancelRequestModal').modal('hide');
                        $.ajax({
                            url: requestUpdateUrl,
                            data: {
                                id,
                                status,
                                order_item_id,
                                remark
                            },
                            dataType: 'json',
                            method: 'post',
                            success: function(resp) {
                                if (resp.status == 'success') {
                                    window.location.reload();
                                } else {
                                    Swal.fire('Error', resp.message, 'error');
                                }
                            },
                            error: function(xhr, textStatus, errorThrown) {
                                console.log(xhr);
                                if (xhr.status !== 200) {
                                    Swal.fire('Error', 'Something went wrong!', 'error');
                                }
                            }
                        });
                    }
                }
            });
        });

        var statuss = @json($statuss);
        var refundRequests = @json($refundRequests);
        var couriers = @json(getCouriers());
        $(document).ready(function() {

            let couriersOptions = '<option value="">Select Courier</option>';
            for (const key in couriers) {
                if (couriers.hasOwnProperty(key)) {
                    couriersOptions += `<option value="${couriers[key].id}">${couriers[key].name}</option>`;
                }
            }

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('.itemstatus, .exchangedstatus, .shippedstatus').change(function() {
                var that = $(this);
                var status = that.val();
                var currentStatus = that.data('status');
                var id = that.data('order');
                var url = "{{ route('admin-orders-item.change-status') }}";
                var inputHtml = '';

                var statusSlug = status ? status.toLocaleLowerCase() : '';
                // Check if the selected status is "shipped"
                if (statusSlug === 'shipped') {
                    // For shipped status, include all fields
                    inputHtml = `
                                <div>
                                    <div style="display: flex;justify-content: center;">
                                        <label>
                                            <input type="radio" id="shipping_type_courier" name="shipping_type" value="Automatic" checked onchange="document.querySelector('.manual-shipping').style.display='none'; document.querySelector('.auto-shipping').style.display='flex';"> Automatic
                                        </label>
                                        <label>
                                            <input type="radio" id="shipping_type_manual" name="shipping_type" value="Manual" style="margin-left:10px;" onchange="document.querySelector('.manual-shipping').style.display='block';document.querySelector('.auto-shipping').style.display='none';"> Manual
                                        </label>
                                    </div>                                                                  
                                </div>

                                <div style="display:flex">
                                        <input type="number" id="auto_length" name="auto_length" placeholder="Length in cm" style="margin:5px;" class="form-control" title="The length of the item in cms. Must be more than 0.5." />

                                        <input type="number" id="auto_breadth" name="auto_breadth" placeholder="Breadth in cm" style="margin:5px;" class="form-control" title="The breadth of the item in cms. Must be more than 0.5." />

                                        <input type="number" id="auto_height" name="auto_height" placeholder="Height in cm" style="margin:5px;" class="form-control" title="The height of the item in cms. Must be more than 0.5." />
                                    </div>
                                    <div><p class="text-danger">The length, breadth, and height (in cm) must each be greater than 0.5.</p></div>
                                <div class="auto-shipping">
                                    
                                </div>

                                <div class="manual-shipping" style="display: none;">
                                    <select id="delivery_partner_name" name="delivery_partner_name" style="width:100%; margin-top:10px;" class="form-control" required>
                                        ${couriersOptions}
                                    </select>                                    
                                    <input id="tracking_url" name="tracking_url" placeholder="Tracking URL" style="width:100%; margin-top:10px;" class="form-control" readonly />
                                    <input id="awb_number" name="awb_number" placeholder="AWB/Tracking No." style="width:100%; margin-top:10px;" class="form-control" />
                                    <textarea id="remark" placeholder="Type your remark here..." style="width:100%; height:100px; margin-top:10px;" class="form-control"></textarea>
                                </div>
                            `;
                } else if (statusSlug === 'refunded') {
                    let refundInfo = '';
                    if(refundRequests[id]){
                        refund_mode = refundRequests[id]['refund_mode'];
                        if(refund_mode == "account"){
                            refundInfo = `
                                        <p class="text-danger">Please copy below user bank details before submitting the refund request.</p>
                                        <table class="table table-bordered mb-3">
                                            <tr>
                                                <td>User Refund Mode</td>
                                                <td>Account Number</td>
                                                <td>IFSC Code</td>
                                                <td>Bank Name</td>
                                            </tr>
                                            <tr>
                                                <td>Bank Channel</td>
                                                <td>${refundRequests[id]['account_number']}</td>
                                                <td>${refundRequests[id]['ifsc_code']}</td>
                                                <td>${refundRequests[id]['bank_name']}</td>
                                            </tr>
                                        </table>
                                    `;
                        }
                        
                    }
                    inputHtml = refundInfo + `
                                <div>
                                    <div style="display: flex;justify-content: center;">
                                        <label>
                                            <input type="radio" id="refund_type_wallet" name="refund_type" value="wallet" checked> Wallet
                                        </label>
                                        <label>
                                            <input type="radio" id="refund_type_bank_channel" name="refund_type" value="bank_channel" style="margin-left:10px;"> Bank Channel
                                        </label>
                                         <label>
                                            <input type="radio" id="refund_type_original_payment" name="refund_type" value="original_payment" style="margin-left:10px;"> Original Payment Method
                                        </label>
                                    </div>                                                                  
                                </div>`
                } else {
                    // For other statuses, include only the remark field
                    inputHtml =
                        `<textarea id="remark" placeholder="Type your remark here..." style="width:100%; height:100px;" class="form-control" ></textarea>
                        `;
                }

                // Status change confirmation
                Swal.fire({
                    title: "Are you sure?",
                    text: "Want to change this status?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Yes, change it",
                    cancelButtonText: "No, cancel",
                    reverseButtons: true
                }).then(function(result) {
                    if (result.value) {
                        // Popup with relevant input fields
                        Swal.fire({
                            title: (statusSlug !== 'shipped') ? 'Enter your details' :
                                'Shipping',
                            html: inputHtml,
                            showCancelButton: true,
                            confirmButtonText: 'Submit',
                            preConfirm: () => {
                                // Access the values after the popup is displayed
                                return {
                                    remark: $('#remark').val(),
                                    refund_type: $('input[name="refund_type"]:checked')
                                        .val(),
                                    auto_height: ($('#auto_height').val()) ? parseFloat(
                                        $('#auto_height').val()) : 0,
                                    auto_breadth: ($('#auto_breadth').val()) ?
                                        parseFloat($('#auto_breadth').val()) : 0,
                                    auto_length: ($('#auto_length').val()) ? parseFloat(
                                        $('#auto_length').val()) : 0,
                                    shipping_type: $(
                                            'input[name="shipping_type"]:checked')
                                        .val(),
                                    awb_number: (statusSlug === 'shipped') ? $(
                                        '#awb_number').val() : null,
                                    tracking_url: (statusSlug === 'shipped') ? $(
                                        '#tracking_url').val() : null,
                                    courier_id: (statusSlug === 'shipped') ? $(
                                        '#delivery_partner_name').val() : null
                                };
                            }
                        }).then(function(remarkResult) {
                            // Ajax call for status update
                            if (remarkResult.isConfirmed) {
                                console.log(remarkResult.value);
                                if (remarkResult.value.shipping_type == 'Manual' && (!
                                        remarkResult.value.awb_number || !remarkResult.value
                                        .tracking_url || !remarkResult.value.courier_id)) {
                                    that.val(currentStatus);
                                    Swal.fire("Cancelled",
                                        "Please fill all the required fields :)",
                                        "error");
                                    return;
                                } else if (remarkResult.value.shipping_type ==
                                    'Automatic' && (!remarkResult.value.auto_height || !
                                        remarkResult.value.auto_breadth || !remarkResult
                                        .value.auto_length)) {
                                    that.val(currentStatus);
                                    Swal.fire("Cancelled",
                                        "Please fill all the required fields :)",
                                        "error");
                                    return;
                                } else if (remarkResult.value.shipping_type ==
                                    'Automatic' && (remarkResult.value.auto_height < 0.5 ||
                                        remarkResult.value.auto_breadth < 0.5 ||
                                        remarkResult.value.auto_length < 0.5)) {
                                    that.val(currentStatus);
                                    Swal.fire("Cancelled",
                                        "Height, length and breadth must each be greater than 0.5",
                                        "error");
                                    return;
                                }  else if(statusSlug !== 'shipped' && statusSlug !== 'refunded' && remarkResult.value.remark ==''){
                                    Swal.fire("Error Validation",
                                        "Please fill remark field",
                                        "error"
                                    ).then(() => {
                                        location.reload(); // Page refresh
                                    });
                                    return;
                                }
                                $.ajax({
                                    type: "POST",
                                    url: url,
                                    data: {
                                        id: id,
                                        status: status,
                                        remark: remarkResult.value.remark || null,
                                        refund_type: remarkResult.value
                                            .refund_type || null,
                                        awb_number: remarkResult.value.awb_number,
                                        tracking_url: remarkResult.value
                                            .tracking_url,
                                        courier_id: remarkResult.value.courier_id,
                                        shipping_type: remarkResult.value
                                            .shipping_type,
                                        height: remarkResult.value.auto_height,
                                        breadth: remarkResult.value.auto_breadth,
                                        length: remarkResult.value.auto_length,
                                    },
                                    success: function(response) {
                                        Swal.fire({
                                            icon: response.status,
                                            title: response.message,
                                            showConfirmButton: true,
                                        }).then(() => {
                                            location.reload();
                                        });
                                    },
                                    error: function(data) {
                                        console.log('Error:', data);
                                    }
                                });
                            } else {
                                that.val(currentStatus);
                                Swal.fire("Cancelled",
                                    "Your current status remains the same :)", "error");
                            }
                        });
                    } else {
                        that.val(currentStatus);
                        Swal.fire("Cancelled", "Your current status remains the same :)", "error");
                    }
                });
            });

            $(document).on('change', '#delivery_partner_name', function() {
                var selectedOption = $(this).find('option:selected');
                var courierId = selectedOption.val();
                if (courierId && couriers[courierId] && couriers[courierId].tracking_url) {
                    $('#tracking_url').val(couriers[courierId].tracking_url);
                } else {
                    $('#tracking_url').val('');
                }
            });


        });
    </script>
@endpush
