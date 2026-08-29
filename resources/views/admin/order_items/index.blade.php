@extends('admin.layout.master')

@section('content')
    @include('admin.layout.response_message')

    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <h1 class="page-title fw-semibold fs-18 mb-0">Order Items</h1>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin-admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Order Items</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-xxl-2 col-xl-2 col-lg-6 col-md-6">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="d-flex align-items-top">
                        <div class="flex-fill">
                            <div class="d-flex flex-wrap align-items-center justify-content-between fs-14 mb-2">
                                <span class="flex-fill">Total Order Items</span>
                            </div>
                            <div class="d-flex flex-wrap align-items-center justify-content-between">
                                <h5 class="fw-semibold mb-0">{{ $totalItemCount ?? 0 }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if(!empty($itemCounts))
            @foreach ($itemCounts as $itemstatus=>$itemcount )
                <div class="col-xxl-2 col-xl-2 col-lg-6 col-md-6">
                    <div class="card custom-card">
                        <div class="card-body">
                            <div class="d-flex align-items-top">
                                <div class="flex-fill">
                                    <div class="d-flex flex-wrap align-items-center justify-content-between fs-14 mb-2">
                                        <span class="flex-fill">{{ $itemstatus }}</span>
                                    </div>
                                    <div class="d-flex flex-wrap align-items-center justify-content-between">
                                        <h5 class="fw-semibold mb-0">{{ $itemcount ?? 0 }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif

        <h5>Today's Items</h5>
        <div class="col-xxl-2 col-xl-2 col-lg-6 col-md-6">
            <div class="card custom-card">
                <div class="card-body">
                    <div class="d-flex align-items-top">
                        <div class="flex-fill">
                            <div class="d-flex flex-wrap align-items-center justify-content-between fs-14 mb-2">
                                <span class="flex-fill">Total Order Items</span>
                            </div>
                            <div class="d-flex flex-wrap align-items-center justify-content-between">
                                <h5 class="fw-semibold mb-0">{{ $totalItemTodayCount ?? 0 }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if(!empty($itemTodaysCounts))
            @foreach ($itemTodaysCounts as $todayitemstatus=>$todayitemcount )
                <div class="col-xxl-2 col-xl-2 col-lg-6 col-md-6">
                    <div class="card custom-card">
                        <div class="card-body">
                            <div class="d-flex align-items-top">
                                <div class="flex-fill">
                                    <div class="d-flex flex-wrap align-items-center justify-content-between fs-14 mb-2">
                                        <span class="flex-fill">{{ $todayitemstatus }}</span>
                                    </div>
                                    <div class="d-flex flex-wrap align-items-center justify-content-between">
                                        <h5 class="fw-semibold mb-0">{{ $todayitemcount ?? 0 }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header justify-content-between">
                    <div class="card-title">Order Items</div>
                    <div class="prism-toggle">
                        <button class="btn btn-primary bulk-print-btn" style="display: none;">Bulk Print</button>
                        <a href="javascript:void(0);" class="btn btn-primary dropdown-toggle mr-2" data-bs-toggle="collapse"
                            data-bs-target="#collapseSearch">Search</a>
                        <a href="{{ route('admin-orders.export-order-items', request()->query()) }}" class="btn btn-success" style="margin-left: 8px;">Export</a>
                    </div>
                </div>

                <div class="accordion accordion-solid accordion-toggle-plus" id="accordionSearch">
                    <div id="collapseSearch" class="collapse m-3 <?php echo !empty($searchVariable) ? 'show' : ''; ?>" data-parent="#accordionSearch">
                        <div>
                            <form id="listSearchForm" class="row mb-6">
                                <div class="col-lg-2 mb-lg-5 mb-6">
                                    <label>Records Per Page</label>
                                    <select name="limit" class="form-control select2init" value="{{ request()->limit }}">
                                        <option value="10" {{ request()->limit == 10 ? 'selected' : ''}}>Default (10)</option>
                                        <option value="20" {{ request()->limit == 20 ? 'selected' : ''}}>20 </option>
                                        <option value="50" {{ request()->limit == 50 ? 'selected' : ''}}>50</option>
                                        <option value="100" {{ request()->limit == 100 ? 'selected' : ''}}>100</option>
                                        <option value="200" {{ request()->limit == 200 ? 'selected' : ''}}>200</option>
                                        <option value="300" {{ request()->limit == 300 ? 'selected' : ''}}>300</option>
                                        <option value="400" {{ request()->limit == 400 ? 'selected' : ''}}>400</option>
                                        <option value="500" {{ request()->limit == 500 ? 'selected' : ''}}>500</option>
                                    </select>
                                </div>
                                <div class="col-lg-2 mb-lg-5 mb-6">
                                    <label>Status</label>
                                    <select name="status" class="form-control select2init" value="{{ request()->status }}">
                                        <option value="">All</option>
                                        @foreach ($status_array as $key => $value)
                                            <option value="{{ $key }}" {{ request()->status == $key ? 'selected' : '' }}>
                                                {{ $value ?? '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-2 mb-lg-5 mb-6">
                                    <label>Order Number</label>
                                    <input type="text" class="form-control" name="order_number" placeholder="Order Number" value="{{ old('order_number', request()->order_number) }}">
                                </div>
                                <div class="col-lg-2 mb-lg-5 mb-6">
                                    <label>Product Name</label>
                                    <input type="text" class="form-control" name="product_name" placeholder="Product Name" value="{{ old('product_name', request()->product_name) }}">
                                </div>
                                <div class="col-lg-2 mb-lg-5 mb-6">
                                    <label for="date_from" class="form-label">Date From</label>
                                    <input type="date" class="form-control" id="date_from" name="date_from" value="{{ old('date_from', request()->date_from) }}">
                                </div>
                                <div class="col-lg-2 mb-lg-5 mb-6">
                                    <label for="date_to" class="form-label">Date To</label>
                                    <input type="date" class="form-control" id="date_to" name="date_to" value="{{ old('date_to', request()->date_to) }}">
                                </div>

                                <div class="row mt-8">
                                    <div class="col-lg-12">
                                        <button class="btn btn-primary" id="kt_search_btn1">
                                            <span>Search</span>
                                        </button>
                                        &nbsp;&nbsp;
                                        <a href='{{ route('admin-orders-items.index') }}' class="btn btn-secondary">
                                            <span>Clear Search</span>
                                        </a>
                                    </div>
                                </div>
                            </form>
                            <hr>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered text-nowrap" style="width:100%">
                        <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" class="itemCheckAll">
                                    Order #
                                </th>
                                <th>SKU</th>
                                <th>Product</th>
                                <th>Qty</th>
                                <th>Price</th>
                                <th>Status</th>
                                <th>Created On</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($results->isNotEmpty())
                                @include('admin.order_items.load_more_data', ['results' => $results])
                            @else
                                <tr>
                                    <td colspan="7" style="text-align: center;">No results found.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                @if ($results->isNotEmpty())
                    <div class="my-3 mx-3">
                        {{ $results->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        // 
        $('.itemCheckAll').on('change', function () {
            $('.items-checkbox').prop('checked', this.checked);
            if ($('.items-checkbox:checked').length) {
                $('.bulk-print-btn').show();
            } else {
                $('.bulk-print-btn').hide();
            }
        });

        $('.items-checkbox').on('change', function () {
            if ($('.items-checkbox:checked').length == $('.items-checkbox').length) {
                $('.itemCheckAll').prop('checked', true);
            } else {
                $('.itemCheckAll').prop('checked', false);
            }

            if ($('.items-checkbox:checked').length) {
                $('.bulk-print-btn').show();
            } else {
                $('.bulk-print-btn').hide();
            }
        });

        $('.bulk-print-btn').on('click', function () {
            if ($('.items-checkbox:checked').length) {
                let checkedItems = $('.items-checkbox:checked');
                let orderItemIds = [];
                checkedItems.each(function () {
                    orderItemIds.push($(this).val());
                });

                $('.bulk-print-btn').attr('disabled', true).html('Generating...');

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ route('admin-orders.generate.bulkitem.invoice') }}",
                    type: 'POST',
                    data: {ids:orderItemIds},
                    success: function (res) {                        
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
                        $('.bulk-print-btn').attr('disabled', false).html('Bulk Print');
                    },
                    error:function(){
                        $('.bulk-print-btn').attr('disabled', false).html('Bulk Print');
                    }

                });
            }
        });


    });

</script>

@endpush