<?php

namespace App\Http\Controllers\Admin;

use App\Config;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Plan;
use App\Models\PlanDetail;
use App\Models\PartnerPlanDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Redirect,DB,Response,Str;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class PartnerController extends Controller
{
    public $model = 'partners';
    public function __construct(Request $request)
    {
        $this->middleware('permission:view_partner|create_partner|edit_partner|delete_partner', ['only' => ['index','store']]);
        $this->middleware('permission:create_partner', ['only' => ['create','store']]);
        $this->middleware('permission:edit_partner', ['only' => ['edit','update']]);
        $this->middleware('permission:delete_partner', ['only' => ['destroy']]);

        $this->listRouteName = 'admin-partners.index';
        View()->share('model', $this->model);
        View()->share('listRouteName', $this->listRouteName);
        $this->request = $request;

    }

    public function index(Request $request)
    {
        $DB = User::query();
        $sortBy = $request->input('sortBy') ? $request->input('sortBy') : 'users.created_at';
        $order = $request->input('order') ? $request->input('order') : 'desc';
        $offset = !empty($request->input('offset')) ? $request->input('offset') : 0 ;
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
                $DB->whereBetween('users.created_at', [$dateS . " 00:00:00", $dateE . " 23:59:59"]);
            } elseif (!empty($searchData['date_from'])) {
                $dateS = $searchData['date_from'];
                $DB->where('users.created_at', '>=', [$dateS . " 00:00:00"]);
            } elseif (!empty($searchData['date_to'])) {
                $dateE = $searchData['date_to'];
                $DB->where('users.created_at', '<=', [$dateE . " 00:00:00"]);
            }
            foreach ($searchData as $fieldName => $fieldValue) {
                if ($fieldValue != "") {
                    if ($fieldName == "name") {
                        $DB->where("users.name", 'like', '%' . $fieldValue . '%');
                    }
                    if ($fieldName == "phone_number") {
                        $DB->where("users.phone_number", 'like', '%' . $fieldValue . '%');
                    }
                    if ($fieldName == "email") {
                        $DB->where("users.email", 'like', '%' . $fieldValue . '%');
                    }
                    if ($fieldName == "is_active") {
                        $DB->where("users.is_active", $fieldValue);
                    }
                }
            }
        }

        $DB->where("users.is_deleted", 0);
        $DB->where("users.user_role_id", config('constant.ROLE_ID.PARTNER_ROLE_ID'));

        $results = $DB->orderBy($sortBy, $order)->offset($offset)->limit($limit)->get();
        $totalResults = $DB->count();

        if(!empty($results)) {
            foreach($results as &$result) {
                if(!empty($result->image)){
                    $result->image = Config('constant.USER_IMAGE_URL').$result->image;
                }
            }
        }
        if($request->ajax()){

            return  View("admin.$this->model.load_more_data", compact('results','totalResults'));
        }else{

            return  View("admin.$this->model.index", compact('results','totalResults'));
        }
    }

    public function create(Request $request)
    {
        $plans = Plan::select('id', 'name')->get();
        return View("admin.$this->model.add",compact('plans'));
    }

    public function edit(Request $request, $enuserid = null)
    {
        $user_id = '';
        if (!empty($enuserid)) {

            $user_id = base64_decode($enuserid);
            $userDetails = User::where('users.id',$user_id)->first();

            if(!empty($userDetails->image)){
                $userDetails->image = Config('constant.USER_IMAGE_URL').$userDetails->image;
            }
            $plans = Plan::select('id', 'name')->get();
            $partnerPlanDetailsData = PartnerPlanDetail::where('user_id',$user_id)->get()->toArray();
            return View("admin.$this->model.edit",['userDetails' => $userDetails, 'plans' => $plans , 'planDetailsData' =>$partnerPlanDetailsData ]);
        }
    }
    public function save(Request $request)
    {

        $formData = $request->all();

        if (!empty($formData)) {
            $validator = Validator::make(
                $request->all(),
                array(

                    'name' => 'required',
                    'email' => 'required|email',
                    'phone_number' =>  ['required','numeric', Rule::unique('users')->where('user_role_id',config('constant.ROLE_ID.CUSTOMER_ROLE_ID')),'digits:10'],
                    'image' => 'nullable|mimes:jpg,jpeg,png',
                    'gender' => 'required',
                    'password'      =>         ['required', Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
                    'confirm_password' =>      'required|same:password',
                ),
                array(
                    "name.required" => trans("The name field is required."),
                    "phone_number.required" => trans("The phone number field is required."),
                    "phone_number.numeric" => trans("The phone number should be numeric."),
                    "email.required" => trans("The email field is required."),
                    "email.email" => trans("The email must be a valid email address"),
                    "email.unique" => trans("The email has already been taken."),
                    "phone_number.unique" => trans("The phone number has already been taken."),
                    "image.required" => trans("The profile image field is required."),
                    "date_of_birth.required" => trans("The date of birth field is required."),
                    "gender.required" => trans("The gender field is required."),
                    "image.mimes" => trans("The profile image should be of type jpeg,jpg,png."),
                )
            );
            if ($validator->fails()) {
                return Redirect::back()->withErrors($validator)->withInput();
            } else {
                DB::beginTransaction();

                $originalString = $request->name ?? "";
                $lowercaseString = Str::lower($originalString);
                $baseSlug = Str::slug($lowercaseString, '-');

                // Check if the base slug already exists
                $alreadyAddedName = User::where('slug', $baseSlug)->first();

                if (!is_null($alreadyAddedName)) {
                    // If the base slug exists and the name is being changed, add a suffix
                    $suffix = 1;

                    while (User::where('slug', $baseSlug . '-' . $suffix)->exists()) {
                        $suffix++;
                    }

                    $slug = $baseSlug . '-' . $suffix;
                } else {
                    $slug = $baseSlug;
                }

                $obj                                = new User;
                $obj->user_role_id                  = config('constant.ROLE_ID.PARTNER_ROLE_ID');
                $obj->slug                          = !empty($slug) ? $slug : "";
                $obj->name                          = $request->input('name');
                $obj->email                         = $request->input('email');
                $obj->phone_number                  = $request->input('phone_number');
                $obj->gender                        = $request->input('gender');
                $obj->password                             =  Hash::make($request->input('password'));
                // if(!empty($request->password)){
                //     $obj->password                      = Hash::make($request->password);
                // }
                if ($request->hasFile('image')) {
                    $extension = $request->file('image')->getClientOriginalExtension();
                    $originalName = $request->file('image')->getClientOriginalName();
                    $fileName = time() . '-image.' . $extension;

                    $folderName = strtoupper(date('M') . date('Y')) . "/";
                    $folderPath = Config('constant.USER_IMAGE_ROOT_PATH') . $folderName;
                    if (!File::exists($folderPath)) {
                        File::makeDirectory($folderPath, $mode = 0777, true);
                    }
                    if ($request->file('image')->move($folderPath, $fileName)) {
                        $obj->image = $folderName . $fileName;
                    }
                }
                $obj->is_verified = 1;
                $obj->is_active = 1;
                $obj->is_approved = 1;
                $obj->plan_id                        = !empty($request->plan_id ) ? $request->input('plan_id') : NULL;
                $obj->payout_period                        = !empty($request->payout_period ) ? $request->input('payout_period') : NULL;

                $obj->save();
                $lastId = $obj->id;
                if(!empty($lastId)){
                    $randomLetters = strtoupper(Str::random(3));

                    $referralCode = $slug . $randomLetters . $lastId;

                    // Save the referral code to the user or do whatever is needed
                    User::where('id', $lastId)->update(["referral_code"=>$referralCode]);

                    if(!empty($request->planDetailsArr) && !empty($request->plan_id)){
                        foreach($request->planDetailsArr as $planKey => $planVal){
                            if(!empty($planVal['sales_from']) && !empty($planVal['sales_to']) && !empty($planVal['type']) && !empty($planVal['amount'])){
                                $planObj = new PartnerPlanDetail;
                                $planObj->user_id = $lastId;
                                $planObj->sales_from = $planVal['sales_from'] ?? 0;
                                $planObj->sales_to = $planVal['sales_to'] ?? 0;
                                $planObj->type = $planVal['type'] ?? NULL;
                                $planObj->amount = $planVal['amount'] ?? 0;
                                $planObj->save();
                                if(empty($planObj->id)){
                                    DB::rollback();
                                    Session()->flash('flash_notice', 'Something Went Wrong');
                                    return Redirect::route('admin-plans.index');
                                }
                            }
                        }
                    }
                    DB::commit();
                }else{
                    DB::rollback();
                    Session()->flash('flash_notice', 'Something Went Wrong');
                    return Redirect::route('admin-partners.index');
                }
                Session()->flash('flash_notice', trans("Partner saved successfully."));
                return Redirect::route('admin-partners.index');
            }
        }

    }
    public function update(Request $request, $enuserid = null)
    {

        $model = User::find($enuserid);
        if (empty($model)) {
            return View("admin.$this->model.edit");
        } else {
            $formData = $request->all();
            if (!empty($formData)) {
                $validator = Validator::make(
                    $request->all(),
                    array(

                        'name' => 'required',
                        'email' => 'nullable|email',
                        'phone_number' => ['required','numeric', Rule::unique('users')->ignore($enuserid)->where('user_role_id',config('constant.ROLE_ID.CUSTOMER_ROLE_ID')),'digits:10'],
                        'image' => 'nullable|mimes:jpg,jpeg,png',
                        'gender' => 'required',
                        'password'      =>  !empty(!empty($request->password)) ? [Password::min(8)->letters()->mixedCase()->numbers()->symbols()] : 'nullable',
                        'confirm_password' =>    !empty(!empty($request->password)) ?  'same:password' : 'nullable',

                    ),
                    array(

                        "name.required" => trans("The name field is required."),
                        "phone_number.required" => trans("The phone number field is required."),
                        "phone_number.numeric" => trans("The phone number should be numeric."),
                        "email.required" => trans("The email field is required."),
                        "email.email" => trans("The email must be a valid email address"),
                        "email.unique" => trans("The email has already been taken."),
                        "phone_number.unique" => trans("The phone number has already been taken."),
                        "image.required" => trans("The profile image field is required."),
                        "image.mimes" => trans("The profile image should be of type jpeg,jpg,png."),
                        "date_of_birth.required" => trans("The date of birth field is required."),
                        "gender.required" => trans("The gender field is required."),
                    )
                );
                if ($validator->fails()) {
                    return Redirect::back()->withErrors($validator)->withInput();
                } else {
                    DB::beginTransaction();
                    $originalString = $request->name ?? "";
                    $lowercaseString = Str::lower($originalString);
                    $baseSlug = Str::slug($lowercaseString, '-');

                    // Check if the base slug already exists
                    $alreadyAddedName = User::where('slug', $baseSlug)->first();

                    if (!is_null($alreadyAddedName)) {
                        // If the base slug exists and the name is being changed, add a suffix
                        if ($alreadyAddedName->name !== $originalString) {
                            $suffix = 1;

                            while (User::where('slug', $baseSlug . '-' . $suffix)->exists()) {
                                $suffix++;
                            }

                            $slug = $baseSlug . '-' . $suffix;
                        } else {
                            // If the name is not being changed, keep the original base slug
                            $slug = !empty($model->slug) ? $model->slug : $baseSlug;
                        }
                    } else {
                        $slug = $baseSlug;
                    }
                    $obj                                = $model;
                    $obj->slug                          = !empty($slug) ? $slug : "";
                    $obj->name                          = $request->input('name');
                    $obj->email                         = $request->input('email');
                    $obj->phone_number                  = $request->input('phone_number');

                    $obj->gender = $request->input('gender');

                    if ($request->hasFile('image')) {
                        $extension = $request->file('image')->getClientOriginalExtension();
                        $originalName = $request->file('image')->getClientOriginalName();
                        $fileName = time() . '-image.' . $extension;

                        $folderName = strtoupper(date('M') . date('Y')) . "/";
                        $folderPath = Config('constant.USER_IMAGE_ROOT_PATH') . $folderName;
                        if (!File::exists($folderPath)) {
                            File::makeDirectory($folderPath, $mode = 0777, true);
                        }
                        if ($request->file('image')->move($folderPath, $fileName)) {
                            $obj->image = $folderName . $fileName;
                        }
                    }
                    if(!empty($request->password)){
                        $obj->password                      = Hash::make($request->password);
                    }

                    $obj->plan_id                        = !empty($request->plan_id ) ? $request->input('plan_id') : NULL;
                    $obj->payout_period                        = !empty($request->payout_period ) ? $request->input('payout_period') : NULL;
                    $obj->save();
                    $lastId = $obj->id;
                    if(!empty($lastId)){
                        if (empty($model->referral_code)) {
                            $randomLetters = strtoupper(Str::random(3));

                            $referralCode = $slug . $randomLetters . $lastId;

                            // Save the referral code to the user or do whatever is needed
                            User::where('id', $lastId)->update(["referral_code"=>$referralCode]);


                        }
                        PartnerPlanDetail::where('user_id',$lastId)->delete();
                        if(!empty($request->planDetailsArr) && !empty($request->plan_id)){
                            foreach($request->planDetailsArr as $planKey => $planVal){
                                if(!empty($planVal['sales_from']) && !empty($planVal['sales_to']) && !empty($planVal['type']) && !empty($planVal['amount'])){
                                    $planObj = new PartnerPlanDetail;
                                    $planObj->user_id = $lastId;
                                    $planObj->sales_from = $planVal['sales_from'] ?? 0;
                                    $planObj->sales_to = $planVal['sales_to'] ?? 0;
                                    $planObj->type = $planVal['type'] ?? NULL;
                                    $planObj->amount = $planVal['amount'] ?? 0;
                                    $planObj->save();
                                    if(empty($planObj->id)){
                                        DB::rollback();
                                        Session()->flash('flash_notice', 'Something Went Wrong');
                                        return Redirect::route('admin-plans.index');
                                    }
                                }
                            }
                        }
                        DB::commit();
                    }else{
                        DB::rollback();
                        Session()->flash('flash_notice', 'Something Went Wrong');
                        return Redirect::route('admin-partners.index');
                    }
                    Session()->flash('flash_notice', trans("Partner updated successfully."));
                    return Redirect::route('admin-partners.index');
                }
            }
        }
    }

    public function destroy($enuserid)
    {
        $user_id = '';
        if (!empty($enuserid)) {
            $user_id = base64_decode($enuserid);
        }
        $userDetails = User::find($user_id);
        if (empty($userDetails)) {
            return Redirect()->route($this->model . '.index');
        }
        if ($user_id) {
            $email = 'delete_' . $user_id . '_' . $userDetails->email;
            $deleted_at = Carbon::now();
            // $phone_number = 'delete_' . $user_id . '_' . !empty($userDetails->phone_number);

            User::where('id', $user_id)->update(array(
                'email' => $email,
                'is_deleted' => 1,
                'deleted_at' => $deleted_at

            ));

            Session()->flash('flash_notice', trans("Partner has been removed successfully."));
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
        $user = User::find($modelId);
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

    public function changedPassword(Request $request, $enuserid = null)
    {
        $user_id = '';
        if (!empty($enuserid)) {
            $user_id = base64_decode($enuserid);
        } else {
            return redirect()->route($this->model . ".index");
        }
        if ($request->isMethod('POST')) {
            if (!empty($user_id)) {
                $validated = $request->validate([
                    'new_password' => ['required', Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
                    'confirm_password' => 'required|same:new_password',
                ]);
                $userDetails = User::find($user_id);
                $userDetails->password = Hash::make($request->new_password);
                $SavedResponse = $userDetails->save();
                if (!$SavedResponse) {
                    Session()->flash('error', trans("Something went wrong."));
                    return Redirect()->back();
                }
                Session()->flash('success', trans("Password changed successfully."));
                return Redirect()->route($this->model . '.index');
            }
        }
        $userDetails = array();
        $userDetails = User::find($user_id);
        $data = compact('userDetails');
        return view("admin.$this->model.change_password", $data);
    }


    public function show(Request $request, $enuserid = null)
    {
        $user_id = '';
        if (!empty($enuserid)) {

            $user_id = base64_decode($enuserid);
            $userDetails = User::where('users.id',$user_id)->first();

            if(!empty($userDetails->image)){
                $userDetails->image = Config('constant.USER_IMAGE_URL').$userDetails->image;
            }

            $data = compact('user_id', 'userDetails');

            return View("admin.$this->model.view", $data);
        }
    }
    public function fetchPlanDetails(Request $request, $planId = null)
    {
        $payout_period = Plan::where('id',$planId)->value('payout_period');
        $planDetailsData = PlanDetail::where('plan_id',$planId)->get()->toArray();
        $data = compact('planDetailsData');
        $htmlData = View("admin.$this->model.plan_details",$data )->render();
        return response()->json(['htmlData' => $htmlData, 'payout_period' => $payout_period]);
    }

}
