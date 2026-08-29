<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Config;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Currency;
use App\Models\OrderItem;
use App\Models\OrderStatus;
use App\Models\UserAddress;
use App\Helpers\EmailHelper;
use Illuminate\Http\Request;
use App\Exports\OrdersExport;
use App\Exports\OrderItemsExport;
use App\Models\RefundRequest;
use App\Models\WalletHistory;
use App\Models\InvoiceSetting;
use App\Mail\orderSuccessEmail;
use App\Models\RefundedHistory;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Validation\Rule;
use App\Models\OrderCancellation;
use Seshac\Shiprocket\Shiprocket;
use App\Models\OrderNotifications;
use App\Models\OrderStatusHistory;
use App\Services\ShiprocketService;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\ProductVariantCombination;
use Illuminate\Support\Facades\{Auth, Http, Log, DB, Mail};
use App\Services\MailService; 

class OrderController extends Controller
{
    public $model = 'orders';
    public $listRouteName;
    public $mailService ; 
    public function __construct(Request $request,MailService $mailService)
    {

        $this->middleware('permission:view_order', ['only' => ['index','items','view']]);

        $this->listRouteName = 'admin-orders.index';
        View()->share('model', $this->model);
        View()->share('listRouteName', $this->listRouteName);
        $this->mailService = $mailService; 
        //$this->request = $request;
    }

    public function index(Request $request)
    {
        $getStatusAccrdingtoOrderstatusForAdmin = Order::getStatusAccrdingtoOrderstatusForAdmin();
        $status_array = Order::getAllStatus();
        $status_id_array = Order::getAllStatusId();
        $basic_status = OrderStatus::orderBy('step', 'Asc')->get();
        $shipped_status = Order::getShippedStatus();
        $exchanged_status = Order::getExchangedStatus();

        $DB = Order::query()->with('items');
        $totalorder = Order::count();
        $totaldelivered = Order::where('status', 'delivered')->count();
        $totalreturned = Order::where('status', 'returned')->count();
        $totalcancelled = Order::where('status', 'cancelled')->count();
        //$totalrevenue = Order::where('status','delivered')->sum('total');
        $totalrevenue = Order::sum('total');
        $sortBy = $request->input('sortBy') ? $request->input('sortBy') : 'orders.created_at';
        $order = $request->input('order') ? $request->input('order') : 'desc';
        $offset = !empty($request->input('offset')) ? $request->input('offset') : 0;
        $limit =  !empty($request->input('limit')) ? $request->input('limit') : Config("Reading.records_per_page");


        // total revenue
        $total_revenue = OrderItem::select(DB::raw('SUM(total) as total_revenue'))
            ->whereHas('orderStatus', function ($q) {
                $q->where('slug', 'delivered');
            })->first()?->toArray();
        $totalrevenue = !empty($total_revenue['total_revenue']) ? round($total_revenue['total_revenue'], 2) : 0;

        // today's total revenue
        $today_total_revenue = OrderItem::select(DB::raw('SUM(total) as total_revenue'))
            ->whereHas('orderStatus', function ($q) {
                $q->where('slug', 'delivered');
            })->where(function ($query) {
                $query->whereDate('created_at', date('Y-m-d'))
                    ->orWhereDate('updated_at', date('Y-m-d'));
            })->first()?->toArray();
        $totalTodayRevenue = !empty($total_revenue['total_revenue']) ? round($today_total_revenue['total_revenue'], 2) : 0;


        $orderItemCounts = OrderItem::select('order_status_id', DB::raw('COUNT(*) as total'))
            ->groupBy('order_status_id')->with(['orderStatus' => function ($q) {
                $q->select('id', 'slug', 'name');
            }])
            ->get()?->toArray();

        $totalItemCount = 0;
        $itemCounts = [];
        $excludeStatusCount = ['cancelled_by_customer', 'pending', 'accepted', 'processing', 'in-transit', 'out-for-delivery', 'return-requested', 'return-accepted', 'refund-pending'];
        if (!empty($orderItemCounts)) {
            foreach ($orderItemCounts as $orderItemCount) {
                $totalItemCount += $orderItemCount['total'] ?? 0;
                if (!empty($orderItemCount['order_status']) && !in_array($orderItemCount['order_status']['slug'], $excludeStatusCount)) {
                    $statusName = $orderItemCount['order_status']['slug'] != 'cancelled' ? $orderItemCount['order_status']['name'] : 'Cancelled';
                    $itemCounts['Total ' . $statusName . ' Items'] = $orderItemCount['total'] ?? 0;
                }
            }
        }


        $orderItemTodaysCounts = OrderItem::select('order_status_id', DB::raw('COUNT(*) as total'))
            ->groupBy('order_status_id')->with(['orderStatus' => function ($q) {
                $q->select('id', 'slug', 'name');
            }])
            ->whereDate('created_at', '=', date('Y-m-d'))
            ->orWhereDate('updated_at', '=', date('Y-m-d'))
            ->get()?->toArray();

        $totalItemTodayCount = 0;
        $itemTodaysCounts = [];
        if (!empty($orderItemTodaysCounts)) {
            foreach ($orderItemTodaysCounts as $orderItemTodayCount) {
                $totalItemTodayCount += $orderItemTodayCount['total'] ?? 0;
                if (!empty($orderItemTodayCount['order_status']) && !in_array($orderItemTodayCount['order_status']['slug'], $excludeStatusCount)) {
                    $statusName = $orderItemTodayCount['order_status']['slug'] != 'cancelled' ? $orderItemTodayCount['order_status']['name'] : 'Cancelled';
                    $itemTodaysCounts['Total ' . $statusName . ' Items'] = $orderItemTodayCount['total'] ?? 0;
                }
            }
        }


        if ($request->all()) {
            $searchData = $request->all();
            unset($searchData['display']);
            unset($searchData['_token']);
            if (isset($searchData['order'])) {
                unset($searchData['order']);
            }
            if (isset($searchData['sortBy'])) {
                unset($searchData['sortBy']);
            }
            if (isset($searchData['offset'])) {
                unset($searchData['offset']);
            }
            if (isset($searchData['limit'])) {
                unset($searchData['limit']);
            }
            if ((!empty($searchData['date_from'])) && (!empty($searchData['date_to']))) {
                $dateS = $searchData['date_from'];
                $dateE = $searchData['date_to'];
                $DB->whereBetween('orders.created_at', [$dateS . " 00:00:00", $dateE . " 23:59:59"]);
            } elseif (!empty($searchData['date_from'])) {
                $dateS = $searchData['date_from'];
                $DB->where('orders.created_at', '>=', [$dateS . " 00:00:00"]);
            } elseif (!empty($searchData['date_to'])) {
                $dateE = $searchData['date_to'];
                $DB->where('orders.created_at', '<=', [$dateE . " 00:00:00"]);
            }
            foreach ($searchData as $fieldName => $fieldValue) {
                if ($fieldValue != "") {
                    if ($fieldName == "order_number") {
                        $DB->where("orders.order_number", 'like', '%' . $fieldValue . '%');
                    }
                    if ($fieldName == "status") {
                        $DB->where("orders.status", $fieldValue);
                    }
                }
            }
        }

        // $results = $DB->orderBy($sortBy, $order)->offset($offset)->limit($limit)->get();
        $results = $DB->orderBy($sortBy, $order)->paginate($limit)->appends(request()->query());

        $orderItemStatusCountArr = array();
        if(!empty($results)) {
            foreach($results as  $result){
               // $orderItemStatusCount = DB::query("select status,count(status) from order_items where order_id=15 group by status");
                $orderItemStatusCount = OrderItem::select(DB::raw('status'))->where('order_id', $result->id)->get()?->toArray();
                $i = 1;
                foreach($orderItemStatusCount as $key => $statusArr){
                    if(isset($orderItemStatusCountArr[$result->id][$statusArr['status'] ])){
                        $i++;
                        $orderItemStatusCountArr[$result->id][$statusArr['status']] = $i;
                    } else {
                        $orderItemStatusCountArr[$result->id][$statusArr['status']] = $i;
                    }
                }
            }
        }

        
        $totalResults = $DB->count();
        if ($request->ajax()) {
            return  View("admin.$this->model.load_more_data", compact('orderItemStatusCountArr','getStatusAccrdingtoOrderstatusForAdmin','status_id_array','results', 'totalResults', 'status_array', 'totalorder', 'totaldelivered', 'totalreturned', 'totalcancelled', 'totalrevenue', 'basic_status', 'shipped_status', 'exchanged_status', 'totalItemCount', 'itemCounts', 'itemTodaysCounts', 'totalItemTodayCount', 'orderItemTodaysCounts', 'totalTodayRevenue'));
        } else {
            return  View("admin.$this->model.index", compact('orderItemStatusCountArr','getStatusAccrdingtoOrderstatusForAdmin','status_id_array','results', 'totalResults', 'status_array', 'totalorder', 'totaldelivered', 'totalreturned', 'totalcancelled', 'totalrevenue', 'basic_status', 'shipped_status', 'exchanged_status', 'totalItemCount', 'itemCounts', 'itemTodaysCounts', 'totalItemTodayCount', 'orderItemTodaysCounts', 'totalTodayRevenue'));
        }
    }

    public function items(Request $request)
    {
        //$status_array = Order::getAllStatus();
        $status_array = OrderStatus::pluck('name', 'id');

        //prx($status_array);
        $DB = OrderItem::query()->with(['order', 'product', 'orderStatusHistory', 'cancelRequest']);


        $sortBy = $request->input('sortBy') ? $request->input('sortBy') : 'order_items.created_at';
        $order = $request->input('order') ? $request->input('order') : 'desc';
        $offset = !empty($request->input('offset')) ? $request->input('offset') : 0;
        $limit =  !empty($request->input('limit')) ? $request->input('limit') : Config("Reading.records_per_page");
        if ($limit > 500) {
            $limit = 500;
        }
        // Overall counts by item status
        $orderItemCounts = OrderItem::select('order_status_id', DB::raw('COUNT(*) as total'))
            ->groupBy('order_status_id')
            ->with(['orderStatus' => function ($q) {
                $q->select('id', 'slug', 'name');
            }])
            ->get()?->toArray();

        $totalItemCount = 0;
        $itemCounts = [];
        $excludeStatusCount = ['cancelled_by_customer', 'pending', 'accepted', 'processing', 'in-transit', 'out-for-delivery', 'return-requested', 'return-accepted', 'refund-pending'];
        if (!empty($orderItemCounts)) {
            foreach ($orderItemCounts as $orderItemCount) {
                $totalItemCount += $orderItemCount['total'] ?? 0;
                if (!empty($orderItemCount['order_status']) && !in_array($orderItemCount['order_status']['slug'], $excludeStatusCount)) {
                    $statusName = $orderItemCount['order_status']['slug'] != 'cancelled' ? $orderItemCount['order_status']['name'] : 'Cancelled';
                    $itemCounts['Total ' . $statusName . ' Items'] = $orderItemCount['total'] ?? 0;
                }
            }
        }

        // Today's counts by item status
        $orderItemTodaysCounts = OrderItem::select('order_status_id', DB::raw('COUNT(*) as total'))
            ->groupBy('order_status_id')
            ->with(['orderStatus' => function ($q) {
                $q->select('id', 'slug', 'name');
            }])
            ->whereDate('created_at', '=', date('Y-m-d'))
            ->orWhereDate('updated_at', '=', date('Y-m-d'))
            ->get()?->toArray();

        $totalItemTodayCount = 0;
        $itemTodaysCounts = [];
        if (!empty($orderItemTodaysCounts)) {
            foreach ($orderItemTodaysCounts as $orderItemTodayCount) {
                $totalItemTodayCount += $orderItemTodayCount['total'] ?? 0;
                if (!empty($orderItemTodayCount['order_status']) && !in_array($orderItemTodayCount['order_status']['slug'], $excludeStatusCount)) {
                    $statusName = $orderItemTodayCount['order_status']['slug'] != 'cancelled' ? $orderItemTodayCount['order_status']['name'] : 'Cancelled';
                    $itemTodaysCounts['Total ' . $statusName . ' Items'] = $orderItemTodayCount['total'] ?? 0;
                }
            }
        }

        if ($request->all()) {
            $searchData = $request->all();
            unset($searchData['display']);
            unset($searchData['_token']);
            if (isset($searchData['order'])) {
                unset($searchData['order']);
            }
            if (isset($searchData['sortBy'])) {
                unset($searchData['sortBy']);
            }
            if (isset($searchData['offset'])) {
                unset($searchData['offset']);
            }
            if (isset($searchData['limit'])) {
                unset($searchData['limit']);
            }

            if ((!empty($searchData['date_from'])) && (!empty($searchData['date_to']))) {
                $dateS = $searchData['date_from'];
                $dateE = $searchData['date_to'];
                $DB->whereBetween('order_items.created_at', [$dateS . " 00:00:00", $dateE . " 23:59:59"]);
            } elseif (!empty($searchData['date_from'])) {
                $dateS = $searchData['date_from'];
                $DB->where('order_items.created_at', '>=', [$dateS . " 00:00:00"]);
            } elseif (!empty($searchData['date_to'])) {
                $dateE = $searchData['date_to'];
                $DB->where('order_items.created_at', '<=', [$dateE . " 23:59:59"]);
            }

            foreach ($searchData as $fieldName => $fieldValue) {
                if ($fieldValue != "") {
                    if ($fieldName == "order_number") {
                        $DB->whereHas('order', function ($q) use ($fieldValue) {
                            $q->where('orders.order_number', 'like', '%' . $fieldValue . '%');
                        });
                    }
                    if ($fieldName == "status") {
                        $DB->where("order_items.order_status_id", $fieldValue);
                    }
                    if ($fieldName == "product_name") {
                        $DB->whereHas('product', function ($q) use ($fieldValue) {
                            $q->where('products.name', 'like', '%' . $fieldValue . '%');
                        });
                    }
                }
            }
        }

        $results = $DB->orderBy($sortBy, $order)->paginate($limit)->appends(request()->query());
        $totalResults = $DB->count();

        if ($request->ajax()) {
            return  View("admin.order_items.load_more_data", compact('results', 'totalResults', 'status_array'));
        } else {
            return  View("admin.order_items.index", compact('results', 'totalResults', 'status_array', 'totalItemCount', 'itemCounts', 'itemTodaysCounts', 'totalItemTodayCount', 'orderItemTodaysCounts'));
        }
    }
    public function user_orders(Request $request, $id)
    {
        $user_id = base64_decode($id);
        $status_array = Order::getAllStatus();
        $DB = Order::where('user_id', $user_id)->with('items');
        $sortBy = $request->input('sortBy') ? $request->input('sortBy') : 'orders.created_at';
        $order = $request->input('order') ? $request->input('order') : 'desc';
        $offset = !empty($request->input('offset')) ? $request->input('offset') : 0;
        $limit =  !empty($request->input('limit')) ? $request->input('limit') : Config("Reading.records_per_page");

        if ($request->all()) {
            $searchData = $request->all();
            unset($searchData['display']);
            unset($searchData['_token']);
            if (isset($searchData['order'])) {
                unset($searchData['order']);
            }
            if (isset($searchData['sortBy'])) {
                unset($searchData['sortBy']);
            }
            if (isset($searchData['offset'])) {
                unset($searchData['offset']);
            }
            if (isset($searchData['limit'])) {
                unset($searchData['limit']);
            }
            if ((!empty($searchData['date_from'])) && (!empty($searchData['date_to']))) {
                $dateS = $searchData['date_from'];
                $dateE = $searchData['date_to'];
                $DB->whereBetween('orders.created_at', [$dateS . " 00:00:00", $dateE . " 23:59:59"]);
            } elseif (!empty($searchData['date_from'])) {
                $dateS = $searchData['date_from'];
                $DB->where('orders.created_at', '>=', [$dateS . " 00:00:00"]);
            } elseif (!empty($searchData['date_to'])) {
                $dateE = $searchData['date_to'];
                $DB->where('orders.created_at', '<=', [$dateE . " 00:00:00"]);
            }
            foreach ($searchData as $fieldName => $fieldValue) {
                if ($fieldValue != "") {
                    if ($fieldName == "order_number") {
                        $DB->where("orders.order_number", 'like', '%' . $fieldValue . '%');
                    }
                    if ($fieldName == "status") {
                        $DB->where("orders.status", $fieldValue);
                    }
                }
            }
        }

        $results = $DB->orderBy($sortBy, $order)->offset($offset)->limit($limit)->get();

        $totalResults = $DB->count();
        if ($request->ajax()) {
            return  View("admin.$this->model.load_more_data", compact('results', 'totalResults', 'status_array'));
        } else {
            return  View("admin.$this->model.user_orders", compact('results', 'totalResults', 'status_array'));
        }
    }
    public function view($id)
    {
        $getStatusAccrdingtoOrderstatusForAdmin = Order::getStatusAccrdingtoOrderstatusForAdmin();
        $id = base64_decode($id);
        $order = Order::find($id);
        $basic_status = OrderStatus::orderBy('step', 'Asc')->get();
        $orderstatushistory = OrderStatusHistory::where('order_id', $id)->orderBy('id', 'Asc')->get();
        $refundRequests = RefundRequest::where('order_id', $id)->orderBy('id', 'DESC')->get()->keyBy('order_item_id')->toArray();
       
        return View("admin.$this->model.view", ['getStatusAccrdingtoOrderstatusForAdmin'=>$getStatusAccrdingtoOrderstatusForAdmin,'order' => $order, 'orderstatushistory' => $orderstatushistory, 'basic_status' => $basic_status, 'refundRequests' => $refundRequests]);
    }

    // public function change_status(Request $request)
    // {
    //     DB::beginTransaction();
    //     try {
    //         $id = $request->id;
    //         $status = $request->status;
    //         $awb_number = $request->awb_number;
    //         $tracking_url = $request->tracking_url;
    //         $delivery_partner_name = $request->delivery_partner_name;
    //         $remark = strip_tags($request->remark);
    //         $order = Order::find($id);

    //         $orderStatusId = OrderStatus::where('slug', $status)->value('id');
    //         $orderstatushistory   = new OrderStatusHistory;
    //         // dd($status);



    //         // if(!file_exists(resource_path('views/emails/order-'.$status.'.blade.php')))
    //         // {
    //         //     $success = 'error';
    //         //     $message = "Invalid template.";
    //         // } else 
    //         // {
    //         //     if(!empty($order))
    //         //     {
    //         //         //$this->sendOrderStatusMail($order, $status);
    //         //       //  $this->viewOrderStatusMail($id, $status);
    //         //     }
    //         // $order->user_id = $order->user_id;
    //         $order->status = $status;
            
    //         $order->delivery_partner_name = $delivery_partner_name;
    //         $order->save();

    //         //  All Order Items update status
    //         $orderStatusArr = OrderStatus::where('slug',$status)->first();
    //         if(!empty($orderStatusArr)){
    //             $orderStatus =  $orderStatusArr->id;
    //         }
    //         OrderItem::where('order_id', $id)->update([
    //             'status' => $status,
    //             'remark' => $remark,
    //             'order_status_id' => $orderStatus
    //         ]);
    //         //  All Order Items update status

            
    //         $orderstatushistory->order_id = $id;
    //         $orderstatushistory->user_id = $order->user_id;
    //         $orderstatushistory->order_status_id = $orderStatusId ?? 0;
    //         $orderstatushistory->order_status = $status;
    //         $orderstatushistory->remark = $remark;
    //         $orderstatushistory->save();

    //         if ($status == 'accepted') {  // confirmed
    //             $shippingItems = [];
    //             $total_discount = 0;
    //             $order = Order::find($id);
    //             foreach ($order->items as $item) {
    //                 // Prepare shipping item details
    //                 $shippingItem = [
    //                     'name' => $item->product->name,
    //                     'sku' => $item->product->sku,
    //                     //'hsn' => $item->product->hsn,
    //                     'units' => $item->qty,
    //                     'selling_price' => $item->total,
    //                     'discount' => $order->coupon_discount,
    //                     'tax' => $item->gst
    //                 ];
    //                 $shippingItems[] = $shippingItem;
    //                 $total_discount += $order->coupon_discount;
    //             }

    //             // Fetch addresses
    //             //if (isset($order['billing_address_id'])) { 
    //               //  $billing_id = $order['billing_address_id'];
    //                 $billAddress = UserAddress::getShippingAddressByUserID('billing', $order->user_id);
    //            // }
    //            // if (isset($order['shipping_address_id'])) {
    //              //   $shipping_id = $order['shipping_address_id'];
    //                 $shipAddress = UserAddress::getShippingAddressByUserID('shipping', $order->user_id);
    //            // }
    //             // Create Shiprocket Order
    //             $addresses = array_merge($billAddress, $shipAddress);
    //             if ($order->payment_method == 'cod') {
    //                 $payment_method = 'cod';
    //             } else {
    //                 $payment_method = 'Prepaid';
    //             }
    //             $shipDetails = [
    //                 "order_id" => $id,
    //                 "order_date" => date('Y-m-d H:i:s', strtotime($order->created_at)),
    //                 "pickup_location" => "Primary",
    //                 "shipping_is_billing" => false,
    //                 "order_items" => $shippingItems,
    //                 "payment_method" => $payment_method,
    //                 "shipping_charges" => 0,
    //                 "total_discount" => $total_discount,
    //                 "sub_total" => $order->total,
    //                 "length" => 0.5,
    //                 "breadth" => 0.5,
    //                 "height" => 0.5,
    //                 "weight" => $order->total_weight
    //             ];
    //             $shippingDetails = array_merge($shipDetails, $addresses);
    //             $token = Shiprocket::getToken();
    //             // dd($token); // This will dump the token for testing
    //             $shiprocketResponse = Shiprocket::order($token)->create($shippingDetails);

    //             // Log::info('Shiprocket Response: ', $shiprocketResponse->toArray());

    //             //  dd($shiprocketResponse);
    //             // Check Shiprocket response
    //             // echo "<pre>"; print_r($shiprocketResponse); 
    //             //   die;

    //             // Get order and shipment IDs
    //             $shiprocket_order_id = !empty($shiprocketResponse['order_id']) ? $shiprocketResponse['order_id'] : '';
    //             $shiprocket_shipment_id = !empty($shiprocketResponse['shipment_id']) ? $shiprocketResponse['shipment_id'] : '';

    //             // Update order with Shiprocket IDs
    //             if (!empty($shiprocket_shipment_id)) {
    //                 Order::where('id', $id)->update([
    //                     'shiprocket_order_id' => $shiprocket_order_id,
    //                     'shiprocket_shipment_id' => $shiprocket_shipment_id
    //                 ]);
    //             }
    //         }
    //         if ($status == 'refunded') {
    //             $refundedhistory   = new RefundedHistory;
    //             $refundedhistory->user_id = $order->user_id;
    //             $refundedhistory->order_id = $id;
    //             $refundedhistory->amount = $order->total;
    //             $refundedhistory->order_status = $status;
    //             $refundedhistory->save();

    //             /*$UserDetails = User::where('id',$order->user_id)->first();
    //             if(!empty($UserDetails))
    //             {
    //                 $UserDetails->refund_wallet += $order->total;
    //                 $UserDetails->wallet_avl_balance += $order->total;
    //                 $UserDetails->save(); 
    //             }*/
    //         }
    //         DB::commit();
    //         $success = 'success';
    //         $message = "Order status changed successfully.";
    //         //}
    //     } catch (\Exception $e) {
    //         DB::rollback();
    //         $success = false;
    //         $message = $e->getMessage();
    //     }
    //     return response()->json(['success' => $success, 'message' => $message]);
    // }

    public function change_status(Request $request)
    {    
        DB::beginTransaction();
        try {
            $id = $request->id;
            $status = $request->status;
            $awb_number = $request->awb_number;
            $tracking_url = $request->tracking_url;
            $delivery_partner_name = $request->delivery_partner_name;
            $remark = strip_tags($request->remark);
            $courier_id = $request->courier_id; 
            $order = Order::find($id);
 
            $orderStatusId = OrderStatus::where('slug', $status)->value('id');
            $orderstatushistory   = new OrderStatusHistory;
            // dd($status);
            $order->status = $status;
            if($status == "shipped"){
                $order->awb_number = $awb_number;
                $order->tracking_url = $tracking_url;
                $order->delivery_partner_name = $delivery_partner_name;
            }
          
            $order->save();
            info("order data-----controller---",[$order]); 
           //  All Order Items update status
            $orderStatusArr = OrderStatus::where('slug',$status)->first();
            if(!empty($orderStatusArr)){
                $orderStatus =  $orderStatusArr->id;
            }
            OrderItem::where('order_id', $id)->update([
                'status' => $status,
                'remark' => $remark,
                'courier_id' => $courier_id,
                'order_status_id' => $orderStatus
            ]);
            //  All Order Items update status
 
           
            $orderstatushistory->order_id = $id;
            $orderstatushistory->user_id = $order->user_id;
            $orderstatushistory->order_status_id = $orderStatusId ?? 0;
            $orderstatushistory->order_status = $status;
            $orderstatushistory->remark = $remark;
            $orderstatushistory->save();
 
            if ($status == 'accepted') {  // confirmed
                $shippingItems = [];
                $total_discount = 0;
                $order = Order::find($id);
                foreach ($order->items as $item) {
                    // Prepare shipping item details
                    $shippingItem = [
                        'name' => $item->product->name,
                        'sku' => $item->product->sku,
                        //'hsn' => $item->product->hsn,
                        'units' => $item->qty,
                        'selling_price' => $item->total,
                        'discount' => $order->coupon_discount,
                        'tax' => $item->gst
                    ];
                    $shippingItems[] = $shippingItem;
                    $total_discount += $order->coupon_discount;
                }
 
                // Fetch addresses
                //if (isset($order['billing_address_id'])) {
                  //  $billing_id = $order['billing_address_id'];
                    $billAddress = UserAddress::getShippingAddressByUserID('billing', $order->user_id);
               // }
               // if (isset($order['shipping_address_id'])) {
                 //   $shipping_id = $order['shipping_address_id'];
                    $shipAddress = UserAddress::getShippingAddressByUserID('shipping', $order->user_id);
               // }
                // Create Shiprocket Order
                $addresses = array_merge($billAddress, $shipAddress);
                if ($order->payment_method == 'cod') {
                    $payment_method = 'cod';
                } else {
                    $payment_method = 'Prepaid';
                }
                $shipDetails = [
                    "order_id" => $id,
                    "order_date" => date('Y-m-d H:i:s', strtotime($order->created_at)),
                    "pickup_location" => "Primary",
                    "shipping_is_billing" => false,
                    "order_items" => $shippingItems,
                    "payment_method" => $payment_method,
                    "shipping_charges" => 0,
                    "total_discount" => $total_discount,
                    "sub_total" => $order->total,
                    "length" => 0.5,
                    "breadth" => 0.5,
                    "height" => 0.5,
                    "weight" => $order->total_weight
                ];
                $shippingDetails = array_merge($shipDetails, $addresses);
                $token = Shiprocket::getToken();
                // dd($token); // This will dump the token for testing
                $shiprocketResponse = Shiprocket::order($token)->create($shippingDetails);
 
                // Log::info('Shiprocket Response: ', $shiprocketResponse->toArray());
 
                //  dd($shiprocketResponse);
                // Check Shiprocket response
                // echo "<pre>"; print_r($shiprocketResponse);
                //   die;
 
                // Get order and shipment IDs
                $shiprocket_order_id = !empty($shiprocketResponse['order_id']) ? $shiprocketResponse['order_id'] : '';
                $shiprocket_shipment_id = !empty($shiprocketResponse['shipment_id']) ? $shiprocketResponse['shipment_id'] : '';
 
                // Update order with Shiprocket IDs
                if (!empty($shiprocket_shipment_id)) {
                    Order::where('id', $id)->update([
                        'shiprocket_order_id' => $shiprocket_order_id,
                        'shiprocket_shipment_id' => $shiprocket_shipment_id
                    ]);
                }
 
                $userData = User::where('id',$order->user_id)->first();
                $orderitem = OrderItem::where('order_id',$order->id)->get();
                info("order Item-----Controller---",[$orderitem]); 
                $this->mailService->OrderConfirmed($userData,$orderitem);
            }
            if ($status == 'refunded') {
                $refundedhistory   = new RefundedHistory;
                $refundedhistory->user_id = $order->user_id;
                $refundedhistory->order_id = $id;
                $refundedhistory->amount = $order->total;
                $refundedhistory->order_status = $status;
                $refundedhistory->save();
 
                /*$UserDetails = User::where('id',$order->user_id)->first();
                if(!empty($UserDetails))
                {
                    $UserDetails->refund_wallet += $order->total;
                    $UserDetails->wallet_avl_balance += $order->total;
                    $UserDetails->save();
                }*/
            }
            if ($status == "processing"){
                $userData = User::where('id',$order->user_id)->first();
                $orderitem = OrderItem::where('order_id',$order->id)->get();
                $this->mailService->orderProcessing($userData,$orderitem);
            }
            if($status == "shipped"){
                $userData = User::where('id',$order->user_id)->first();
                $orderitem = OrderItem::where('order_id',$order->id)->get();
                $this->mailService->orderShipped($userData,$orderitem);
            }
            if($status == "in-transit"){
                $userData = User::where('id',$order->user_id)->first();
                $orderitem = OrderItem::where('order_id',$order->id)->get();
                $this->mailService->orderInTransit($userData,$orderitem);
            }
            if($status == "out-for-delivery"){
                $userData = User::where('id',$order->user_id)->first();
                $orderitem = OrderItem::where('order_id',$order->id)->get();
                $this->mailService->orderOutForDelivery($userData,$orderitem);
            }

            if($status == "delivered"){
                $userData = User::where('id',$order->user_id)->first();
                $orderitem = OrderItem::where('order_id',$order->id)->get();
                $this->mailService->orderDelivered($userData,$orderitem);
            }
            DB::commit();
            $success = 'success';
            $message = "Order status changed successfully.";
            //}
        } catch (\Exception $e) {
            DB::rollback();
            $success = false;
            $message = $e->getMessage();
        }
        return response()->json(['success' => $success, 'message' => $message]);
    }
    public function change_item_status(Request $request)
    {
        DB::beginTransaction();
        try {
            $shippingType   = $request->shipping_type;
            $id             = $request->id;
            $remark         = strip_tags($request->remark);

            $status = $request->status;
            $slug   = OrderStatus::where('slug', $status)->select('slug', 'name')->first();

            // update order item status
            $orderItem  = OrderItem::where('id', $id)->with(['updatedBy','product' => function ($q) {
                $q->select('id', 'name','sku');
            }])->first();


            // get order user id
            $ordermain = Order::where('id', $orderItem->order_id)->first();

            if (strtolower($slug->slug) == 'shipped') {
                $orderItem->length = $request->length ?? 1;
                $orderItem->breadth = $request->breadth ?? 1;
                $orderItem->height = $request->height ?? 1;

                if ($shippingType == 'Manual') {
                    $orderItem->awb_number = $request->awb_number;
                    $orderItem->courier_id = $request->courier_id;
                    $orderItem->remark = $request->remark;
                } else {
                    $response = $this->shiprocket($orderItem);                    
                    if (isset($response['status_code']) && $response['status_code'] != 1) {
                        DB::rollback();
                        return response()->json(['success' => false, 'message' => !empty($response['message']) ? 'Shiprocket Error: '.$response['message'] : json_encode($response)]);
                    }
                    $orderItem->shiprocket_response = json_encode($response);
                }
            } else if ($slug->slug == 'refund-pending' || $slug->slug == 'refunded') {

                if ($slug->slug == 'refunded') {
                    if ($request->refund_type == 'wallet') {
                        $this->refundToUserWallet($orderItem);
                    } else if ($request->refund_type == 'original_payment' && $orderItem->order->payment_method == 'razorpay') {
                        if (!empty($item->order->razorpay_payment_id)) {
                            $resp = refundPayment($orderItem->order->razorpay_payment_id, $orderItem->total,  'item refund');
                            //$resp = refundPayment($item->order->razorpay_payment_id, 1,  $cr->reason);

                            if (isset($resp['success']) && $resp['success'] == true) {
                            } else {
                                $res = $this->refundToUserWallet($orderItem);
                            }
                        } else {
                            $res = $this->refundToUserWallet($orderItem);
                        }
                    }
                }
                $refundedhistory   = new RefundedHistory;
                $refundedhistory->user_id = $ordermain->user_id;
                $refundedhistory->order_id = $orderItem->order_id;
                $refundedhistory->order_item_id = $orderItem->id;
                $refundedhistory->amount = $orderItem->total;
                $refundedhistory->order_status = $slug->slug;
                $refundedhistory->save();
            }

            $orderItem->order_status_id = $slug->id;
            $orderItem->status = $slug->slug;
            $orderItem->shipping_type = $shippingType;
            $orderItem->updated_by = Auth::user()->id;
            $orderItem->save();

            // insert order status history
            $orderstatushistory   = new OrderStatusHistory;
            $orderstatushistory->order_status_id = $slug->id;
            $orderstatushistory->order_id = $orderItem->order_id;
            $orderstatushistory->order_item_id = $id;
            $orderstatushistory->user_id = $ordermain->user_id;
            $orderstatushistory->order_status = $slug->slug ?? '';
            $orderstatushistory->order_status_id = $slug->id ?? '';
            $orderstatushistory->remark = $remark;
            $orderstatushistory->save();

            // Order main status update according items
            $orderItemCount = OrderItem::where('order_id',$orderItem->order_id)->count();
            $orderPendingCount = OrderItem::where('order_id',$orderItem->order_id)->where('status','pending')->count();
            $orderAcceptedCount = OrderItem::where('order_id',$orderItem->order_id)->where('status','accepted')->count();
            $orderProcessingCount = OrderItem::where('order_id',$orderItem->order_id)->where('status','processing')->count();
            $orderShippedCount = OrderItem::where('order_id',$orderItem->order_id)->where('status','shipped')->count();
            $orderTransitCount = OrderItem::where('order_id',$orderItem->order_id)->where('status','in-transit')->count();
            $orderOutForDeliveryCount = OrderItem::where('order_id',$orderItem->order_id)->where('status','out-for-delivery')->count();
            $orderDeliveredCount = OrderItem::where('order_id',$orderItem->order_id)->where('status','delivered')->count();
            $orderReturnRequestCount = OrderItem::where('order_id',$orderItem->order_id)->where('status','return-requested')->count();
            $orderReturnAcceptCount = OrderItem::where('order_id',$orderItem->order_id)->where('status','return-accepted')->count();
            $orderRefundedCount = OrderItem::where('order_id',$orderItem->order_id)->where('status','refunded')->count();
            $orderRefundPendingCount = OrderItem::where('order_id',$orderItem->order_id)->where('status','refund-pending')->count();
            $orderCancelCount = OrderItem::where('order_id',$orderItem->order_id)->where('status','cancelled')->count();
            $orderCancelCustomerCount = OrderItem::where('order_id',$orderItem->order_id)->where('status','cancelled_by_customer')->count();
            
            if($orderItemCount==$orderPendingCount){ 
                $ordermainstatus = 'pending';
            } else if($orderItemCount==$orderAcceptedCount){ 
                $ordermainstatus = 'accepted';
            }  else if($orderItemCount==$orderProcessingCount){   
                $ordermainstatus = 'processing';
            }  else if($orderItemCount==$orderShippedCount){ 
                $ordermainstatus = 'shipped';
            } else if($orderItemCount==$orderTransitCount){  
                $ordermainstatus = 'in-transit';
            } else if($orderItemCount==$orderOutForDeliveryCount){ 
                $ordermainstatus = 'out-for-delivery';
            } else if($orderItemCount==$orderDeliveredCount){  
                $ordermainstatus = 'delivered';
            }else if($orderItemCount==$orderReturnRequestCount){  
                $ordermainstatus = 'return-requested';
            }  else if($orderItemCount==$orderReturnAcceptCount){  
                $ordermainstatus = 'return-accepted';
            } else if($orderItemCount==$orderRefundPendingCount){  
                $ordermainstatus = 'refund-pending';
            } else if($orderItemCount==$orderRefundedCount){  
                $ordermainstatus = 'refunded';
            } else if($orderItemCount==$orderCancelCount){  
                $ordermainstatus = 'cancelled';
            } else if($orderItemCount==$orderCancelCustomerCount){  
                $ordermainstatus = 'cancelled_by_customer';
            } else {  
                $ordermainstatus = 'pending';
            }

            Order::where('id', $ordermain->id)->update(['status' => $ordermainstatus]);
            
            EmailHelper::getEmailTemplate($slug, $orderItem, $ordermain);
            DB::commit();
            $success = 'success';
            $message = "Order status changed successfully.";
            // }
        } catch (Exception $e) {
            DB::rollback();
            $success = false;
            $message = $e->getMessage() . ' | Line: ' . $e->getLine() . ' | File: ' . $e->getFile();
        }
        return response()->json(['success' => $success, 'message' => $message]);
    }


    // prepare order data for shiprocket
   
    private function shiprocket($orderItem)
    {
        $billingAddress = !empty($orderItem->order->billing_address)
            ? json_decode($orderItem->order->billing_address, true)
            : [];

        $shippingAddress = !empty($orderItem->order->shipping_address)
            ? json_decode($orderItem->order->shipping_address, true)
            : [];
        $variantSku = $orderItem?->product?->sku;
        if(!empty($orderItem->product_variant_combination_id)){
            $variantSkuData = getProductVariantSku($orderItem->product_variant_combination_id);
            $variantSku = !empty($variantSkuData?->sku) ? $variantSku.'_'.$variantSkuData?->sku:'';            
        }

        $token = $this->getShiprocketToken();

        // Prepare the data array properly instead of embedding PHP variables in JSON strings
        $payload = [
            "order_id" => (string) $orderItem->order->id,
            "order_date" => $orderItem->order->created_at->format('Y-m-d H:i'),
            "pickup_location" => "warehouse",
            "address" => "F-158, First Floor, Near Chitrala Circle,",
            "address_2" => "SITAPURA",
            "city" => "Jaipur",
            "email" => "pyramidexportjpr@gmail.com",
            "phone" => "6375565058",
            "seller_name" => "Dinesh",
            "state" => "Rajasthan",
            "country" => "India",
            "status" => 2,
            "pin_code" => "302022",
            "lat" => "26.7799499",
            "long" => "75.8475853",
            "comment" => "",
            "reseller_name" => "VASVI",
            "company_name" => "VASVI",

            "billing_customer_name" => $billingAddress['billing_customer_name'] ?? 'Customer',
            "billing_last_name" => $billingAddress['billing_last_name'] ?? 'User',
            "billing_address" => $billingAddress['billing_address'] ?? '',
            "billing_address_2" => $billingAddress['billing_address_2'] ?? '',
            "billing_isd_code" => "",
            "billing_city" => $billingAddress['billing_city'] ?? '',
            "billing_pincode" => $billingAddress['billing_pincode'] ?? '',
            "billing_state" => $billingAddress['billing_state'] ?? '',
            "billing_country" => $billingAddress['billing_country'] ?? '',
            "billing_email" => $billingAddress['billing_email'] ?? '',
            "billing_phone" => $billingAddress['billing_phone'] ?? '',
            "billing_alternate_phone" => "",

            "shipping_is_billing" => false,
            "shipping_customer_name" => $shippingAddress['shipping_customer_name'] ?? '',
            "shipping_last_name" => $shippingAddress['shipping_last_name'] ?? '',
            "shipping_address" => $shippingAddress['shipping_address'] ?? '',
            "shipping_address_2" => $shippingAddress['shipping_address_2'] ?? '',
            "shipping_city" => $shippingAddress['shipping_city'] ?? '',
            "shipping_pincode" => $shippingAddress['shipping_pincode'] ?? '',
            "shipping_country" => $shippingAddress['shipping_country'] ?? '',
            "shipping_state" => $shippingAddress['shipping_state'] ?? '',
            "shipping_email" => $shippingAddress['shipping_email'] ?? '',
            "shipping_phone" => $shippingAddress['shipping_phone'] ?? '',

            "order_items" => [
                [
                    "name" => $orderItem->product->name ?? '',
                    "sku" => $variantSku ?? ('sku-' . ($orderItem->product->id ?? 'sku-'.time())),
                    "units" => (string) ($orderItem->quantity ?? 1),
                    "selling_price" => (string) ($orderItem->selling_price ?? 0),
                    "discount" => "",
                    "tax" => "",
                    "hsn" => ""
                ]
            ],
            "payment_method" => (!empty($orderItem->order->payment_status) && $orderItem->order->payment_status == 'paid') ? 'Prepaid' : 'COD',
            "shipping_charges" => "",
            "giftwrap_charges" => "",
            "transaction_charges" => "",
            "total_discount" => "",
            "sub_total" => (string) ($orderItem->total ?? 0),
            "length" => (string) ($orderItem->length ?? 10),
            "breadth" => (string) ($orderItem->breadth ?? 15),
            "height" => (string) ($orderItem->height ?? 10),
            "weight" => (string) ($orderItem->product->weight ?? 0.25),
            "ewaybill_no" => "",
            "customer_gstin" => "",
            "invoice_number" => "",
            "order_type" => ""
        ];

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://apiv2.shiprocket.in/v1/external/orders/create/adhoc',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token
            ],
        ]);

        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            $error_msg = curl_error($curl);
            curl_close($curl);
            return ['error' => $error_msg];
        }

        curl_close($curl);
        return json_decode($response,true);
    }

    
    private function getShiprocketToken()
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://apiv2.shiprocket.in/v1/external/auth/login',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode([
                "email" => "dzone.developers@gmail.com",
                "password" => "U#0TFopn#KVh8bpL"
            ]),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json'
            ],
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        $responseArr = json_decode($response, true);
        return $responseArr['token'] ?? '';
    }

    public function generateNewInvoiceOld(Request $request)
    {

        $orderDetail = Order::findOrFail($request->id);

        $settings = Setting::pluck('value', 'key');
        $invoiceSetting = InvoiceSetting::first();
        //$socialLinks = Setting::where([['key','like','Social%']])->get();
        $order = Order::with('items')->where('orders.id', $request->id)->leftJoin('users', 'users.id', 'orders.user_id')->select('orders.*', 'users.name as user_name', 'users.email as user_email')->first();
        $currency = Currency::where('currency_code', $order->currency_code)->value('currency_code');
        // dd($currency);
        $pdf = PDF::loadView('invoices.order_new_invoice', ['order' => $order, 'currency' => $currency, 'settings' => $settings, 'invoiceSetting' => $invoiceSetting]);
        // $path = Config('constant.ORDER_INVOICE_ROOT_PATH') . $request->id."_invoice.pdf";
        $filename = $request->id . "_invoice.pdf";

        if (!file_exists(Config('constant.ORDER_INVOICE_ROOT_PATH'))) {
            mkdir(Config('constant.ORDER_INVOICE_ROOT_PATH'), 0755, true);
        }

        $invoiceDirectory = Config('constant.ORDER_INVOICE_ROOT_PATH') . $filename;
        //$pdf->save($invoiceDirectory);
        return $pdf->stream($filename);
    }

    public function generateNewInvoice(Request $request,$id)
    {   
        $order = Order::where('orders.id', $id)->leftJoin('users', 'users.id', 'orders.user_id')->select('orders.*', 'users.name as user_name', 'users.email as user_email')->first();
       
        $checkout_data = OrderItem::with(['product', 'itemTax'])->where('order_id', $id)->get();
        $GSTIN = config('Site.GSTIN');
        $GSTIN = empty($GSTIN) ? $GSTIN : Setting::where('key','Site.GSTIN')->value('value');
        $ordertype = 'order';
        //echo $GSTIN;die;
        $currency = Currency::where('currency_code', $order->currency_code)->value('symbol');
        $supplySetting = InvoiceSetting::with(['country', 'state', 'city'])
            ->where('is_active', 1)
            ->first();
        //$randonNum = rand(1000, 9999);
        return view('invoices.order_new_invoice', [
            'checkout_data' => $checkout_data,
            'order' => $order,
            'currency' => $currency,
            'GSTIN' => $GSTIN,
            'supplySetting' => $supplySetting,
            'ordertype' => $ordertype
        ]);
        exit;
        $pdf = PDF::loadView('invoices.order_new_invoice', ['checkout_data' => $checkout_data, 'order' => $order, 'currency' => $currency, 'GSTIN' => $GSTIN, 'supplySetting' => $supplySetting,'ordertype'=>$ordertype]);
        //$path = Config('constant.ORDER_INVOICE_ROOT_PATH') . $request->id . $randonNum . "_invoice.pdf";
        /* $invoiceDirectory = Config('constant.ORDER_INVOICE_ROOT_PATH');
        if (!file_exists($invoiceDirectory)) {
            mkdir($invoiceDirectory, 0755, true);
        } */
        $filename = $request->id . "_invoice.pdf";
        return $pdf->stream($filename);
    }

    public function generateBulkItemsInvoice(Request $request)
    {
        if (empty($request->ids)) {
            //abort('500');
            return redirect()->back()->with("error", "Invalid request");
        }

        $itemsData = OrderItem::with(['product', 'itemTax', 'order'])->whereIn('id', $request->ids)->get();
        //prx($itemsData);
        $ordertype = 'item';
        $GSTIN = config('Site.GSTIN');
        $GSTIN = empty($GSTIN) ? $GSTIN : Setting::where('key','Site.GSTIN')->value('value');
        $currency = Currency::where('currency_code', 'INR')->value('symbol');        
        $supplySetting = InvoiceSetting::with(['country', 'state', 'city'])
            ->where('is_active', 1)
            ->first();

        $pdf = PDF::loadView('invoices.order_bulk_items_invoice', ['itemsData' => $itemsData, 'currency' => $currency, 'GSTIN' => $GSTIN, 'supplySetting' => $supplySetting,'ordertype'=>$ordertype]);
        
        $filename = $request->id . "_invoice.pdf";
        //return $pdf->stream($filename);
        $pdfContent = $pdf->download()->getOriginalContent();
        return response()->json([
            'file' => base64_encode($pdfContent),
            'filename' => "bulk_items_invoice.pdf"
        ]);
    }

    public function generateItemsInvoice(Request $request)
    {

        $order = Order::where('orders.id', $request->id)->leftJoin('users', 'users.id', 'orders.user_id')->select('orders.*', 'users.name as user_name', 'users.email as user_email')->first();
        $checkout_data = OrderItem::with(['product', 'itemTax'])->whereIn('id', $request->ids)->get();
        $GSTIN = config('Site.GSTIN');
        $GSTIN = empty($GSTIN) ? $GSTIN : Setting::where('key','Site.GSTIN')->value('value');
        $currency = Currency::where('currency_code', $order->currency_code)->value('symbol');
        $supplySetting = InvoiceSetting::with(['country', 'state', 'city'])
            ->where('is_active', 1)
            ->first();
        $ordertype = 'item';
        $randonNum = rand(1000, 9999);
        $pdf = PDF::loadView('invoices.order_new_invoice', ['checkout_data' => $checkout_data, 'order' => $order, 'currency' => $currency, 'GSTIN' => $GSTIN, 'supplySetting' => $supplySetting,'ordertype'=>$ordertype]);
        $path = Config('constant.ORDER_INVOICE_ROOT_PATH') . $request->id . $randonNum . "_invoice.pdf";
        //$invoiceDirectory = Config('constant.ORDER_INVOICE_ROOT_PATH');
        //if (!file_exists($invoiceDirectory)) {
        //   mkdir($invoiceDirectory, 0755, true);
        //}
        //$filename = $request->id . "_invoice.pdf";
        //return $pdf->stream($filename);

        
        $pdfContent = $pdf->download()->getOriginalContent();
        return response()->json([
            'file' => base64_encode($pdfContent),
            'filename' => $request->id . "_invoice.pdf"
        ]);
    }

    // public function generateBulkInvoice(Request $request)
    // {
    //     if(isset($request->order)){
    //         foreach($request->order as $key=> $order_id){
    //             $orders[$key] = Order::with('items')->where('orders.id',$order_id)->leftJoin('users','users.id','orders.user_id')->select('orders.*','users.name as user_name','users.email as user_email')->first();
    //             $currencies[$key] = Currency::where('currency_code',$orders[$key]->currency_code)->value('symbol') ;
    //         }
    //     }
    //     // dd($currency);
    //     $pdf = PDF::loadView('invoices.bulk_order_invoice', ['all_orders' => $orders,'currency' => $currencies ]);



    //     $invoiceDirectory = public_path(Config('constant.ORDER_INVOICE_ROOT_PATH'));
    //     $path = $invoiceDirectory . "bulk_invoice.pdf";

    //     if (!file_exists($invoiceDirectory)) {
    //         mkdir($invoiceDirectory, 0755, true);
    //     }

    //     $pdf->save($path);
    //     // $path = Config('constant.ORDER_INVOICE_ROOT_PATH') . $request->id."_invoice.pdf";
    //     // $invoiceDirectory = Config('constant.ORDER_INVOICE_ROOT_PATH');
    //     // if (!file_exists($invoiceDirectory)) {
    //     //     mkdir($invoiceDirectory, 0755, true);
    //     // }
    //     // $filename = "bulk_invoice.pdf";
    //     // return $pdf->stream($filename);

    //     $publicPath = Config('constant.ORDER_INVOICE_ROOT_PATH') ."bulk_invoice.pdf";
    //     $pdfUrl = asset($publicPath);  // Use asset() to generate a URL

    //     return response()->json(['pdf_url' => $pdfUrl]);
    // }

    public function generateBulkInvoice(Request $request)
    {
        if ($request->isMethod('post')) {
            $orders = [];
            $currencies = [];

            if ($request->has('order')) {
                foreach ($request->order as $order_id) {
                    $order = Order::with('items', 'currency')
                        ->where('orders.id', $order_id)
                        ->leftJoin('users', 'users.id', 'orders.user_id')
                        ->select('orders.*', 'users.name as user_name', 'users.email as user_email')
                        ->first();

                    $orders[] = $order;
                    $currencies[] = Currency::where('currency_code', $order->currency_code)->value('symbol');
                }
            }
            // dd($orders);
            $pdf = PDF::loadView('invoices.bulk_order_invoice', ['all_orders' => $orders, 'currency' => $currencies]);

            $invoiceDirectory = public_path(Config('constant.ORDER_INVOICE_ROOT_PATH'));

            if (!file_exists($invoiceDirectory)) {
                mkdir($invoiceDirectory, 0755, true);
            }

            $path = $invoiceDirectory . "bulk_invoice.pdf";
            $pdf->save($path);

            $pdfUrl = asset(Config('constant.ORDER_INVOICE_ROOT_PATH') . "bulk_invoice.pdf");

            return response()->json(['pdf_url' => $pdfUrl]);
        }
    }

    public function exportOrders()
    {
        return Excel::download(new OrdersExport, 'orders.xlsx');
    }

    public function exportOrderItems(Request $request)
    {
        return Excel::download(new OrderItemsExport($request->all()), 'order-items.xlsx');
    }


    public function updateCancelRequest(Request $request)
    {
        getOrderStatuss(0, 'cancelled_by_customer');
        if (!empty($request->id) && !empty($request->status)) {
            $cr = OrderCancellation::where('id', $request->id)->where('order_item_id', $request->order_item_id)->first();
            DB::beginTransaction();
            if (!empty($cr)) {
                if ($request->status == 1) {
                    $orderStatus = getOrderStatuss(0, 'cancelled_by_customer');
                    $statusId = !empty($orderStatus['id']) ? $orderStatus['id'] : 13; // cancelled_by_customer
                    $item = OrderItem::where('id', $request->order_item_id)->first();

                    if ($item->order->payment_method == 'razorpay') {
                        if (!empty($item->order->razorpay_payment_id)) {
                            $resp = refundPayment($item->order->razorpay_payment_id, $item->total,  $cr->reason);
                            //$resp = refundPayment($item->order->razorpay_payment_id, 1,  $cr->reason);

                            if (isset($resp['success']) && $resp['success'] == true) {
                                $cr->refund_status = 'refunded';
                                $cr->refund_response =  !empty($resp['data']) ? json_encode($resp['data']) : 'refund processed by razorepay';
                            } else {
                                $res = $this->refundToUserWallet($item);
                                if ($res) {
                                    $cr->refund_status = 'refunded';
                                    $cr->refund_response = 'refunded to user wallet';
                                } else {
                                    $cr->refund_status = 'refund failed';
                                    $cr->refund_response = 'failed to refund';
                                }
                            }
                        } else {
                            $res = $this->refundToUserWallet($item);
                            if ($res) {
                                $cr->refund_status = 'refunded';
                                $cr->refund_response = 'refunded to user wallet';
                            } else {
                                $cr->refund_status = 'refund failed';
                                $cr->refund_response = 'failed to refund';
                            }
                        }
                    } else if ($item->order->payment_method == 'wallet' || $item->order->payment_method == 'wallet_with_razorpay') {
                        $res = $this->refundToUserWallet($item);
                        if ($res) {
                            $cr->refund_status = 'refunded';
                            $cr->refund_response = 'refunded to user wallet';
                        } else {
                            $cr->refund_status = 'refund failed';
                            $cr->refund_response = 'failed to refund';
                        }
                    }


                    $item->update(['order_status_id' => $statusId, 'status' => $orderStatus['slug'] ?? 'cancelled_by_customer']);

                    if (!empty($item->product_variant_combination_id)) {
                        $productVarient = ProductVariantCombination::where('id', $item->product_variant_combination_id)->first();
                        if (!empty($productVarient)) {
                            $productVarient->qty += $item->qty;
                        }
                    }

                    OrderStatusHistory::create([
                        'user_id' => $item->order->user_id,
                        'order_id' => $item->order->id,
                        'order_item_id' => $item->id,
                        'order_status_id' => $statusId,
                        'order_status' => $orderStatus['slug'],
                        'remark' => $request->remark,
                    ]);
                }

                $cr->status = $request->status;
                $cr->admin_remark = $request->remark;
                $cr->updated_by = Auth::user()->id;
                $cr->save();

                DB::commit();

                return response()->json(['status' => 'success', 'message' => 'Request updated successful']);
            }
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'Something went wrong!']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Invalid request']);
        }
    }

    private function refundToUserWallet($item)
    {
        $user = User::where('id', $item->order->user_id)->first();
        if (!empty($user)) {
            $user->wallet_avl_balance += $item->total;
            $user->save();
            WalletHistory::create([
                'user_id' => $item->order->user_id,
                'amount' => $item->total,
                'type' => 'credit',
                'description' => '#' . $item->order->order_number . ' Item refund (id:' . $item->id . ')',
                'transaction_id' => $item->order->order_number . '-' . $item->id,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            return true;
        }
        return false;
    }

    public function updateReturnRequest(Request $request)
    {
        getOrderStatuss(0, 'cancelled_by_customer');
        if (!empty($request->id) && !empty($request->status)) {
            $cr = RefundRequest::where('id', $request->id)->where('order_item_id', $request->order_item_id)->first();
            DB::beginTransaction();

            if (!empty($cr)) {
                // return request accept
                $item = OrderItem::where('id', $request->order_item_id)->first();
                if ($request->status == 1) {
                    $orderStatus = getOrderStatuss(0, 'return-accepted');
                    $statusId = !empty($orderStatus['id']) ? $orderStatus['id'] : 9; // return-accepted
                    $item->update(['order_status_id' => $statusId, 'status' => $orderStatus['slug'] ?? 'return-accepted']);
                } else {
                    $orderStatus = getOrderStatuss(0, 'delivered');
                    $statusId = !empty($orderStatus['id']) ? $orderStatus['id'] : 7; // delivered
                    $item->update(['order_status_id' => $statusId, 'status' => $orderStatus['slug'] ?? 'return-accepted']);
                }

                $cr->status = $request->status;
                $cr->admin_remark = strip_tags($request->remark);
                $cr->updated_by = Auth::user()->id;
                $cr->save();

                DB::commit();

                return response()->json(['status' => 'success', 'message' => 'Request updated successful']);
            }
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'Something went wrong!']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Invalid request']);
        }
    }
}
