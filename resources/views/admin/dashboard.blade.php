@extends('admin.layout.master')

@section('content')
<!-- Start::page-header -->

<div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
    <div>
        <p class="fw-semibold fs-18 mb-0">Welcome back, {{ ucfirst(auth()->user()->name) }}</p>
        <!-- <span class="fs-semibold text-muted">Track your activities here.</span> -->
    </div>
</div>

<!-- End::page-header -->

<div class="row mb-4">
    <div class="col-xxl-3 col-xl-3 col-lg-6 col-md-6">
        <div class="card custom-card purple_box_dash">
            <div class="card-body">
                <div class="d-flex align-items-top">
                    <a href="{{ route('admin-admin_users.index') }}">
                    <div class="flex-fill">
                        <div class="d-flex flex-wrap align-items-center justify-content-between fs-14 mb-2">
                            <span class="flex-fill">Total Customers</span>
                        </div>
                        <div class="d-flex flex-wrap align-items-center justify-content-between">
                            <h5 class="fw-semibold mb-0">{{ $usersCount ?? 0 }}</h5>
                            <div id="btcCoin"></div>
                        </div>
                    </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xxl-3 col-xl-3 col-lg-6 col-md-6">
        <div class="card custom-card purple_box_dash teal_box_dash">
            <div class="card-body">
                <div class="d-flex align-items-top">
                    
                    <a href="{{ route('admin-category.index') }}">
                    <div class="flex-fill">
                        <div class="d-flex flex-wrap align-items-center justify-content-between fs-14 mb-2">
                            <span class="flex-fill">Total Categories</span>
                        </div>
                        <div class="d-flex flex-wrap align-items-center justify-content-between">
                            <h5 class="fw-semibold mb-0">{{ $categoriesCount ?? 0 }}</h5>
                            <div id="btcCoin"></div>
                        </div>
                    </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xxl-3 col-xl-3 col-lg-6 col-md-6">
        <div class="card custom-card purple_box_dash blue_box_dash">
            <div class="card-body">
                <div class="d-flex align-items-top">
                    <a href="{{ url('/admin/product/list') }}">
                        <div class="flex-fill">
                            <div class="d-flex flex-wrap align-items-center justify-content-between fs-14 mb-2">
                                <span class="flex-fill">Total Products</span>
                            </div>
                            <div class="d-flex flex-wrap align-items-center justify-content-between">
                                <h5 class="fw-semibold mb-0">{{ $productsCount ?? 0 }}</h5>
                                <div id="glmCoin"></div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xxl-3 col-xl-3 col-lg-6 col-md-6">
        <div class="card custom-card purple_box_dash brown_box_dash">
            <div class="card-body">
                <div class="d-flex align-items-top">
                    <a href="{{ url('/admin/orders') }}">
                        <div class="flex-fill">
                            <div class="d-flex flex-wrap align-items-center justify-content-between fs-14 mb-2">
                                <span class="flex-fill">Total Revenue</span>
                            </div>
                            <div class="d-flex flex-wrap align-items-center justify-content-between">
                                <h5 class="fw-semibold mb-0">₹{{ $totalrevenue ?? 0 }}</h5>
                                <div id="glmCoin"></div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="row mb-4">
    <div class="col-xl-12">
        <div class="card custom-card shadow-lg border-0">
            <div class="card-header bg-gradient-primary text-white border-0">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="card-title">
                        <h4 class="fw-bold mb-1 text-white">
                            <i class="ri-dashboard-3-line me-2"></i>Order Items Analytics
                        </h4>
                        <p class="mb-0 opacity-75">Real-time tracking of order items by status</p>
                    </div>
                    <div class="btn-list">
                        <button type="button" class="btn btn-warning btn-sm" id="refreshChartBtn">
                            <i class="ri-refresh-line me-1"></i>Refresh
                        </button>
                    </div>
                </div>
            </div>
            
            
            <div class="card-body bg-light">
                <div class="row mb-4">
                    <div class="col-lg-3 col-md-6 mb-3">
                        <label for="chartDateFrom" class="form-label fw-semibold">
                            <i class="ri-calendar-line me-1"></i>Date From
                        </label>
                        <input type="date" class="form-control form-control-lg border-0 shadow-sm" id="chartDateFrom" name="date_from">
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <label for="chartDateTo" class="form-label fw-semibold">
                            <i class="ri-calendar-line me-1"></i>Date To
                        </label>
                        <input type="date" class="form-control form-control-lg border-0 shadow-sm" id="chartDateTo" name="date_to">
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <label for="chartStatusFilter" class="form-label fw-semibold">
                            <i class="ri-filter-3-line me-1"></i>Status Filter
                        </label>
                        <select class="form-control form-control-lg border-0 shadow-sm" id="chartStatusFilter" name="status">
                            <option value="">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="shipped">Shipped</option>
                            <option value="delivered">Delivered</option>
                            <option value="refunded">Refunded</option>
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3 d-flex align-items-end">
                        <button type="button" class="btn btn-primary btn-lg w-100 shadow-sm" id="applyFiltersBtn">
                            <i class="ri-filter-3-fill me-2"></i>Apply Filters
                        </button>
                    </div>
                </div>

                <!-- Chart Container -->
                <div class="row g-3">
                    <div class="col-xxl-5 col-xl-6 col-lg-6">
                        <div class="chart-container bg-white rounded-3 shadow-sm p-4" style="position: relative; height: 420px;">
                            <canvas id="orderItemsDoughnut"></canvas>
                        </div>
                    </div>
                    <div class="col-xxl-7 col-xl-6 col-lg-6">
                        <div class="chart-container bg-white rounded-3 shadow-sm p-4" style="position: relative; height: 420px;">
                            <canvas id="orderItemsBar"></canvas>
                        </div>
                    </div>
                    <div class="col-xl-5 col-lg-5">
                        <div class="chart-summary bg-white rounded-3 shadow-sm p-4">
                            <div class="d-flex align-items-center mb-4">
                                <i class="ri-bar-chart-box-line text-primary me-2 fs-4"></i>
                                <h5 class="fw-bold mb-0 text-dark">Status Summary</h5>
                            </div>
                            
                            <div class="summary-item d-flex justify-content-between align-items-center mb-3 p-3 rounded-2" style="background: linear-gradient(135deg, #FFE0E0 0%, #FFB3B3 100%);">
                                <span class="d-flex align-items-center fw-semibold">
                                    <span class="status-indicator me-3 shadow-sm" style="background-color: #FF6B6B;"></span>
                                    <span class="text-dark">Pending</span>
                                </span>
                                <span class="fw-bold fs-5" style="color: #FF5252;" id="pendingCount">0</span>
                            </div>
                            
                            <div class="summary-item d-flex justify-content-between align-items-center mb-3 p-3 rounded-2" style="background: linear-gradient(135deg, #E0F7F4 0%, #B3E8E1 100%);">
                                <span class="d-flex align-items-center fw-semibold">
                                    <span class="status-indicator me-3 shadow-sm" style="background-color: #4ECDC4;"></span>
                                    <span class="text-dark">Shipped</span>
                                </span>
                                <span class="fw-bold fs-5" style="color: #26A69A;" id="shippedCount">0</span>
                            </div>
                            
                            <div class="summary-item d-flex justify-content-between align-items-center mb-3 p-3 rounded-2" style="background: linear-gradient(135deg, #E3F2FD 0%, #BBDEFB 100%);">
                                <span class="d-flex align-items-center fw-semibold">
                                    <span class="status-indicator me-3 shadow-sm" style="background-color: #45B7D1;"></span>
                                    <span class="text-dark">Delivered</span>
                                </span>
                                <span class="fw-bold fs-5" style="color: #2196F3;" id="deliveredCount">0</span>
                            </div>
                            
                            <div class="summary-item d-flex justify-content-between align-items-center mb-3 p-3 rounded-2" style="background: linear-gradient(135deg, #E8F5E8 0%, #C8E6C9 100%);">
                                <span class="d-flex align-items-center fw-semibold">
                                    <span class="status-indicator me-3 shadow-sm" style="background-color: #96CEB4;"></span>
                                    <span class="text-dark">Refunded</span>
                                </span>
                                <span class="fw-bold fs-5" style="color: #66BB6A;" id="refundedCount">0</span>
                            </div>
                            
                            <hr class="my-4">
                            
                            <div class="summary-item d-flex justify-content-between align-items-center p-3 rounded-2 bg-gradient-primary text-white">
                                <span class="fw-bold fs-6">
                                    <i class="ri-dashboard-line me-2"></i>Total Items
                                </span>
                                <span class="fw-bold fs-4" id="totalItemsCount">0</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-7 col-lg-7">
                        <div class="chart-summary bg-white rounded-3 shadow-sm p-4">
                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <div class="d-flex align-items-center">    
                                    <i class="ri-bar-chart-box-line text-primary me-2 fs-4"></i>
                                    <h5 class="fw-bold mb-0 text-dark">Recent Orders</h5>
                                </div>
                                <a href="{{ route('admin-orders.index') }}" class="btn btn-primary ">View Orders</a>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Sr</th>
                                            <th>Order No</th>
                                            <th>Customer</th>
                                            <th>Total Amount</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(!empty($latestOrders))
                                            @foreach($latestOrders as $i=>$latestOrder)
                                                <tr>
                                                    <td>
                                                        {{ $i+1 }}
                                                    </td>
                                                    <td>
                                                        <a target="_blank" href="{{ route('admin-orders.view', base64_encode($latestOrder->id)) }}">{{ $latestOrder->order_number }}</a>
                                                    </td>
                                                    <td>
                                                        {{ @$latestOrder->user->name }}
                                                    </td>
                                                    <td>
                                                        ₹{{ @$latestOrder->total }}
                                                    </td>
                                                    <td>
                                                        {{ @$latestOrder->created_at->format('d M, Y h:i a') }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- End::row-1 -->
@endsection

@push('scripts')
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
<style>
    .card.custom-card.purple_box_dash {
    background-color: #5e35b1;
    color: #fff;
}
.purple_box_dash a {
    color: #fff;
}
.purple_box_dash span.flex-fill {
    font-size: 16px;
}
.purple_box_dash .card-body {
    padding: 30px !important;
    color: #fff !important;
}
.purple_box_dash:before {
    content: "";
    position: absolute;
    width: 210px;
    height: 210px;
    border-radius: 50%;
    top: -125px;
    right: -15px;
    opacity: .5;
    background: #4527a0;
}
.purple_box_dash:after {
    content: "";
    position: absolute;
    width: 210px;
    height: 210px;
    border-radius: 50%;
    top: -85px;
    right: -95px;
    background: #4527a0;
}
.teal_box_dash {
    background-color: #1e88e5 !important;
}
.teal_box_dash:before {
    background: #1565c0;
}
.teal_box_dash:after {
    background: #1565c0;
}
 
.blue_box_dash {
    background-color: #4371f9 !important;
}
.blue_box_dash:before {
    background: #3260e9;
}
.blue_box_dash:after {
    background: #2656e7;
}
.brown_box_dash {
    background-color: #d48686 !important;
}
.brown_box_dash:before {
    background: #c97575;
}
.brown_box_dash:after {
    background: #cb7373;
}
</style>

<style>
/* Enhanced Status Indicators */
.status-indicator {
    width: 16px;
    height: 16px;
    border-radius: 50%;
    display: inline-block;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Chart Summary Styling */
.chart-summary {
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    border: 1px solid rgba(0,0,0,0.05);
    transition: all 0.3s ease;
}

.chart-summary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}

.summary-item {
    transition: all 0.3s ease;
    border: 1px solid transparent;
}

.summary-item:hover {
    transform: translateX(5px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

/* Chart Container */
.chart-container {
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    border: 1px solid rgba(0,0,0,0.05);
    transition: all 0.3s ease;
}

.chart-container:hover {
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}

/* Card Enhancements */
.card.custom-card {
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
    border-radius: 15px;
    overflow: hidden;
}

.card.custom-card:hover {
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
    transform: translateY(-3px);
}

/* Button Enhancements */
.btn-wave {
    position: relative;
    overflow: hidden;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-wave:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.15);
}

.btn-wave::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.3);
    transform: translate(-50%, -50%);
    transition: width 0.6s, height 0.6s;
}

.btn-wave:active::before {
    width: 300px;
    height: 300px;
}

/* Form Controls */
.form-control-lg {
    border-radius: 10px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.form-control-lg:focus {
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    border-color: #0d6efd;
    transform: translateY(-1px);
}

/* Background Gradients */
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.bg-light-warning {
    background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
}

.bg-light-info {
    background: linear-gradient(135deg, #d1ecf1 0%, #74b9ff 100%);
}

.bg-light-success {
    background: linear-gradient(135deg, #d4edda 0%, #00b894 100%);
}

.bg-light-danger {
    background: linear-gradient(135deg, #f8d7da 0%, #e17055 100%);
}

/* Loading Animation */
.loading-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.9);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
    border-radius: 15px;
}

.spinner {
    width: 50px;
    height: 50px;
    border: 4px solid #f3f3f3;
    border-top: 4px solid #667eea;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Responsive Design */
@media (max-width: 768px) {
    .chart-container {
        padding: 15px;
        height: 350px !important;
    }
    .chart-summary {
        padding: 20px;
        margin-top: 20px;
    }
    .summary-item {
        padding: 12px;
        margin-bottom: 10px;
    }
    .card.custom-card {
        margin-bottom: 20px;
    }
}

@media (max-width: 576px) {
    .chart-container {
        height: 300px !important;
    }
    .summary-item {
        padding: 10px;
    }
    .btn-list {
        flex-direction: column;
        gap: 10px;
    }
}

/* Animation for numbers */
@keyframes countUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.summary-item span:last-child {
    animation: countUp 0.6s ease-out;
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
}
</style>

<script>
$(document).ready(function() {
    let doughnutChart = null;
    let barChart = null;
    
    // Initialize chart on page load
    loadChartData();
    
    // Show loading overlay
    function showLoading() {
        const chartContainer = $('.chart-container');
        if (!chartContainer.find('.loading-overlay').length) {
            chartContainer.append('<div class="loading-overlay"><div class="spinner"></div></div>');
        }
    }
    
    // Hide loading overlay
    function hideLoading() {
        $('.loading-overlay').remove();
    }
    
    // Load chart data function
    function loadChartData() {
        const dateFrom = $('#chartDateFrom').val();
        const dateTo = $('#chartDateTo').val();
        const statusFilter = $('#chartStatusFilter').val();
        
        showLoading();
        
        // Add a small delay to show loading animation
        setTimeout(() => {
            $.ajax({
                url: '{{ route("admin-dashboard.order-items-data") }}',
                type: 'GET',
                data: {
                    date_from: dateFrom,
                    date_to: dateTo,
                    status: statusFilter
                },
                timeout: 10000, // 10 second timeout
                success: function(response) {
                    hideLoading();
                    if (response && response.success) {
                        updateChart(response.data);
                        updateSummary(response.data.summary, response.data.totalItems);
                    } else {
                        console.error('Error loading chart data:', response?.message || 'Unknown error');
                        showError('Failed to load chart data: ' + (response?.message || 'Unknown error'));
                    }
                },
                error: function(xhr, status, error) {
                    hideLoading();
                    console.error('AJAX Error:', error, xhr);
                    let errorMessage = 'Error loading chart data. Please try again.';
                    
                    if (xhr.status === 404) {
                        errorMessage = 'Chart data endpoint not found. Please check the route.';
                    } else if (xhr.status === 500) {
                        errorMessage = 'Server error occurred. Please try again later.';
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    
                    showError(errorMessage);
                    
                    // Show fallback data
                    showFallbackData();
                }
            });
        }, 500);
    }
    
    // Show fallback data when AJAX fails
    function showFallbackData() {
        const fallbackData = {
            chartData: {
                labels: ['Pending', 'Shipped', 'Delivered', 'Refunded'],
                datasets: [{
                    label: 'Order Items',
                    data: [0, 0, 0, 0],
                    backgroundColor: [
                        '#FF6B6B', // Coral Red for Pending
                        '#4ECDC4', // Teal for Shipped  
                        '#45B7D1', // Sky Blue for Delivered
                        '#96CEB4'  // Mint Green for Refunded
                    ],
                    borderColor: [
                        '#FF5252', // Darker Coral Red
                        '#26A69A', // Darker Teal
                        '#2196F3', // Darker Sky Blue  
                        '#66BB6A'  // Darker Mint Green
                    ],
                    borderWidth: 2
                }]
            },
            summary: { pending: 0, shipped: 0, delivered: 0, refunded: 0 },
            totalItems: 0
        };
        
        updateChart(fallbackData);
        updateSummary(fallbackData.summary, fallbackData.totalItems);
    }
    
    // Show error message
    function showError(message) {
        // Create a toast notification or alert
        if (typeof toastr !== 'undefined') {
            toastr.error(message);
        } else {
            // Create a custom notification
            const notification = $(`
                <div class="alert alert-danger alert-dismissible fade show position-fixed" 
                     style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                    <i class="ri-error-warning-line me-2"></i>${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `);
            $('body').append(notification);
            setTimeout(() => notification.remove(), 5000);
        }
    }
    
    // Show success message
    function showSuccess(message) {
        if (typeof toastr !== 'undefined') {
            toastr.success(message);
        } else {
            const notification = $(`
                <div class="alert alert-success alert-dismissible fade show position-fixed" 
                     style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                    <i class="ri-check-line me-2"></i>${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `);
            $('body').append(notification);
            setTimeout(() => notification.remove(), 3000);
        }
    }
    
    // Update chart function
    function updateChart(data) {
        const doughnutCtx = document.getElementById('orderItemsDoughnut').getContext('2d');
        const barCtx = document.getElementById('orderItemsBar').getContext('2d');
        
        // Destroy existing charts if they exist
        if (doughnutChart) { doughnutChart.destroy(); }
        if (barChart) { barChart.destroy(); }
        // Ensure data is properly formatted and numeric
        const chartData = {
            labels: data.chartData.labels || ['Pending', 'Shipped', 'Delivered', 'Refunded'],
            datasets: [{
                label: 'Order Items',
                data: (data.chartData.datasets[0].data || [0, 0, 0, 0]).map(val => {
                    const numVal = parseInt(val) || 0;
                    return isNaN(numVal) ? 0 : numVal;
                }),
                backgroundColor: [
                    '#FF6B6B', // Coral Red for Pending
                    '#4ECDC4', // Teal for Shipped  
                    '#45B7D1', // Sky Blue for Delivered
                    '#96CEB4'  // Mint Green for Refunded
                ],
                borderColor: [
                    '#FF5252', // Darker Coral Red
                    '#26A69A', // Darker Teal
                    '#2196F3', // Darker Sky Blue  
                    '#66BB6A'  // Darker Mint Green
                ],
                borderWidth: 2,
                hoverBackgroundColor: [
                    '#FF5252',
                    '#26A69A', 
                    '#2196F3',
                    '#66BB6A'
                ],
                hoverBorderColor: [
                    '#D32F2F',
                    '#00695C',
                    '#1976D2', 
                    '#388E3C'
                ],
                hoverBorderWidth: 3
            }]
        };
        
        // Create new chart
        const commonOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                        font: { size: 14, weight: 'bold' },
                        color: '#333'
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    borderColor: '#333',
                    borderWidth: 1,
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const raw = context.parsed;
                            const value = (raw && typeof raw === 'object') ? (parseInt(raw.y ?? raw.x ?? 0) || 0) : (parseInt(raw) || 0);
                            const total = context.dataset.data.reduce((a, b) => (parseInt(a) || 0) + (parseInt(b) || 0), 0);
                            const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : '0.0';
                            return `${label}: ${value} items (${percentage}%)`;
                        }
                    }
                }
            },
            animation: { animateRotate: true, duration: 1200, easing: 'easeInOutQuart' }
        };
        // Doughnut chart
        const doughnutOptions = Object.assign({}, commonOptions, { cutout: '60%' });
        doughnutChart = new Chart(doughnutCtx, {
            type: 'doughnut',
            data: chartData,
            options: doughnutOptions
        });

        // Bar chart with datalabels
        const barOptions = Object.assign({}, commonOptions, {
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } },
            plugins: Object.assign({}, commonOptions.plugins, {
                datalabels: {
                    display: true,
                    anchor: 'end',
                    align: 'end',
                    color: '#111',
                    padding: 4,
                    font: { weight: 'bold' },
                    formatter: function(value) {
                        const num = parseInt(value) || 0;
                        return num > 0 ? num : '';
                    }
                }
            })
        });
        barChart = new Chart(barCtx, {
            type: 'bar',
            data: chartData,
            options: barOptions
        });
    }
    
    // Update summary function
    function updateSummary(summary, totalItems) {
        // Ensure all values are properly formatted numbers
        const pending = parseInt(summary.pending) || 0;
        const shipped = parseInt(summary.shipped) || 0;
        const delivered = parseInt(summary.delivered) || 0;
        const refunded = parseInt(summary.refunded) || 0;
        const total = parseInt(totalItems) || 0;
        
        // Update with animation
        animateNumber('#pendingCount', pending);
        animateNumber('#shippedCount', shipped);
        animateNumber('#deliveredCount', delivered);
        animateNumber('#refundedCount', refunded);
        animateNumber('#totalItemsCount', total);
    }
    
    // Animate number counting
    function animateNumber(selector, targetValue) {
        $(selector).text(targetValue);
        return;
        const element = $(selector);
        const startValue = parseInt(element.text()) || 0;
        const duration = 1000;
        const increment = (targetValue - startValue) / (duration / 16);
        let currentValue = startValue;
        
        const timer = setInterval(() => {
            currentValue += increment;
            if ((increment > 0 && currentValue >= targetValue) || 
                (increment < 0 && currentValue <= targetValue)) {
                currentValue = targetValue;
                clearInterval(timer);
            }
            element.text(Math.round(currentValue));
        }, 16);
    }
    
    // Event handlers
    $('#applyFiltersBtn').on('click', function() {
        $(this).prop('disabled', true).html('<i class="ri-loader-4-line me-2 align-middle d-inline-block"></i>Loading...');
        loadChartData();
        setTimeout(() => {
            $(this).prop('disabled', false).html('<i class="ri-filter-3-fill me-2 align-middle d-inline-block"></i>Apply Filters');
        }, 1000);
    });
    
    $('#refreshChartBtn').on('click', function() {
        $(this).prop('disabled', true).html('<i class="ri-loader-4-line me-2 align-middle d-inline-block"></i>Refreshing...');
        // Clear filters
        $('#chartDateFrom').val('');
        $('#chartDateTo').val('');
        $('#chartStatusFilter').val('');
        loadChartData();
        setTimeout(() => {
            $(this).prop('disabled', false).html('<i class="ri-refresh-line me-2 align-middle d-inline-block"></i>Refresh');
        }, 1000);
    });
    
    // Enter key support for filters
    $('#chartDateFrom, #chartDateTo, #chartStatusFilter').on('keypress', function(e) {
        if (e.which === 13) { // Enter key
            $('#applyFiltersBtn').click();
        }
    });
    
    // Keyboard shortcuts
    $(document).on('keydown', function(e) {
        // Ctrl + R to refresh chart
        if (e.ctrlKey && e.key === 'r') {
            e.preventDefault();
            $('#refreshChartBtn').click();
        }
        // Ctrl + F to focus on first filter
        if (e.ctrlKey && e.key === 'f') {
            e.preventDefault();
            $('#chartDateFrom').focus();
        }
    });
    
    // Auto-refresh every 5 minutes
    setInterval(function() {
        loadChartData();
    }, 300000); // 5 minutes
    
    // Test connection button
    
    
    // Removed toggle handler since both charts are shown
    
    // Add tooltip for keyboard shortcuts
    $('#refreshChartBtn').attr('title', 'Refresh Chart (Ctrl + R)');
    $('#applyFiltersBtn').attr('title', 'Apply Filters (Enter)');
    $('#chartDateFrom').attr('title', 'Focus filters (Ctrl + F)');
    $('#toggleChartTypeBtn').attr('title', 'Toggle Chart Type');

    // Register datalabels plugin if available
    if (window.ChartDataLabels && window.Chart && Chart.register) {
        Chart.register(ChartDataLabels);
    }
});
</script>
@endpush