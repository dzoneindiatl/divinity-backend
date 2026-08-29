<?php

namespace App\Http\Controllers\Admin;

use App\Config;
use Carbon\Carbon;
use App\Models\Plan;
use App\Models\User;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\PlanDetail;
use App\Models\{Category};
use App\Models\CouponAssign;
use Illuminate\Http\Request;
use App\Exports\CouponExport;
use App\Services\CouponService;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Redirect, Response, Str, Exception;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use App\Http\Requests\Coupon\SaveCouponRequest;
use App\Models\CouponUse;

class CouponController extends Controller
{
    public $model = 'coupons';
    protected $couponService;
    public $listRouteName;
    public function __construct(Request $request, CouponService $couponService)
    {
        $this->middleware('permission:view_coupan|create_coupan|edit_coupan|delete_coupan', ['only' => ['index', 'store']]);
        $this->middleware('permission:create_coupan', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit_coupan', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete_coupan', ['only' => ['destroy']]);


        $this->couponService = $couponService;
        $this->listRouteName = 'admin-coupons.index';
        View()->share('model', $this->model);
        View()->share('listRouteName', $this->listRouteName);
        //$this->request = $request;


    }

    public function index(Request $request)
    {
        $DB = Coupon::query()->with(['couponUses']);
        $sortBy = $request->input('sortBy') ? $request->input('sortBy') : 'coupons.created_at';
        $order = $request->input('order') ? $request->input('order') : 'desc';
        $offset = !empty($request->input('offset')) ? $request->input('offset') : 0;
        $limit =  !empty($request->input('limit')) ? $request->input('limit') : Config("Reading.records_per_page");

        if ($request->all()) {
            $searchData            =    $request->all();
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
                $DB->whereBetween('coupons.created_at', [$dateS . " 00:00:00", $dateE . " 23:59:59"]);
            } elseif (!empty($searchData['date_from'])) {
                $dateS = $searchData['date_from'];
                $DB->where('coupons.created_at', '>=', [$dateS . " 00:00:00"]);
            } elseif (!empty($searchData['date_to'])) {
                $dateE = $searchData['date_to'];
                $DB->where('coupons.created_at', '<=', [$dateE . " 00:00:00"]);
            }
            foreach ($searchData as $fieldName => $fieldValue) {
                if ($fieldValue != "") {
                    if ($fieldName == "name") {
                        $DB->where("coupons.name", 'like', '%' . $fieldValue . '%');
                    }
                    if ($fieldName == "is_active") {
                        $DB->where("coupons.is_active", $fieldValue);
                    }
                }
            }
        }

        // $results = $DB->orderBy($sortBy, $order)->offset($offset)->limit($limit)->get();
        $DB->whereNull("coupons.updated_coupon_id");
        $totalResults = $DB->count();
        $results = $DB->paginate($limit)->appends(request()->query());
        /*  if($request->ajax()){

            return  View("admin.$this->model.load_more_data", compact('results','totalResults'));
        }else{

            return  View("admin.$this->model.index", compact('results','totalResults'));
        } */
        return  View("admin.$this->model.index", compact('results', 'totalResults'));
    }

    public function couponLog(Request $request, $couponId = null)
    {
        $DB = Coupon::query()->with(['couponUses']);
        $DB->where("coupons.updated_coupon_id",$couponId);
        $totalResults = $DB->count();
        $results = $DB->orderBy('id','DESC')->get();
       
        // $results = $DB->query();
        return  View("admin.$this->model.coupon_logs", compact('results', 'totalResults'));
    }

    public function create(Request $request)
    {
        $categories = Category::whereNull('parent_id')
            ->where(['is_active' => 1, 'is_deleted' => 0])
            ->select('id', 'name')->get();
        $users = User::select('id', 'name', 'is_active', 'created_at')
            ->where(['user_role_id' => 3, 'is_deleted' => 0])
            ->orderBy('name', 'asc')
            ->get();

        $coupon  = [];
        if ($request->coupon_id) {
            $coupon = Coupon::findOrFail(base64_decode($request->coupon_id));
        }

        return view("admin.$this->model.create", compact('categories', 'users', 'coupon'));
    }

    public function store(SaveCouponRequest $request, CouponService $couponService)
    {
        //  dd($request->all());

        $ip = $request->ip();
        try {
            $couponService->store($request->all(),$ip);

            Session()->flash('flash_notice', trans("Coupon saved successfully."));
            return redirect()->route('admin-coupons.index');
        } catch (Exception $e) {
            report($e);
            // Session()->flash('flash_notice', $e->getMessage());
            Session()->flash('flash_notice', 'Something went wrong.');
            return redirect()->route('admin-coupons.index');
        }
    }

    public function destroy($enuserid)
    {
        $user_id = '';
        if (!empty($enuserid)) {
            $user_id = base64_decode($enuserid);
        }
        $userDetails = Coupon::find($user_id);
        if (empty($userDetails)) {
            return Redirect()->route($this->model . '.index');
        }
        if ($user_id) {
            Coupon::where('id', $user_id)->delete();

            Session()->flash('flash_notice', trans("Coupon has been removed successfully."));
        }
        return back();
    }

    public function changeStatus($modelId = 0, $status = 0)
    {
        if ($status == 1) {
            $statusMessage = trans("Partner has been actvated successfully");
        } else {
            $statusMessage = trans("Partner has been deactivated successfully");
        }
        $user = Coupon::find($modelId);
        if ($user) {
            $currentStatus = $user->is_active;
            if (isset($currentStatus) && $currentStatus == 0) {
                $NewStatus = 1;
            } else {
                $NewStatus = 0;
            }
            $user->is_active = $NewStatus;
            $ResponseStatus = $user->save();
        }
        Session()->flash('flash_notice', $statusMessage);
        return back();
    }




    public function show(Request $request, $enuserid = null)
    {
        $user_id = '';
        if (!empty($enuserid)) {

            $user_id = base64_decode($enuserid);
            $userDetails = Coupon::where('users.id', $user_id)->first();

            $data = compact('user_id', 'userDetails');

            return View("admin.$this->model.view", $data);
        }
    }
    public function fetchPlanDetails(Request $request, $planId = null)
    {
        $payout_period = Plan::where('id', $planId)->value('payout_period');
        $planDetailsData = PlanDetail::where('plan_id', $planId)->get()->toArray();
        $data = compact('planDetailsData');
        $htmlData = View("admin.$this->model.plan_details", $data)->render();
        return response()->json(['htmlData' => $htmlData, 'payout_period' => $payout_period]);
    }

    public function create_new(Request $request)
    {
        if ($request->isMethod('post')) {
            try {
                $rules = [
                    'name' => 'required',
                    'coupon_code' => 'required',
                    'discount_value' => 'required',
                    'start_date' => 'required',
                    'end_date' => 'required',
                    'category' => 'required'
                ];
                $validator = Validator::make($request->all(), $rules);
                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator);
                }
                DB::beginTransaction();

                //echo "<pre>"; print_r($request->all()); die;
                $coupon = new Coupon();
                $coupon->name = $request->name;
                $coupon->coupon_code = $request->coupon_code;
                $coupon->coupon_type = $request->coupon_type;
                $coupon->user_type = $request->user_type;
                $coupon->discount_type = $request->discount_type;
                $coupon->discount_value = $request->discount_value;
                $coupon->max_discount = $request->max_discount;
                $coupon->min_discount = $request->min_discount;
                $coupon->min_cart_value = $request->min_cart_value;
                $coupon->start_date = $request->start_date;
                $coupon->end_date = $request->end_date;
                $coupon->is_unlimited = $request->is_unlimited;
                $coupon->available_coupons = $request->available_coupons;
                $coupon->category_id = $request->category;
                if (!empty($request->customer_name)) {
                    $coupon->coustomer_names = json_encode($request->customer_name);
                }
                if (!empty($request->sub_category)) {
                    $coupon->sub_categories = json_encode($request->sub_category);
                }
                $coupon->is_active = $request->is_active;
                $coupon->description = $request->description;
                $coupon->save();

                DB::commit();
                return redirect()->route("admin-$this->model.index")->with(['success' => "Coupon Added Successfully."]);
            } catch (Exception $e) {
                DB::rollback();
                Log::error($e);
                return redirect()->back()->with(['error' => $e->getMessage()]);
            }
        } else {
            $categories = Category::whereNull('parent_id')->where('is_active', 1)->where('is_deleted', 0)->pluck('name', 'id');


            $sub_categories = Category::whereNotNull('parent_id')->where('is_active', 1)->where('is_deleted', 0)->pluck('name', 'id');
            $users = User::where('user_role_id', 2)->where('is_active', 1)->where('is_deleted', 0)->select('id', 'name')->get();
            return view("admin.$this->model.create", [
                'categories' => $categories,
                'sub_categories' => $sub_categories,
                'users' => $users
            ]);
        }
    }

    public function edit_new(Request $request, $id = null)
    {
        $id = base64_decode($id);
        if ($request->isMethod('post')) {
            try {
                $rules = [
                    'name' => 'required',
                    'coupon_code' => 'required',
                    'discount_value' => 'required',
                    'start_date' => 'required',
                    'end_date' => 'required',
                    'category' => 'required'
                ];
                //echo "<pre>"; print_r($request->all()); die;
                $validator = Validator::make($request->all(), $rules);
                if ($validator->fails()) {
                    //return Redirect::back()->withErrors($validator);
                    return redirect()->back()->withErrors($validator);
                }
                DB::beginTransaction();

                //echo "<pre>"; print_r($request->all()); die;
                $coupon = Coupon::find($id);
                $coupon->name = $request->name;
                $coupon->coupon_code = $request->coupon_code;
                $coupon->coupon_type = $request->coupon_type;
                $coupon->user_type = $request->user_type;
                $coupon->discount_type = $request->discount_type;
                $coupon->discount_value = $request->discount_value;
                $coupon->max_discount = $request->max_discount;
                $coupon->min_discount = $request->min_discount;
                $coupon->min_cart_value = $request->min_cart_value;
                $coupon->start_date = $request->start_date;
                $coupon->end_date = $request->end_date;
                $coupon->is_unlimited = $request->is_unlimited;
                $coupon->available_coupons = $request->available_coupons;
                $coupon->category_id = $request->category;
                if (!empty($request->customer_name)) {
                    $coupon->coustomer_names = json_encode($request->customer_name);
                }
                if (!empty($request->sub_category)) {
                    $coupon->sub_categories = json_encode($request->sub_category);
                }
                $coupon->is_active = $request->is_active;
                $coupon->description = $request->description;
                $coupon->save();

                DB::commit();
                return redirect()->route("admin-$this->model.index")->with(['success' => "Coupon Updated Successfully."]);
            } catch (Exception $e) {
                DB::rollback();
                Log::error($e);
                return redirect()->back()->with(['error' => $e->getMessage()]);
            }
        } else {
            $couponDetail = Coupon::where('coupons.id', $id)->first();
            $categories = Category::whereNull('parent_id')->where('is_active', 1)->where('is_deleted', 0)->pluck('name', 'id');
            $sub_categories = Category::whereNotNull('parent_id')->where('is_active', 1)->where('is_deleted', 0)->pluck('name', 'id');
            $users = User::where('user_role_id', 2)->where('is_active', 1)->where('is_deleted', 0)->select('id', 'name')->get();

            return View("admin.$this->model.create", ['userDetails' => $couponDetail, 'categories' => $categories, 'sub_categories' => $sub_categories, 'users' => $users]);
        }
    }

    public function getSubCategoryList(Request $request)
    {

        try {
            $cat_id = $request->cat_id;
            $sub_categories = Category::where('parent_id', $cat_id)->where('is_active', 1)->where('is_deleted', 0)->select('id', 'name')->get();

            return response()->json(['data' => $sub_categories, 'success' => true, 'message' => 'Data fetched'], 200);
        } catch (Exception $e) {
            Log::error($e);
            return response()->json(['message' => 'Something is wrong', 'success' => false, 'error_msg' => $e->getMessage()], 500);
        }
    }

    public function getUserTypeList(Request $request)
    {
        try {
            $user_type = $request->user_type;
            // if ($user_type == "new") {
            //     $users = User::select('id', 'name', 'is_active')
            //         ->where('user_role_id', 3)
            //         ->where('is_deleted', 0)
            //         ->where('is_active', 1)
            //         ->where('created_at', '>=', Carbon::now()->subDays(7))
            //         ->orderBy('name', 'asc')
            //         ->get();
            // } else if ($user_type == "existing") {
            //     $users = User::select('id', 'name', 'is_active')
            //         ->where('user_role_id', 3)
            //         ->where('is_deleted', 0)
            //         ->where('is_active', 1)
            //         ->orderBy('name', 'asc')
            //         ->get();
            // } else {
            //     $users = User::select('id', 'name', 'is_active')
            //         ->where('user_role_id', 3)
            //         ->where('is_deleted', 0)
            //         ->orderBy('name', 'asc')
            //         ->get();
            // }

            if ($user_type == "new") {
                $users = User::with('orders')->select('id', 'name', 'is_active')
                    ->where('user_role_id', 3)
                    ->where('is_deleted', 0)
                    ->where('is_active', 1)
                    ->has('orders', '=', 0)
                   // ->where('created_at', '>=', Carbon::now()->subDays(7))
                    ->orderBy('name', 'asc')
                    ->get();
            } else if ($user_type == "existing") {
                $users = User::with('orders')->select('id', 'name', 'is_active')
                    ->where('user_role_id', 3)
                    ->where('is_deleted', 0)
                    ->where('is_active', 1)
                    ->has('orders', '>=', 1)
                    ->orderBy('name', 'asc')
                    ->get();
            } else {
                $users = User::select('id', 'name', 'is_active')
                    ->where('user_role_id', 3)
                    ->where('is_deleted', 0)
                    ->orderBy('name', 'asc')
                    ->get();
            }
            return response()->json(['data' => $users, 'success' => true, 'message' => 'Data fetched'], 200);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json(['message' => 'Something is wrong', 'success' => false, 'error_msg' => $e->getMessage()], 500);
        }
    }

    public function updateDetailPageDisplayStatus(Request $request)
    {
        if ($request->id && in_array($request->status, [0, 1])) {
            // First set all coupons to 0
            Coupon::query()->update(['show_on_detail' => 0]);

            // Then set the selected coupon
            Coupon::where('id', $request->id)
                ->update(['show_on_detail' => $request->status]);

            return response()->json(['status' => 'success']);
        }

        return response()->json(['status' => 'error', 'message' => 'Invalid request'], 400);
    }

    public function getChildCategories($sub_category_id)
    {

        $childCategories = Category::where('parent_id', $sub_category_id)->get();
        // dd($childCategories);

        return response()->json($childCategories);
    }

    public function exportCoupon()
    {
        return Excel::download(new CouponExport, 'couponexport.xlsx');
    }

    public function getCouponUses(Request $request)
    {
        $coupon_id = $request->id;
        $couponUses = CouponUse::where('coupon_id', $coupon_id)->with(['user', 'order'])->get();
        $html = view("admin.$this->model.coupon_uses", ['couponUses' => $couponUses])->render();
        return response()->json(['status' => 'success', 'html' => $html], 200);
    }
}
