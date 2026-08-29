<?php
use App\Models\OrderStatus;
use App\Models\User;
use App\Models\Varient;
use App\Models\VariantValue;
use App\Models\Category;
use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Support\Str;
use App\Models\RefundRequest;
use App\Models\OrderCancellation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Models\ProductVariantValue;
use App\Models\ProductGraphics;
use App\Models\ProductVariant;
use App\Models\ProductVariantCombination;

if (!function_exists('getTotalvarientQty')) {
    function getTotalvarientQty($productId = 0)
    {
        $getTotalvarientQty = ProductVariantCombination::where('product_id', $productId)->sum('qty');
        $qty = 0;
        if(!empty($getTotalvarientQty)){
            $qty = $getTotalvarientQty;
        }
        return $qty;
       
    }
}

if (!function_exists('getOrderedVarientQty')) {
    function getOrderedVarientQty($productId = 0)
    {
        $getOrderedVarientQty = $users = DB::table('product_variant_combinations')
                            ->join('order_items', 'product_variant_combinations.id', '=', 'order_items.product_variant_combination_id')
                            ->where('product_variant_combinations.product_id', $productId)
                            ->sum('order_items.qty');
        $qty = 0;
        if(!empty($getOrderedVarientQty)){
            $qty = $getOrderedVarientQty;
        }
        return $qty;
       
    }
}



if (!function_exists('activeVarientByProductId')) {
    function activeVarientByProductId($productId = 0)
    {
        $activeVarientIdArr      = ProductVariantValue::where('is_main', 1)->where('product_id', $productId)->first();
        $activeVarientID = '';
        if(!empty($activeVarientIdArr)){
            $activeVarientID = $activeVarientIdArr->variant_value_id;
        }
        return $activeVarientID;
       
    }
}

if (!function_exists('allVarientByProductId')) {
    function allVarientByProductId($productId = 0)
    {
        $activeVarientIdObj      = ProductVariant::where('product_id', $productId)->get();
        $activeVarientIdArr = array();
        if(!empty($activeVarientIdObj)){
            foreach($activeVarientIdObj as $variantId){
                $activeVarientIdArr[] = $variantId->variant_id;
            }
        }
        return $activeVarientIdArr;
       
    }
}

if (!function_exists('getActiveFrontImg')) {
    function getActiveFrontImg($productId = 0,$varient_id=0)
    {
        $getActiveFrontImgArr      = ProductGraphics::where('status', 1)->where('is_front', 1)->where('product_id', $productId)->first();//->where('variant_id', $varient_id)
        $getActiveFrontImg = '';
        if(!empty($getActiveFrontImgArr)){
            $getActiveFrontImg = $getActiveFrontImgArr->graphic;
        }
        return $getActiveFrontImg;
       
    }
}

if (!function_exists('getActiveBackImg')) {
    function getActiveBackImg($productId = 0,$varient_id=0)
    {
        $getActiveBackImgArr      = ProductGraphics::where('status', 1)->where('is_back', 1)->where('product_id', $productId)->first();//->where('variant_id', $varient_id)
        $getActiveBackImg = '';
        if(!empty($getActiveBackImgArr)){
            $getActiveBackImg = $getActiveBackImgArr->graphic;
        }
        return $getActiveBackImg;
       
    }
}

if (!function_exists('getSmartCategoryUrl')) {
    function getSmartCategoryUrl($type, $category, $subcategory = null, $child = null)
    {
        $parentSlug = $category->slug;
        $childSlug = null;
        $subChildSlug = null;

        if ($type === 'category') {
            $firstSub = $category->subcategories->first();
            if ($firstSub) {
                $childSlug = $firstSub->slug;
                $firstChild = $firstSub->subcategories->first();
                if ($firstChild) {
                    $subChildSlug = $firstChild->slug;
                }
            }
        } elseif ($type === 'subcategory' && $subcategory) {
            $childSlug = $subcategory->slug;
            $firstChild = $subcategory->subcategories->first();
            if ($firstChild) {
                $subChildSlug = $firstChild->slug;
            }
        } elseif ($type === 'child' && $subcategory && $child) {
            $childSlug = $subcategory->slug;
            $subChildSlug = $child->slug;
        }

        $params = ['parent' => $parentSlug];
        if ($childSlug) $params['child'] = $childSlug;
        if ($subChildSlug) $params['subchild'] = $subChildSlug;

        return route('category.show', $params);
    }
}

if (!function_exists('getYoutubeVideoId')) {
    function getYoutubeVideoId($url)
    {
        preg_match('/[\\?\\&]v=([^\\?\\&]+)/', $url, $matches);
        return $matches[1] ?? '';
    }
}

if (!function_exists('getVimeoVideoId')) {
    function getVimeoVideoId($url)
    {
        preg_match('/vimeo\.com\/(\d+)/', $url, $matches);
        return $matches[1] ?? '';
    }
}


if (!function_exists('getCurrency')) {
    function getCurrency()
    {
        return 100;
    }
}

if (!function_exists('getCurrencySymbol')) {
    function getCurrencySymbol()
    {
        return 100;
    }
}


if (!function_exists('prx')) {
    function prx($arr, $exit = true)
    {
        echo '<pre>';
        print_r($arr);
        if ($exit) exit;
    }
}

if (!function_exists('getOrderStatuss')) {
    function getOrderStatuss($id = 0, $slug = '')
    {
        $status = Cache::remember('order_statusess', 60 * 60, function () {
            return OrderStatus::all()->keyBy('id')->toArray();
        });
        if ($id > 0 && isset($status[$id])) {
            $status = $status[$id];
        } else if (!empty($slug)) {
            $status = collect($status)->firstWhere('slug', $slug);
        }
        return $status;
    }
}


if (!function_exists('getProductImages')) {
    function getProductImages($productId = 0, $combo = null)
    {
        if (empty($combo) || $productId == 0) {
            return null;
        }
        $valueIds = [];

        foreach ($combo as $variantName => $variantValue) {
            $variantId = DB::table('variants')
                ->where('name', $variantName)
                ->value('id');

            if ($variantId) {
                $valueId = DB::table('variant_values')
                    ->where('variant_id', $variantId)
                    ->where('name', $variantValue)
                    ->value('id');

                if ($valueId) {
                    $valueIds[] = $valueId;
                }
            }
        }

        // Find image matching any of the variant values
        return DB::table('product_graphics')
            ->where('product_id', $productId)
            ->whereIn('variant_id', $valueIds)
            ->orderBy('id', 'Asc')
            ->first();
    }
}

if (!function_exists('getCouriers')) {
    function getCouriers()
    {
        return Cache::remember('couriers_list', 60 * 60, function () {
            return DB::table('couriers')->where('status', 1)->get()->keyBy('id')->toArray();
        });
    }
}

if (!function_exists('orderCancellationRequest')) {
    function orderCancellationRequest($orderId =  0)
    {
        return OrderCancellation::where('order_id', $orderId)->with('admin')->get()->keyBy('order_item_id')?->toArray();
    }
}

if (!function_exists('orderRefundRequest')) {
    function orderRefundRequest($orderId =  0)
    {
        return RefundRequest::where('order_id', $orderId)->with('admin')->get()->keyBy('order_item_id')?->toArray();
    }
}

if (!function_exists('getProductVariantSku')) {
    function getProductVariantSku($pvcId =  0)
    {
        return DB::table('product_variant_combinations')->where('id', $pvcId)->first();
    }
}
if (!function_exists('productSlug')) {
    function productSlug($slug)
    {
        return Str::slug($slug);
    }
}

if (!function_exists('getUserRole')) {
    function getUserRole($id = 0)
    {
        if(empty($id)){
            $user = Auth::user();
            $role = $user?->role?->name;
        } else {
            $user = User::find($id);
            $role = $user?->role?->name;
        }
        return $role;
    }
}

if (!function_exists('getUserDetail')) {
    function getUserDetail($id = 0)
    {
        $userArr = array(); 
        $user = User::find($id);
        if(!empty($user)){
            $userArr = $user->toArray();
        }
        return $userArr;
    }
}

if (!function_exists('refundPayment')) {
    function refundPayment(string $paymentId, ?int $amount = null, ?string $reason = null): array
    {
        $url = "https://api.razorpay.com/v1/payments/{$paymentId}/refund";

        $payload = [];
        if (!is_null($amount)) {
            $payload['amount'] = $amount * 100; // in rupees
        }
        if (!is_null($reason)) {
            $payload['notes'] = ['reason' => $reason];
        }
        $RAZORPAY_KEY = env('RAZORPAY_MODE', 'live') == 'live' ? env('RAZORPAY_LIVE_KEY') : env('RAZORPAY_TEST_KEY');
        $RAZORPAY_SECRET = env('RAZORPAY_MODE', 'live') == 'live' ? env('RAZORPAY_LIVE_SECRET') : env('RAZORPAY_TEST_SECRET');

        $response = Http::withBasicAuth(
            $RAZORPAY_KEY,
            $RAZORPAY_SECRET
        )->post($url, $payload);

        if ($response->successful()) {
            return [
                'success' => true,
                'data' => $response->json(),
            ];
        }

        return [
            'success' => false,
            'error' => $response->json(),
            'status' => $response->status(),
        ];
    }

    if (!function_exists('getCategpryIdByName')) {
        function getCategpryIdByName($name = '')
        {
            $category = 0;
            if(!empty($name)){
                $category = Category::where('name', $name)->value('id') ?? 0;
            } 
            return $category;
        }
    }

    if (!function_exists('getVarientIdByName')) {
        function getVarientIdByName($name = '')
        {
            $varient = 0;
            if(!empty($name)){
                $varient = Varient::where('name', $name)->value('id') ?? 0;
            } 
            return $varient;
        }
    }

    if (!function_exists('getAttributIdByName')) {
        function getAttributIdByName($name = '')
        {
            $attribute = 0;
            if(!empty($name)){
                $attribute = Attribute::where('name', $name)->value('id') ?? 0;
            } 
            return $attribute;
        }
    }

    if (!function_exists('getAttributValueIdByName')) {
        function getAttributValueIdByName($name = '')
        {
            $attribute = 0;
            if(!empty($name)){
                $attribute = AttributeValue::where('name', $name)->value('id') ?? 0;
            } 
            return $attribute;
        }
    }
}