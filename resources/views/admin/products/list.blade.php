@extends('admin.layout.master')
@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.3.0/css/responsive.bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
@endpush

<style>
    .table-container {
        width: 100%;
        overflow-x: auto;
        /* Add a scrollbar for horizontal overflow */
    }

    .table-container {
        overflow-x: auto;
        overflow-y: auto;
        max-height: 600px;
        /* Increase the maximum height */
        height: 100%;
        /* Use the full height */
        width: 100%;
        /* Use the full width */
        white-space: nowrap;
        /* Ensure the table does not wrap and can scroll horizontally */
    }
    .input-group.qty-group {
        flex-wrap: nowrap;
    }
    .qty-group input.form-control.qty-input {
        flex: 0 1 auto;
        width: 65px;
    }
</style>

@section('content')
    @include('admin.layout.response_message')
    <!-- Page Header -->
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <h1 class="page-title fw-semibold fs-18 mb-0">Products</h1>
        <div class="ms-md-1 ms-0">
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin-admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Products</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-header justify-content-between">
                    <div class="card-title">
                        Products
                    </div>
                    <div class="prism-toggle">
                        <a href="javascript:void(0);" class="btn btn-primary dropdown-toggle mr-2" data-bs-toggle="collapse"
                            data-bs-target="#collapseOne6">
                            Search
                        </a>
                        <a href="{{ route('admin-product-create-new-product') }}" class="btn btn-primary"
                            style="margin-right: 10px;">Add Product</a>
                        <a id="exportBtn" href="{{ route('admin-product.export-product') }}" class="btn btn-success" style="margin-right: 10px;">Export</a>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#importProductModal">
                            Import Products
                        </button>
                        <a href="{{ asset('uploads/excel_format.xlsm') }}" class="btn btn-primary">
                            Import Excel Format
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="accordion accordion-solid accordion-toggle-plus" id="accordionExample6">
                        <div id="collapseOne6" class="collapse m-3 <?php echo !empty($searchVariable) ? 'show' : ''; ?>" data-parent="#accordionExample6">
                            <div>
                                <form id="listSearchForm" class="row mb-6">
                                  
                                    <div class="col-lg-4 mb-lg-5 mb-6">
                                        <label>Name</label>
                                        <input type="text" class="form-control" name="name" placeholder=" Name"
                                           value="{{ old('name',request()->name) }}">
                                    </div>

                                    <div class="col-lg-2 mb-lg-5 mb-6">
                                        <label>SKU</label>
                                        <input type="text" class="form-control" name="sku" placeholder=" SKU"
                                           value="{{ old('name',request()->sku) }}">
                                    </div>
                                  
                                    <?php
                                    use App\Models\Category;
                                    $categories = Category::whereNull('parent_id')->get();
                                    ?>
                                    <div class="col-lg-2  mb-lg-5 mb-6">
                                        <label>Category</label>
                                        <select name="category_id" class="form-control select2init" value="">
                                            <option value="" selected>Select Category</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}" {{ $category->id == request()->category_id ? 'selected':'' }}>{{ $category->name }}</option>
                                            @endforeach
                                           
                                        </select>
                                    </div>
                                    <div class="col-lg-2 mb-lg-5 mb-6">
                                        <label for="date_from" class="form-label"><span class="text-danger"></span>Date
                                            From</label>
                                        <input type="date" class="form-control " id="date_from" name="date_from"
                                            placeholder="Date From" value="{{ old('date_from',request()->date_from) }}">

                                    </div>
                                    <div class="col-lg-2 mb-lg-5 mb-6">
                                        <label for="date_to" class="form-label"><span class="text-danger"></span>Date
                                            To</label>
                                        <input type="date" class="form-control " id="date_to" name="date_to"
                                            placeholder="Date To" value="{{ old('date_to',request()->date_to) }}">

                                    </div>
                                    <div class="row mt-8">
                                        <div class="col-lg-12">
                                            <button class="btn btn-primary btn-primary--icon" id="kt_search_btn">
                                                <span>
                                                    <i class="la la-search"></i>
                                                    <span>Search</span>
                                                </span>
                                            </button>
                                            &nbsp;&nbsp;
                                            <a href='{{ route($listRouteName) }}' class="btn btn-secondary btn-secondary--icon">
                                                <span>
                                                    <i class="la la-close"></i>
                                                    <span>Clear Search</span>
                                                </span>
                                            </a>
                                        </div>
                                    </div>
                                </form>
                                
                                <hr>
                            </div>
                        </div>
                    </div>
                    <form action="{{ route('admin-product-updatedata') }}">
                        <div class="container mt-4 mb-4">
                            <div class="row">
                                <div class="col-2 mt-4">
                                    <label for="selectOptions" class="form-label">Select All:</label>
                                    <input type="checkbox" id="selectAll">
                                </div>
                                <div class="col-3">
                                    <label for="selectOptions" class="form-label">Bulk Action:</label>
                                    <select name="bulk_action" class="form-select" id="selectOptions">
                                        <option value="" selected>Select Bulk</option>
                                        <option value="1">Mark as draft</option>
                                        <option value="2">Mark as published</option>
                                        <option value="3">Mark as unpublished</option>
                                        <option value="4">Mark as featured</option>
                                        <option value="5">Mark as unfeatured</option>
                                        <option value="6">Mark as new arrivals</option>
                                        <option value="7">Mark as new unarrivals</option>
                                    </select>
                                </div>
                                <div class="col-2">
                                    <button type="submit" class="btn btn-outline-primary mt-4">Update</button>
                                </div>
                                <div class="container col-5 d-flex justify-content-end align-items-center">
                                    <button type="button" class="btn btn-outline-primary">
                                        Total Products:
                                        <span class="badge ms-2 totalDataCount">{{ $totalResults ?? 0 }}</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="table-container">
                            <table id="datatable-basic" data-sorting="" data-order=""
                                class="table table-bordered text-nowrap" style="width:100%">
                                <thead>
                                    <tr>
                                        <!-- <th>abc</th> -->
                                        <th>S.No.</th>
                                        <th>Image</th>
                                        <th class="sortable" data-column="products.name">Name <i
                                                class="sort-icon ri-sort-asc"></i></th>
                                        <th class="sortable" data-column="category_name">Category <i
                                                class="sort-icon ri-sort-asc"></i></th>
                                        <th class="sortable" data-column="products.buying_price">Price <i
                                                class="sort-icon ri-sort-asc"></i></th>
                                        <th>Status</th>
                                        <th>Out of Stock</th>
                                        <th>Publish</th>
                                        <!-- <th>Quantity</th> -->
                                        <th>T.Qty</th>
                                        <th>T.Avl</th>
                                        <th style="min-width:225px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="powerwidgets" class="sortableCategories">
                                    <!-- <tr id="loader-row" style="display: none;">
                                        <td colspan="7" style="text-align: center;">
                                            <button class="btn btn-light" type="button" disabled="">
                                                <span class="spinner-grow spinner-grow-sm align-middle" role="status"
                                                    aria-hidden="true"></span> Loading...
                                            </button>
                                        </td>
                                    </tr> -->
                                        @if ($productLsit->isNotEmpty())
                                            @include('admin.products.load_more_data', ['productLsit' => $productLsit])          
                                        @else
                                            <tr>
                                                <td colspan="10" style="text-align: center;">No Products Found.</td>
                                            </tr>
                                        @endif
                                </tbody>
                                <div id="loading" class="text-center my-4" style="display:none;">
                                    <img src="https://vasvi.in/assets/front/img/favi_vasvi.png" width="40" alt="Loading...">
                                </div>
                            </table>
                        </div>
                        @if ($productLsit->isNotEmpty())
                        <div class="my-3 mx-3">
                            {{ $productLsit->links('pagination::bootstrap-5') }}
                        </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Bootstrap Modal -->
    <div class="modal fade" id="productVariantModal" tabindex="-1" aria-labelledby="productVariantModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg"> <!-- optional: use modal-lg for wider content -->
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="productVariantModalLabel">Product Variants</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="varientDivBody">

                </div>
            </div>
        </div>
    </div>

    <!-- Price Modal -->
    <div class="modal fade" id="priceModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Product & Variant Prices</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body" id="priceModalBody">
                    <div class="text-center">Loading...</div>
                </div>

            </div>
        </div>
    </div>
    <div class="modal fade" id="importProductModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('admin-product-products.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Import Products</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <input type="file" name="file" class="form-control" accept=".xlsx,.xls" required>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Upload</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.3.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.6/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/.12.1/jquery-ui.min.js"></script>

    <script>
        var routeName = '{{ route($listRouteName) }}';
    </script>
    <script src="{{ asset('assets/js/datatables.js') }}"></script>

    <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/js/sweet-alerts.js') }}"></script>

    <script>
        $(document).ready(function() {
            $(document).on('change', '.in-stock-checkbox', function() {
                var productId = $(this).data('product-id');
                var inStock = $(this).prop('checked') ? 1 : 0;

                $.ajax({
                    url: "{{ route('admin-product-update-stock') }}", // Replace with your actual route
                    type: 'POST',
                    data: {
                        productId: productId,
                        inStock: inStock
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Update successfully',
                            showConfirmButton: true,
                        })
                    },
                    error: function(error) {
                        // Handle error (if needed)
                    }
                });
            });
        });

        $(document).ready(function() {
            $(document).on('change', '.is-featured-checkbox', function() {
                var productId = $(this).data('product-id');
                var isFeatured = $(this).prop('checked') ? 1 : 0;

                $.ajax({
                    url: "{{ route('admin-product-update-featured') }}", // Replace with your actual route
                    type: 'POST',
                    data: {
                        productId: productId,
                        isFeatured: isFeatured
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Update successfully',
                            showConfirmButton: true,
                        })
                    },
                    error: function(error) {
                        // Handle error (if needed)
                    }
                });
            });
            $(document).on('change', '.is-newarrivals-checkbox', function() {
                var productId = $(this).data('product-id');
                var isFeatured = $(this).prop('checked') ? 1 : 0;

                $.ajax({
                    url: "{{ route('admin-product-update-new-arrivals') }}",
                    type: 'POST',
                    data: {
                        productId: productId,
                        isNewArrivals: isFeatured
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Update successfully',
                            showConfirmButton: true,
                        })
                    },
                    error: function(error) {
                        // Handle error (if needed)
                    }
                });
            });
        });

        $(document).ready(function() {
            $('#selectAll').on('click', function() {
                $('.product-checkbox').prop('checked', this.checked);
            });

            $('.product-checkbox').on('click', function() {
                if (!$(this).prop('checked')) {
                    $('#selectAll').prop('checked', false);
                } else if ($('.product-checkbox:checked').length === $('.product-checkbox').length) {
                    $('#selectAll').prop('checked', true);
                }
            });
        });

        $(document).ready(function() {

            $(function() {

                $("#sortable").sortable({

                    handle: ".move-line",
                    update: function(event, ui) {
                        let productOrder = [];

                        $('#sortable tr').each(function(index, element) {
                            var productId = $(element).data('id');
                            if (productId !== undefined) {
                                productOrder.push(productId);
                            }
                        });


                        $.ajax({
                            url: '{{ route('admin-product-update-order') }}',
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                products: productOrder
                            },
                            success: function(response) {

                            }
                        });
                    }
                });
            });
        });

        $(document).on('click', '.update-qty', function () {
           let productId = $(this).closest('.qty-group').data('product-id');
           let qty = $(this).closest('.qty-group').find('.qty-input').val();

            $.ajax({
                url: '{{ route("admin-product-update.product.qty") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    product_id: productId,
                    qty: qty
                },
                success: function (response) {
                     Swal.fire({
                            icon: 'success',
                            title: 'Quantity updated successfully.',
                            showConfirmButton: true,
                        });
                    //alert(response.message);
                }
            });
        });

        let offset = {{ $limit }};
        let limit = {{ $limit }};
        let totalCount = {{ $totalResults }};
        let url   = "{{ route('admin-product-list') }}";
        let loading = false;
        function onScrollHandler() {
            if (loading) return;
            if (offset >= totalCount) {
                window.removeEventListener('scroll', onScrollHandler);
                return;
            }
            let threshold = window.innerWidth <= 768 ? 1200 : 800;
            if (window.innerHeight + window.scrollY >= document.body.offsetHeight - threshold) {
                loadMore();
            }
        }

        window.addEventListener('scroll', onScrollHandler);
        function loadMore() {
            loading = true;
            document.getElementById('loader').style.display = 'block';
            let form = $('#listSearchForm');
            let formData = form.serialize();
            console.log(formData);
            $.ajax({
                url: url,
                data: formData+'&offset=' + offset + '&limit=' + limit + '&ajax=1',
                success: function (res) {
                    if (!res.html || $.trim(res.html) === '') {
                        window.removeEventListener('scroll', onScrollHandler);
                        document.getElementById('loader').innerHTML = '<p>No more records</p>';
                    } else {
                        $('#sortable').append(res.html);
                        offset += limit;
                        totalCount = res.totalResults; // ✅ update filtered count
                        document.getElementById('loader').style.display = 'none';

                        if (offset >= totalCount) {
                        window.removeEventListener('scroll', onScrollHandler);
                        document.getElementById('loader').innerHTML = '<p>No more records</p>';
                        }
                    }
                },
                error: function () {
                    document.getElementById('loader').innerHTML = '<p>Error loading data</p>';
                },
                complete: function () {
                    loading = false;
                }
            });
        }

        $('#exportBtn').on('click', function () {
            let form = $('#listSearchForm');
            let formData = form.serialize();
            window.location.href = '/admin/export-products?' + formData;
        });
        //varient modal
        $(document).on('click', '.productVariantModal', function () {
            let productId = $(this).closest('.variant-group').data('product-id');
            let url = "{{ route('admin-product-get-varient', ['productId' => '::productId::']) }}".replace('::productId::', productId);
            $.ajax({
                url: url,
                method: 'GET',
                success: function (response) {
                    $("#varientDivBody").html(response.html);
                    $('#productVariantModal').modal('show');
                }
            });
        });
        </script>


<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>
<script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="{{ asset('assets/js/sweet-alerts.js') }}"></script>
<script type="text/javascript">
    new Sortable(powerwidgets, {
        animation: 150,
        ghostClass: 'sortable-ghost',
        onEnd: function(evt) {
            var counter = 1;
            var requestData = [];
            $(".items-inner").each(function() {
                requestData.push({
                    "id": $(this).attr("data-id"),
                    "order": counter
                });
                counter++;
            });

            $.ajax({
                url: '{{ Route('admin-product-product.updateProductOrder') }}',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    "requestData": requestData
                },
                success: function(response) {
                    Swal.fire({
                        title: "Success",
                        text: "Product Order updated successfully!",
                        icon: "success"
                    });
                    location.reload();
                }
            });
        },
    });
</script>




@endpush
