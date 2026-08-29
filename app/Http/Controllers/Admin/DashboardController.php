<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Models\Subscriber;
use App\Models\OrderNotifications;
use App\Models\OrderItem;
use App\Models\OrderStatus;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use App\Models\NewsletterSubscription;

class DashboardController extends Controller
{
    public function index()
    {
        try {

            $categoriesCount = Category::whereNull('parent_id')->count();
            $usersCount = User::where('is_deleted', 0)->where('user_role_id', config('constant.ROLE_ID.CUSTOMER_ROLE_ID'))->count();
            $productsCount = Product::count();
            $unreadNotificationsCount = OrderNotifications::where('user_id', auth()->id())->where('is_read', 0)->count();

            $totalrevenue = OrderItem::select(DB::raw('SUM(total) as total_revenue'))
            ->whereHas('orderStatus', function ($q) {
                $q->where('slug', 'delivered');
            })->first()?->toArray();

            $latestOrders = Order::with(['user'=>function($q){$q->select('id','name','email');}])->take(9)->orderBy('id','desc')->get();            

            $totalrevenue = !empty($totalrevenue['total_revenue']) ? round($totalrevenue['total_revenue'], 2) : 0;
            return view('admin.dashboard', compact('categoriesCount', 'productsCount', 'usersCount', 'unreadNotificationsCount','totalrevenue','latestOrders'));
        } catch (Exception $e) {
            Log::error($e);
            return redirect()->back()->with(['error' => 'Something is wrong', 'error_msg' => $e->getMessage()]);
        }
    }
    public function showHeaderNotifications()
    {
        // Get the current user
        $user = auth()->user();
        // Fetch unread notifications for the user
        $notifications = OrderNotifications::where('user_id', $user->id)->where('is_read', 0)->get();

        // Get the count of unread notifications
        $unreadNotificationsCount = $notifications->count();

        // Pass data to the view
        return view('admin.dashboard', compact('notifications', 'unreadNotificationsCount'));
    }

    public function markAsReadAjax($id)
    {
        // Find the notification by its ID
        $notification = OrderNotifications::findOrFail($id);
        if (!$notification) {
            return response()->json(['message' => 'Notification not found.'], 404);
        }
        // Mark the notification as read
        $notification->is_read = 1;
        $notification->save();

        // Return the updated unread notifications count
        $unreadCount = OrderNotifications::where('user_id', auth()->id())
            ->where('is_read', 0)
            ->count();

        // Return a JSON response with the unread notification count
        return response()->json(['unreadCount' => $unreadCount]);
    }


    public function subscribe(Request $request)
    {
        $DB = NewsletterSubscription::orderBy('id', 'desc');
        if ($request->all()) {
            $searchData            =    $request->all();
            unset($searchData['display']);
            unset($searchData['_token']);

            if ((!empty($searchData['date_from'])) && (!empty($searchData['date_to']))) {
                $dateS = $searchData['date_from'];
                $dateE = $searchData['date_to'];
                $DB->whereBetween('newsletter_subscriptions.created_at', [$dateS . " 00:00:00", $dateE . " 23:59:59"]);
            } elseif (!empty($searchData['date_from'])) {
                $dateS = $searchData['date_from'];
                $DB->where('newsletter_subscriptions.created_at', '>=', [$dateS . " 00:00:00"]);
            } elseif (!empty($searchData['date_to'])) {
                $dateE = $searchData['date_to'];
                $DB->where('newsletter_subscriptions.created_at', '<=', [$dateE . " 00:00:00"]);
            }
            foreach ($searchData as $fieldName => $fieldValue) {
                if ($fieldValue != "") {
                    if ($fieldName == "email") {
                        $DB->where("newsletter_subscriptions.email", 'like', '%' . $fieldValue . '%');
                    }
                }
            }
        }
        $subscribe =  $DB->orderBy('id', 'desc')->simplePaginate(10);
        //$subscribe = NewsletterSubscription::orderBy('id', 'desc')->simplePaginate(10);

        return view('admin.subscriber', compact('subscribe'));
    }

    public function updatesubscribe(Request $request, $id)
    {
        $request->validate([
            'email' => 'required|email|unique:subscribers,email,' . $id,
        ]);

        $subscriber = NewsletterSubscription::findOrFail($id);
        $subscriber->update(['email' => $request->email]);

        return response()->json(['status' => 'success', 'message' => 'Email updated successfully!']);
    }

    public function destroysubscribe($id)
    {
        $subscriber = NewsletterSubscription::findOrFail($id);
        $subscriber->delete();

        return response()->json(['status' => 'success', 'message' => 'Subscriber deleted successfully!']);
    }


    /**
     * Get order items data for dashboard charts
     */
    public function getOrderItemsData(Request $request)
    {
        try {
            $dateFrom = $request->input('date_from');
            $dateTo = $request->input('date_to');
            $statusFilter = $request->input('status');

            // Base query for order items (date-filtered only)
            $baseQuery = OrderItem::query();

            // Apply date filters (used for both chart and summary)
            if ($dateFrom && $dateTo) {
                $baseQuery->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59']);
            } elseif ($dateFrom) {
                $baseQuery->where('created_at', '>=', $dateFrom . ' 00:00:00');
            } elseif ($dateTo) {
                $baseQuery->where('created_at', '<=', $dateTo . ' 23:59:59');
            }

            // Clone base query for chart-specific filtering
            $chartQuery = (clone $baseQuery);

            // Apply status filter ONLY for chart data (not for summary)
            if ($statusFilter) {
                // If status filter is a bucket (pending/shipped/delivered/refunded),
                // translate to corresponding slugs and apply whereIn on IDs.
                $bucketToSlugs = [
                    'pending' => ['pending','received','confirmed','accepted','processing'],
                    'shipped' => ['shipped','in-transit','out-for-delivery','out_for_delivery'],
                    'delivered' => ['delivered','completed'],
                    'refunded' => ['refunded','returned','refund-pending','return-accepted','cancelled','cancelled_by_customer']
                ];

                if (is_string($statusFilter) && isset($bucketToSlugs[$statusFilter])) {
                    $ids = OrderStatus::whereIn('slug', $bucketToSlugs[$statusFilter])->pluck('id')->all();
                    if (!empty($ids)) {
                        $chartQuery->whereIn('order_status_id', $ids);
                    } else {
                        // Fall back to exact slug match if mapping returns empty
                        $statusId = OrderStatus::where('slug', $statusFilter)->value('id');
                        if ($statusId) {
                            $chartQuery->where('order_status_id', $statusId);
                        }
                    }
                } else {
                    // Numeric ID or exact slug that didn't match a bucket
                    if (is_numeric($statusFilter)) {
                        $chartQuery->where('order_status_id', $statusFilter);
                    } else if (is_string($statusFilter)) {
                        $statusId = OrderStatus::where('slug', $statusFilter)->value('id');
                        if ($statusId) {
                            $chartQuery->where('order_status_id', $statusId);
                        }
                    }
                }
            }

            // Get order items grouped by status
            // Chart counts (respect status filter)
            $orderItemCounts = $chartQuery->select('order_status_id', DB::raw('COUNT(*) as total'))
                ->groupBy('order_status_id')
                ->with(['orderStatus' => function ($q) {
                    $q->select('id', 'slug', 'name', 'color');
                }])
                ->get();

            // Format data for charts
            $chartData = [];
            $statusCounts = [];
            $totalItems = 0;

            foreach ($orderItemCounts as $item) {
                if ($item->orderStatus) {
                    $statusName = $item->orderStatus->name;
                    $statusSlug = $item->orderStatus->slug;
                    $count = $item->total;
                    $totalItems += $count;

                    $statusCounts[$statusSlug] = [
                        'name' => $statusName,
                        'count' => $count,
                        'color' => $item->orderStatus->color ?? '#007bff'
                    ];
                }
            }

            // Summary counts (IGNORE status filter; use date filters only)
            $summaryItemCounts = $baseQuery->select('order_status_id', DB::raw('COUNT(*) as total'))
                ->groupBy('order_status_id')
                ->with(['orderStatus' => function ($q) {
                    $q->select('id', 'slug', 'name');
                }])
                ->get();

            $summaryStatusCounts = [];
            foreach ($summaryItemCounts as $item) {
                if ($item->orderStatus) {
                    $summaryStatusCounts[$item->orderStatus->slug] = [
                        'name' => $item->orderStatus->name,
                        'count' => (int)($item->total ?? 0)
                    ];
                }
            }
            
            // Get specific status counts for main dashboard summary - check multiple possible slug variations
            $pendingCount = $summaryStatusCounts['pending']['count'] ?? $summaryStatusCounts['received']['count'] ?? $summaryStatusCounts['confirmed']['count'] ?? 0;
            $shippedCount = $summaryStatusCounts['Shipped']['count'] ?? $summaryStatusCounts['shipped']['count'] ?? $summaryStatusCounts['in-transit']['count'] ?? $summaryStatusCounts['out-for-delivery']['count'] ?? 0;
            $deliveredCount = $summaryStatusCounts['delivered']['count'] ?? $summaryStatusCounts['completed']['count'] ?? 0;
            $refundedCount = $summaryStatusCounts['refunded']['count'] ?? $summaryStatusCounts['returned']['count'] ?? $summaryStatusCounts['cancelled']['count'] ?? 0;

            // Debug logging
            /* Log::info('Dashboard Chart Data:', [
                'totalItems' => $totalItems,
                'statusCounts' => $statusCounts,
                'availableSlugs' => array_keys($statusCounts),
                'pendingCount' => $pendingCount,
                'shippedCount' => $shippedCount,
                'deliveredCount' => $deliveredCount,
                'refundedCount' => $refundedCount
            ]); */

            // Prepare chart data with better colors and ensure numeric values
            $chartData = [
                'labels' => ['Pending', 'Shipped', 'Delivered', 'Refunded'],
                'datasets' => [
                    [
                        'label' => 'Order Items',
                        'data' => [
                            (int)$pendingCount,
                            (int)$shippedCount,
                            (int)$deliveredCount,
                            (int)$refundedCount
                        ],
                        'backgroundColor' => [
                            '#FF6B6B', // Coral Red for Pending
                            '#4ECDC4', // Teal for Shipped  
                            '#45B7D1', // Sky Blue for Delivered
                            '#96CEB4'  // Mint Green for Refunded
                        ],
                        'borderColor' => [
                            '#FF5252', // Darker Coral Red
                            '#26A69A', // Darker Teal
                            '#2196F3', // Darker Sky Blue  
                            '#66BB6A'  // Darker Mint Green
                        ],
                        'borderWidth' => 2,
                        'hoverBackgroundColor' => [
                            '#FF5252',
                            '#26A69A',
                            '#2196F3',
                            '#66BB6A'
                        ],
                        'hoverBorderColor' => [
                            '#D32F2F',
                            '#00695C',
                            '#1976D2',
                            '#388E3C'
                        ],
                        'hoverBorderWidth' => 3
                    ]
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'chartData' => $chartData,
                    'statusCounts' => $statusCounts, // chart status counts (respects status filter)
                    'totalItems' => $totalItems,
                    'summary' => [
                        'pending' => $pendingCount,
                        'shipped' => $shippedCount,
                        'delivered' => $deliveredCount,
                        'refunded' => $refundedCount
                    ]
                ]
            ]);
        } catch (Exception $e) {
            Log::error('Dashboard order items data error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching order items data'
            ], 500);
        }
    }
}
