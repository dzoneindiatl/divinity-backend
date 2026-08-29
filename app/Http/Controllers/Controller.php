<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\path;
use Illuminate\Support\Facades\DB;
use Config;
use Mail;
use Request;
use Str;
use File;
use App\Models\User;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderTax;
use App\Models\OrderItem;
use App\Models\OrderItemTax;
use App\Models\Transaction;
use App\Mail\InvoiceEmail;
use App\Models\Currency;
use App\Models\Cart;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use App\Traits\HasPermissions;


class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests,HasPermissions;
    public function __construct() {
        
        //$this->applyPermissions();
	}

    public function buildTree($parentId = 0){
		$user_id	    =	Auth::user()->id;
		$user_role_id	=	Auth::user()->user_role_id;
		$branch         =   array();
		$elements       =   array();
		$superadmin = Config('constant.ROLE_ID.SUPER_ADMIN_ROLE_ID');
		if($user_role_id == $superadmin){
			$elements = DB::table("acls")
							->where("parent_id",$parentId)
							->orderBy('acls.module_order','ASC')
							->get();

		}else {
			if($parentId == 0){
				$elements = DB::table("acls")
							->where("parent_id",$parentId)
							->where("acls.id",DB::raw("(select admin_module_id from user_permissions where user_permissions.admin_module_id = acls.id AND is_active = 1 AND user_id = $user_id LIMIT 1)"))
							->orderBy('acls.module_order','ASC')
							->get();
			}else{
				$elements = 	DB::table("acls")
								->where("parent_id",$parentId)
								->where("acls.id",DB::raw("(select admin_sub_module_id from user_permission_actions where user_permission_actions.admin_sub_module_id = acls.id AND is_active = 1 AND user_id = $user_id LIMIT 1)"))
								->orderBy('acls.module_order','ASC')
								->get();
			}
		}

		foreach($elements as $element){
			if ($element->parent_id == $parentId){
				$children = $this->buildTree($element->id);
				if ($children){
					$element->children = $children;
				}
				$branch[] = $element;
			}
		}

		return $branch;
	}

	public function generateRandomPassword(){

		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%&*_";
		$password = substr( str_shuffle( $chars ), 0, 9 );
		return $password;
	}

	public function getSlug($title, $fieldName,$modelName,$limit = 30){
		$slug 		= 	 substr(Str::slug($title),0 ,$limit);
		$Model		=	 "\App\Models\\$modelName";
		$slugCount 	=    count($Model::where($fieldName, $slug)->get());
		if($slugCount > 0){
			$slugCount 	=    count($Model::where($fieldName,"LIKE", "%".$slug."%")->get());
			return $slug."-".$slugCount;
		}else {
			return $slug;
		}

	}//end getSlug()

	public function arrayStripTags($array)
    {
        $result = array();
        foreach ($array as $key => $value) {
            // Don't allow tags on key either, maybe useful for dynamic forms.
            $key = strip_tags($key, config('constants.ALLOWED_TAGS_XSS'));

            // If the value is an array, we will just recurse back into the
            // function to keep stripping the tags out of the array,
            // otherwise we will set the stripped value.
            if (is_array($value)) {
                $result[$key] = $this->arrayStripTags($value);
            } else {
                // I am using strip_tags(), you may use htmlentities(),
                // also I am doing trim() here, you may remove it, if you wish.
                $result[$key] = trim(strip_tags($value, config('constants.ALLOWED_TAGS_XSS')));
            }
        }

        return $result;

    }

	public function change_error_msg_layout($errors = array())
    {
        $response = array();
        $response["status"] = "error";
        if (!empty($errors)) {
            $error_msg = "";
            foreach ($errors as $errormsg) {
                $error_msg1 = (!empty($errormsg[0])) ? $errormsg[0] : "";
                $error_msg .= $error_msg1 . ", ";
            }
            $response["msg"] = trim($error_msg, ", ");
        } else {
            $response["msg"] = "";
        }
        $response["data"] = (object) array();
        $response["errors"] = $errors;
        return $response;
    }

    public function change_error_msg_layout_with_array($errors = array())
    {
        $response = array();
        $response["status"] = "error";
        if (!empty($errors)) {
            $error_msg = "";
            foreach ($errors as $errormsg) {
                $error_msg1 = (!empty($errormsg[0])) ? $errormsg[0] : "";
                $error_msg .= $error_msg1 . ", ";
            }
            $response["msg"] = trim($error_msg, ", ");
        } else {
            $response["msg"] = "";
        }
        $response["data"] = array();
        $response["errors"] = $errors;
        return $response;
    }

    public function sendMail($to, $fullName, $subject, $messageBody, $from = '', $files = false, $path = '', $attachmentName = '')
    {

        $from = Config::get("Site.from_email");

        $data = array();
        $data['to'] = $to;
        $data['from'] = (!empty($from) ? $from : Config::get("Site.from_email"));
        $data['fullName'] = $fullName;
        $data['subject'] = $subject;
        $data['filepath'] = $path;
        $data['attachmentName'] = $attachmentName;
        if ($files === false) {
            Mail::send('emails.template', array('messageBody' => $messageBody), function ($message) use ($data) {
                $message->to($data['to'], $data['fullName'])->from($data['from'])->subject($data['subject']);

            });
        } else {
            if ($attachmentName != '') {
                Mail::send('emails.template', array('messageBody' => $messageBody), function ($message) use ($data) {
                    $message->to($data['to'], $data['fullName'])->from($data['from'])->subject($data['subject'])->attach($data['filepath'], array('as' => $data['attachmentName']));
                });
            } else {
                Mail::send('emails.template', array('messageBody' => $messageBody), function ($message) use ($data) {
                    $message->to($data['to'], $data['fullName'])->from($data['from'])->subject($data['subject'])->attach($data['filepath']);
                });
            }
        }
    }

    public function generateOrderNumber($lastId = 0){
		if(!empty($lastId)){
			$orderNumber = "JJ-".($lastId + 10000);
			return $orderNumber;
			
		}
	}

    public function createOrderAndInvoice($data = [],$checkoutData,$checkoutItemData){
        // $checkoutData = session()->get('checkoutData') ?? [];
        // $checkoutItemData = session()->get('checkoutItemData') ?? [];
        // print_r($checkoutData);die;
        if(!empty($checkoutData)){
            //Create TOrder
            $obj    =   new Order;
            $obj->user_id  = auth()->guard('customer')->user()->id;
            $obj->address_id  = $checkoutData['address_id'];
            $obj->sub_total  = $checkoutData['sub_total'] ?? 0;
            $obj->total  = $checkoutData['total'] ?? 0;
            $obj->coupon_name  = $checkoutData['coupon_name'] ?? Null;
            $obj->coupon_discount  = $checkoutData['coupon_discount'] ?? 0;
            $obj->delivery  = $checkoutData['delivery'] ?? 0;
            $obj->payment_method  = $checkoutData['payment_method'] ?? Null;
            $obj->transaction_id  = $data['transaction_id'] ?? Null;
            $obj->currency_code  = session()->get('currency') ?? 'INR';
            $obj->order_number  = ' ';
            $obj->save();
            $lastId = $obj->id;
            if(!empty($lastId)){

                Order::where('id',$lastId)->update(['order_number' => $this->generateOrderNumber($lastId)]);

                if(!empty($checkoutData['tax'])){
                    foreach($checkoutData['tax'] as $tax){

                        $obj2   =   new OrderTax;
                        $obj2->order_id = $lastId;
                        $obj2->category_tax_id = $tax['category_tax_id'];
                        $obj2->tax_val = $tax['tax_val'];
                        $obj2->tax_price = $tax['tax_price'];
                        $obj2->save();
                    }
                }

                //Creating Order Items
                $checkoutItemData = session()->get('checkoutItemData') ?? [];
                if(!empty($checkoutItemData)){
                    foreach($checkoutItemData as $checkout){
                        $obj              =    new OrderItem;
                        $obj->order_id    = $lastId;
                        $obj->product_id    = $checkout['product_id'];
                        $obj->qty           = $checkout['quantity'];
                        $obj->sub_total  = $checkout['sub_total'] ?? 0;
                        $obj->total  = $checkout['total'] ?? 0;
                        $obj->coupon_name  = $checkout['coupon_name'] ?? Null;
                        $obj->coupon_discount  = $checkout['coupon_discount'] ?? 0;
                        $obj->delivery  = $checkout['delivery'] ?? 0;
                        $obj->status  = OrderItem::RECEIVED;
                        $obj->save();
                        $lastItemId = $obj->id;
                        if(!empty($lastItemId)){
                            if(!empty($checkout['tax'])){
                                foreach($checkout['tax'] as $tax){

                                    $obj1   =   new OrderItemTax;
                                    $obj1->order_item_id = $lastItemId;
                                    $obj1->category_tax_id = $tax['category_tax_id'];
                                    $obj1->tax_val = $tax['tax_val'];
                                    $obj1->tax_price = $tax['tax_price'];
                                    $obj1->save();
                                }
                            }

                        }
                    }
                }
                // Creating Transaction 
                $obj           =   new Transaction; 
                $obj->user_id  = auth()->guard('customer')->user()->id;
                $obj->reference_id  = $lastId;
                $obj->type  = Transaction::ORDER_TYPE;
                $obj->amount  = $checkoutData['total'] ?? 0;
                $obj->status  = Transaction::SUCCESS;
                $obj->transaction_id  = $data['transaction_id'] ?? Null;
                $obj->save();
            }
            //Generating Invoice
            $invoicePath = $this->generateInvoice($checkoutData,$checkoutItemData,$lastId);
            Order::where('id',$lastId)->update(['invoice_path' => $invoicePath]);

            // Send email to customer with the invoice as an attachment
            $this->sendInvoiceEmail($checkoutData,$checkoutItemData,$lastId);

            session()->forget('checkoutData');
            session()->forget('checkoutItemData');
            if($checkoutData['checkoutFrom'] == 'cartPage'){
                Cart::where('user_id',auth()->guard('customer')->user()->id)->delete();
            }

        }
    }

    protected function generateInvoice($checkoutData,$checkoutItemData,$lastId)
    {
        $order = Order::where('orders.id',$lastId)->leftJoin('users','users.id','orders.user_id')->select('orders.*','users.name as user_name','users.email as user_email')->first();
        $currency = Currency::where('currency_code',$order->currency_code)->value('symbol') ;
        $pdf = PDF::loadView('invoices.order_invoice', ['checkoutData' => $checkoutData,'checkoutItemData' => $checkoutItemData,'order' => $order,'currency' => $currency ]);
        $path = Config('constant.ORDER_INVOICE_ROOT_PATH') . $lastId."_invoice.pdf";
        $invoiceDirectory = Config('constant.ORDER_INVOICE_ROOT_PATH');
        if (!file_exists($invoiceDirectory)) {
            mkdir($invoiceDirectory, 0755, true);
        }
        $pdf->save($path);

        return $path;
    }

    protected function sendInvoiceEmail($checkoutData,$checkoutItemData,$lastId)
    {
        $order = Order::where('orders.id',$lastId)->leftJoin('users','users.id','orders.user_id')->select('orders.*','users.name as user_name','users.email as user_email')->first();
        $currency = Currency::where('currency_code',$order->currency_code)->value('symbol') ;
        $to = $order->user_email;
        $subject = 'Order Placed Successfully';
        $attachmentPath = Config('constant.ORDER_INVOICE_ROOT_PATH') . $lastId."_invoice.pdf";

        Mail::to($to)->send(new InvoiceEmail($subject, $attachmentPath, $order->user_name));
    }

    public function viewOrderStatusMail($orderId, $status) {
        
        $order = Order::find($orderId); // findOrFail ke bajaye find use karte hain    
       
        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }    
        $categories = Category::where(['is_active' => 1, 'is_deleted' => 0, 'parent_id' => null])->get();
        $subject = "Order " . $status;
        $template = "order-" . $status;
       
        // Email content generate karna
        $emailContent = view('emails.' . $template, ['order' => $order, 'categories' => $categories])->render();
    
        // Template ko display karna (browser mein)
        return response($emailContent)
            ->header('Content-Type', 'text/html');
    }
    
    public function sendOrderStatusMail($order, $status){
        $from = Config::get("Site.from_email");
        $categories = Category::where(['is_active'=>1, 'is_deleted'=>0, 'parent_id'=>null])->get();
        $subject = "Order ".$status;
        $template = "order-".$status;
        $data = array();
        $data['to'] = $order->user->email;
        //$data['to'] = 'jay@mailinator.com';
        $data['from'] = (!empty($from) ? $from : Config::get("Site.from_email"));
        $data['fullName'] = $order->user->name;
        $data['subject'] = $subject;

      // Invoice file path
    //$invoicePath = storage_path('invoices/invoice_' . $order->id . '.pdf'); // Adjust path as necessary

        Mail::send(
            'emails.'.$template,
            ['order' => $order, 'categories' => $categories],
            function ($message) use ($data) {
                $message->to($data['to'], $data['fullName'])->from($data['from'])->subject($data['subject']);
            }
        );
        // Mail::send(
        //     'emails.' . $template,
        //     ['order' => $order, 'categories' => $categories],
        //     function ($message) use ($data, $invoicePath) {
        //         $message->to($data['to'], $data['fullName'])
        //                 ->from($data['from'])
        //                 ->subject($data['subject']);
    
        //         // Attach invoice
        //         if (file_exists($invoicePath)) {
        //             $message->attach($invoicePath);
        //         }
        //     }
        // );
    }


}
