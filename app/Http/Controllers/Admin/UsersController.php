<?php

namespace App\Http\Controllers\Admin;

// use App\Config;
use Exception;
use Carbon\Carbon;
use App\Models\City;
use App\Models\User;
use App\Models\State;
use App\Models\Country;
use App\Models\Language;
use App\Models\UserReview;
use App\Models\EmailAction;
use App\Models\UserAddress;
use Illuminate\Support\Str;
use App\Exports\UsersExport;
use App\Imports\UsersImport;
use Illuminate\Http\Request;
use App\Models\EmailTemplate;
use App\Models\ReferralHistory;
use App\Models\ReferralSetting;
use App\Models\RefundedHistory;
use Illuminate\Validation\Rule;
use App\Exports\SubscriberExport;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\FranchiseEnquiryExport;
use App\Exports\WholesaleEnquiryExport;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use App\Models\UserLoginHistory;

class UsersController extends Controller
{
    public $model = 'admin_users';
    public $listRouteName = 'admin_users';
    public $request = null;

    public function __construct(Request $request)
    {
        $this->middleware('permission:view_user|create_user|edit_user|delete_user', ['only' => ['index', 'show']]);
        $this->middleware('permission:create_user', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit_user', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete_user', ['only' => ['destroy']]);

        $this->listRouteName = 'admin-admin_users.index';
        View()->share('model', $this->model);
        View()->share('listRouteName', $this->listRouteName);
        $this->request = $request;
    }

    public function index(Request $request)
    {
		$DB = User::with(['addresses.city', 'addresses.state', 'addresses.country', 'orders', 'refundedhistorys', 'referralhistorys']);
        $sortBy = $request->input('sortBy') ? $request->input('sortBy') : 'users.created_at';
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
                    if ($fieldName == "role") {
                        $DB->where("users.user_role_id", $fieldValue);
                    }
                }
            }
        }

        $DB->where("users.is_deleted", 0);
        $DB->where('user_role_id', '!=', 3);
        $results = $DB->orderBy($sortBy, $order)->offset($offset)->limit($limit)->get();
		$totalResults = $DB->count();
        $roles = Role::all()->pluck('name', 'id');
        // if(!empty($results)) {
        //     foreach($results as &$result) {
        //         if(!empty($result->image)){
        //             $result->image = Config('constant.USER_IMAGE_URL').$result->image;
        //         }
        //     }
        // }

        //echo "<pre>@@@"; print_r($results); exit;
        if ($request->ajax()) {

            return  View("$this->model.load_more_data", compact('results', 'totalResults', 'roles'));
        } else {

            return  View("admin.$this->model.index", compact('results', 'totalResults', 'roles'));
        }
    }

    public function customers(Request $request)
    {
		$DB = User::with(['addresses.city', 'addresses.state', 'addresses.country', 'orders', 'refundedhistorys', 'referralhistorys']);
        $sortBy = $request->input('sortBy') ? $request->input('sortBy') : 'users.created_at';
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
                    if ($fieldName == "role") {
                        $DB->where("users.user_role_id", $fieldValue);
                    }
                }
            }
        }

        $DB->where("users.is_deleted", 0);
        $DB->where("users.user_role_id", 3 );
        $results = $DB->orderBy($sortBy, $order)->offset($offset)->limit($limit)->get();
		$totalResults = $DB->count();
        $roles = Role::all()->pluck('name', 'id');
        // if(!empty($results)) {
        //     foreach($results as &$result) {
        //         if(!empty($result->image)){
        //             $result->image = Config('constant.USER_IMAGE_URL').$result->image;
        //         }
        //     }
        // }

        //echo "<pre>@@@"; print_r($results); exit;
        if ($request->ajax()) {

            return  View("$this->model.load_more_data", compact('results', 'totalResults', 'roles'));
        } else {

            return  View("admin.$this->model.index", compact('results', 'totalResults', 'roles'));
        }
    }

    public function create(Request $request)
    {
        $roles = Role::where('status', true)->pluck('name', 'id')->all();
        return View("admin.$this->model.add", compact('roles'));
    }

    public function edit(Request $request, $enuserid = null)
    {
        $user_id = '';
        if (!empty($enuserid)) {

            $user_id = base64_decode($enuserid);
            $userDetails = User::where('users.id', $user_id)->first();

            // if(!empty($userDetails->image)){
            //     $userDetails->image = Config('constant.USER_IMAGE_URL').$userDetails->image;
            // }
            
            //$roles = Role::where('status', true)->pluck('name', 'id')->all();
            $roles = Role::pluck('name', 'id')->all();
            return View("admin.$this->model.edit", compact('userDetails', 'roles'));
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
                    'phone_number' =>  ['nullable', 'numeric', Rule::unique('users')->where('user_role_id', config('constant.ROLE_ID.CUSTOMER_ROLE_ID')), 'digits:10'],
                    // 'image' => 'nullable|mimes:jpg,jpeg,png',
                    'gender' => 'nullable',
                    'roles' => 'required',
                    'date_of_birth' => 'nullable',
                    'password'      => ['required', Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
                    'confirm_password' => 'required|same:password',
                ),
                array(
                    "name.required" => trans("The name field is required."),
                    "phone_number.required" => trans("The phone number field is required."),
                    "phone_number.numeric" => trans("The phone number should be numeric."),
                    "email.required" => trans("The email field is required."),
                    "email.email" => trans("The email must be a valid email address"),
                    "email.unique" => trans("The email has already been taken."),
                    "phone_number.unique" => trans("The phone number has already been taken."),
                    // "image.required" => trans("The profile image field is required."),
                    "date_of_birth.required" => trans("The date of birth field is required."),
                    "gender.nullable" => trans("The gender field is required."),
                    "roles.required" => trans("The role field is required."),
                    //"image.mimes" => trans("The profile image should be of type jpeg,jpg,png."),
                )
            );
            if ($validator->fails()) {
                return Redirect::back()->withErrors($validator)->withInput();
            } else {
                DB::beginTransaction();

                $originalString = ucwords($request->name);
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

                $image = $request->file('image');
                if (isset($image)) {
                    $imageName = time() . '.' . $image->getClientOriginalExtension();
                    //$image->move(public_path('uploads/users'), $imageName); // save in public/uploads
                    $image->move(Config('constant.USER_IMAGE_ROOT_PATH'), $imageName);
                }


                $obj                                = new User;
                $obj->user_role_id                  = $request->input('roles');
                $obj->slug                          = !empty($slug) ? $slug : "";
                $obj->name                          = $request->input('name');
                $obj->email                         = $request->input('email');
                $obj->phone_number                  = $request->input('phone_number');
                $obj->wallet_avl_balance            = $request->input('wallet_avl_balance');
                $obj->date_of_birth =   !empty($request->input('date_of_birth')) ? date('Y-m-d', strtotime($request->input('date_of_birth'))) : NULL;
                $obj->gender = $request->input('gender');
                $obj->password =  Hash::make($request->input('password'));
                $obj->image =  $imageName ?? '';

                $obj->is_verified = 1;
                $obj->is_active = 1;
                $obj->is_approved = 1;

                $obj->save();
                $lastId = $obj->id;
                // $obj->syncRoles($request->roles);
                if (!empty($lastId)) {
                    $role = Role::find($request->roles);
                    if ($role) {
                        $obj->assignRole($role->name);
                    }
                    $randomLetters = strtoupper(Str::random(3));

                    $referralCode = $slug . $randomLetters . $lastId;

                    // Save the referral code to the user or do whatever is needed
                    User::where('id', $lastId)->update(["referral_code" => $referralCode]);

                    // if (!empty($request->input('email'))) {
                    //     $settingsEmail         =  Config::get('Site.from_email');
                    //     $full_name            =  $request->input('name');
                    //     $email              =  $request->input('email');
                    //     $emailActions        =     EmailAction::where('action', '=', 'registration')->get()->toArray();
                    //     $emailTemplates        =     EmailTemplate::where('action_id', '=', $emailActions[0])->select("name", "action", "subject", "body")->get()->toArray();

                    //     $cons                 =     explode(',', $emailActions[0]['options']);
                    //     $constants             =     array();
                    //     foreach ($cons as $key => $val) {
                    //         $constants[] = '{' . $val . '}';
                    //     }
                    //     $subject             =  $emailTemplates[0]['subject'];
                    //     $rep_Array             =     array($full_name);
                    //     $messageBody        =   str_replace($constants, $rep_Array, $emailTemplates[0]['body']);
                    //     $this->sendMail($email, $full_name, $subject, $messageBody, $settingsEmail);
                    // }

                    DB::commit();
                } else {
                    DB::rollback();
                    Session()->flash('flash_notice', 'Something Went Wrong');
                    return Redirect::route('admin-admin_users.index');
                }
                Session()->flash('flash_notice', trans("User saved successfully."));
                return Redirect::route('admin-admin_users.index');
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
                        'email' => 'required|email',
                        'phone_number' => ['nullable', 'numeric', Rule::unique('users')->ignore($enuserid)->where('user_role_id', config('constant.ROLE_ID.CUSTOMER_ROLE_ID')), 'digits:10'],
                        //'image' => 'nullable|mimes:jpg,jpeg,png',
                        'gender' => 'nullable',
                        'roles' => 'required',
                        // 'date_of_birth' => 'required',
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
                        //"image.required" => trans("The profile image field is required."),
                        // "image.mimes" => trans("The profile image should be of type jpeg,jpg,png."),
                        // "date_of_birth.required" => trans("The date of birth field is required."),
                        "gender.nullable" => trans("The gender field is required."),
                        "roles.required" => trans("The role field is required."),
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
                    $obj->name                          = $request->input('name');
                    $obj->slug                          = !empty($slug) ? $slug : "";
                    $obj->email                         = $request->input('email');
                    $obj->phone_number                  = $request->input('phone_number');
                    $obj->wallet_avl_balance            = $request->input('wallet_avl_balance');

                    $obj->date_of_birth =   !empty($request->input('date_of_birth')) ? date('Y-m-d', strtotime($request->input('date_of_birth'))) : NULL;
                    $obj->gender = $request->input('gender');
                    $obj->wallet_active                 = $request->input('wallet_active');
                    $obj->user_role_id                  = $request->input('roles');


                    // if ($request->hasFile('image')) {
                    //     $extension = $request->file('image')->getClientOriginalExtension();
                    //     $originalName = $request->file('image')->getClientOriginalName();
                    //     $fileName = time() . '-image.' . $extension;

                    //     $folderName = strtoupper(date('M') . date('Y')) . "/";
                    //     $folderPath = Config('constant.USER_IMAGE_ROOT_PATH') . $folderName;
                    //     if (!File::exists($folderPath)) {
                    //         File::makeDirectory($folderPath, $mode = 0777, true);
                    //     }
                    //     if ($request->file('image')->move($folderPath, $fileName)) {
                    //         $obj->image = $folderName . $fileName;
                    //         // $obj->original_image_name = $originalName;
                    //     }
                    // }

                    $image = $request->file('image');
                    if (isset($image)) {
                        $imageName = time() . '.' . $image->getClientOriginalExtension();
                        //$image->move(public_path('uploads/users'), $imageName); // save in public/uploads
                        $image->move(Config('constant.USER_IMAGE_ROOT_PATH'), $imageName);
                        $obj->image =  $imageName ?? '';
                    }



                    if (!empty($request->password)) {
                        $obj->password                      = Hash::make($request->password);
                    }
                    $obj->save();
                    //$obj->syncRoles($request->roles);
                    $lastId = $obj->id;
                    if (!empty($lastId)) {
                        $role = Role::find($request->roles);
                        if ($role) {
                            $obj->syncRoles($role->name);
                        }
                        // if (empty($model->referral_code)) {
                        //     $randomLetters = strtoupper(Str::random(3));

                        //     $referralCode = $slug . $randomLetters . $lastId;

                        //     // Save the referral code to the user or do whatever is needed
                        //     User::where('id', $lastId)->update(["referral_code" => $referralCode]);
                        // }

                        DB::commit();
                    } else {
                        DB::rollback();
                        Session()->flash('flash_notice', 'Something Went Wrong');
                        return Redirect::route('admin-admin_users.index');
                    }
                    Session()->flash('flash_notice', trans("User updated successfully."));
                    return Redirect::route('admin-admin_users.index');
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

            Session()->flash('flash_notice', trans("User has been removed successfully."));
        }

        return Redirect::route('admin-admin_users.index');
    }

    public function changeStatus($modelId = 0, $status = 0)
    {
        if ($status == 1) {
            $statusMessage = trans("User has been actvated successfully");
        } else {
            $statusMessage = trans("User has been deactivated successfully");
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
            $userDetails = User::with([
                'addresses.city',
                'addresses.state',
                'addresses.country',
                'orders.items.product'
            ])->where('users.id', $user_id)->first();
            // if(!empty($userDetails->image)){
            //     $userDetails->image = Config('constant.USER_IMAGE_URL').$userDetails->image;
            // }
            $states = State::where('is_active', 1)->get();
            $countries = Country::where('is_active', 1)->get();
            $data = compact('user_id', 'userDetails', 'states', 'countries');
            // dd($userDetails->addresses[0]->city->name);
            return View("admin.$this->model.view", $data);
        }
    }

    public function loginhistory(Request $request)
    {
        if (!empty($request->user_id)) {
            $user_id = $request->user_id;
            $userLoginHistory = UserLoginHistory::where('user_id', $user_id)->orderBy('id','DESC')->limit(5)->get();
            $historyHtml = '';
            if(!empty($userLoginHistory)){
                $i= 1;
                foreach($userLoginHistory as $result){
                    $user_name= '';
                    $userObj = User::find($result->user_id);
                    if(!empty($userObj)){
                        $user_name  = $userObj->name;
                    }
 
                    $historyHtml .= '<tr class="list-data-row" data-total-count="4">
                        <td>'.$i.'</td>
                        <td>'. $user_name .'</td>
                        <td>'. $result->ip .'</td>
                        <td>'. $result->login_time .'</td>
                    </tr>';
                    $i++;
                }
            } else {
                $historyHtml .='<tr><td>Record Not Found</td></tr>';
            }
            return $historyHtml;
        }
    }

    public function exportUsers()
    {
        return Excel::download(new UsersExport, 'users.xlsx');
    }

    public function exportWholesaleEnquiry()
    {
        return Excel::download(new WholesaleEnquiryExport, 'wholesale.xlsx');
    }

    public function exportFranchiseEnquiryExport()
    {
        return Excel::download(new FranchiseEnquiryExport, 'franchise.xlsx');
    }

    public function exportSubscriber()
    {
        return Excel::download(new SubscriberExport, 'subscriber.xlsx');
    }


    public function importUsers()
    {
        return view("admin.$this->model.import");
    }

    public function importUsersSave(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv',
        ]);
        Excel::import(new UsersImport(), $request->file('file'));
        Session()->flash('flash_notice', trans("File imported successfully."));
        return Redirect::route('admin-admin_users.index');
    }
    public function getChildByParent(Request $request)
    {
        $data = [];
        $type = $request->type;

        // dd($request->parent_id);
        if ($type == 'state') {
            $data = State::where(['country_id' => $request->parent_id, 'is_active' => 1])->pluck('name', 'id');
        } elseif ($type == 'city') {
            $data = City::where(['state_id' => $request->parent_id, 'is_active' => 1])->pluck('name', 'id');
        }
        return response()->json(['data' => $data, 'success' => true, 'message' => 'Data fetched'], 200);
    }
    public function user_address_edit(Request $request, $addressId)
    {
        // dd($addressId);
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone_number' => 'required|numeric',
            'alternate_number' => 'nullable|numeric',
            'country' => 'required|numeric',
            'address' => 'required|max:255',
            'city' => 'required|numeric',
            'state' => 'nullable|numeric',
        ]);
        $user_address = UserAddress::find($addressId);
        $user_address->user_id = $request->user_id ?? '';
        $user_address->name    = $request->name     ?? '';
        $user_address->email    = $request->email     ?? '';
        $user_address->phone_number    = $request->phone_number     ?? '';
        $user_address->alternate_number    = $request->alternate_number     ?? '';
        $user_address->country_id    = $request->country     ?? '';
        $user_address->address    = $request->address     ?? '';
        $user_address->postal_code    = $request->postal_code     ?? '';
        $user_address->city_id    = $request->city     ?? '';
        $user_address->state_id    = $request->state ?? '';
        $user_address->landmark = $request->landmark ?? '';

        // dd($user_address);
        if ($user_address->save()) {
            $response = array();
            $response["status"] = "success";
            $response["msg"] = "Updated successfully";
            $response["http_code"] = 200;
            return Response::json($response, 200);
        } else {
            $response = array();
            $response["status"] = "error";
            $response["msg"] = "Failed to update";
            $response["http_code"] = 500;
            return Response::json($response, 500);
        }
    }
    public function user_address_save(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone_number' => 'required|numeric',
            'alternate_number' => 'nullable|numeric',
            'country' => 'required|numeric',
            'address' => 'required|max:255',
            'city' => 'required|numeric',
            'state' => 'nullable|numeric',
        ]);

        $user_address = new UserAddress;
        $user_address->user_id = $request->user_id ?? '';
        $user_address->name    = $request->name     ?? '';
        $user_address->email    = $request->email     ?? '';
        $user_address->phone_number    = $request->phone_number     ?? '';
        $user_address->alternate_number    = $request->alternate_number     ?? '';
        $user_address->country_id    = $request->country     ?? '';
        $user_address->address    = $request->address     ?? '';
        $user_address->postal_code    = $request->postal_code     ?? '';
        $user_address->city_id    = $request->city     ?? '';
        $user_address->state_id    = $request->state ?? '';
        $user_address->landmark = $request->landmark ?? '';

        // dd($user_address);
        if ($user_address->save()) {
            $response = array();
            $response["status"] = "success";
            $response["msg"] = "Updated successfully";
            $response["http_code"] = 200;
            return Response::json($response, 200);
        } else {
            $response = array();
            $response["status"] = "error";
            $response["msg"] = "Failed to update";
            $response["http_code"] = 500;
            return Response::json($response, 500);
        }
    }

    public function UserProductreview($token)
    {
        $userId = decrypt($token);
        try {

            $result = [];
            $colorArray = [];
            $productReviews = UserReview::where('user_id', $userId)->where('is_deleted', '0')->get();
            $totalResults = $productReviews->count();
            return view('admin.admin_users.review', compact('productReviews', 'result', 'totalResults'));
        } catch (Exception $e) {
            Log::error($e);
            return redirect()->back()->with(['error' => 'Something is wrong', 'error_msg' => $e->getMessage()]);
        }
    }

    public function editReview(Request $request, $token = null)
    {
        try {
            $reviewId = '';
            if (!empty($token)) {
                $reviewId = base64_decode($token);
                $review = UserReview::find($reviewId);
                $user = User::where('is_deleted', 0)->where('is_active', 1)->select('id', 'name')->get();
                // echo "<pre>"; print_r($categoryTaxes); die;
                return View("admin.admin_users.reviewedit", compact('review', 'user'));
            }
        } catch (Exception $e) {
            Log::error($e);
            return redirect()->back()->with(['error' => 'Something is wrong', 'error_msg' => $e->getMessage()]);
        }
    }


    public function updateReview(Request $request, $encodedReviewId, $encodedUserId)
    {
        try {
            // Log the request data
            // Log::info('Request Data:', $request->all());

            // Decode IDs and find the review
            $reviewId = base64_decode($encodedReviewId);
            $userId = base64_decode($encodedUserId);
            $review = UserReview::find($reviewId);

            // Validate the incoming request
            $request->validate([
                'rating' => 'required|integer|between:1,5',
                'title' => 'required|string|min:5',
                'review' => 'required|string|max:1000',
            ]);

            // Update and save the review
            $review->rating = $request->input('rating');
            $review->title = $request->input('title');
            $review->review = $request->input('review');
            $review->save();

            session()->flash('flash_notice', trans("Review updated successfully."));
            return redirect()->route('admin-admin_users.user-review', ['token' => encrypt($userId)]);
        } catch (Exception $e) {
            Log::error($e);
            return redirect()->back()->with(['error' => 'Something went wrong: ' . $e->getMessage()]);
        }
    }


    public function changeStatusReview($modelId = 0, $status = 0)
    {
        if ($status == 1) {
            $statusMessage = trans("Review has been actvated successfully");
        } else {
            $statusMessage = trans("Review has been deactivated successfully");
        }
        $review = UserReview::find($modelId);
        if ($review) {
            $currentStatus = $review->is_active;
            if (isset($currentStatus) && $currentStatus == 0) {
                $NewStatus = 1;
            } else {
                $NewStatus = 0;
            }
            $review->is_active = $NewStatus;
            $ResponseStatus = $review->save();
        }
        Session()->flash('flash_notice', $statusMessage);
        return back();
    }

    public function reviewDelete($encodedReviewId, $encodedUserId)
    {
        try {
            // $reviewId = '';
            // if (!empty($encodedReviewId)) {
            //     $reviewId = base64_decode($encodedReviewId);
            // }
            $reviewId = base64_decode($encodedReviewId);
            $userId = base64_decode($encodedUserId);
            $review = UserReview::find($reviewId);
            // if (empty($review)) {
            //     return redirect()->route('admin-product-review', ['token' => base64_encode($userId)]);
            // }
            if ($review) {
                UserReview::where('id', $reviewId)->update(array(
                    'is_deleted' => 1
                ));
                // CategoryVariant::where('category_id',$reviewId)->delete();
                // CategorySpecification::where('category_id',$reviewId)->delete();

                Session()->flash('flash_notice', trans("Review has been removed successfully."));
            }
            return back();
        } catch (Exception $e) {
            Log::error($e);
            return redirect()->back()->with(['error' => 'Something is wrong', 'error_msg' => $e->getMessage()]);
        }
    }

    public function UserReferralHistories($token)
    {
        $userId = decrypt($token);
        try {

            $result = [];
            $colorArray = [];
            //  $referralHistory = ReferralHistory::where('referral_by', $userId)->where('is_deleted', '0')->get();   
            $referralHistory = ReferralHistory::with('user') // Eager load the 'user' relationship
                ->where('referral_by', $userId)
                ->where('is_deleted', '0')
                ->get();
            $referralHistoryto = ReferralHistory::with('user') // Eager load the 'user' relationship
                ->where('referral_to', $userId)
                ->where('is_deleted', '0')
                ->get();
            $totalReferrals = $referralHistory->count();
            return view('admin.admin_users.user-referral-histories', compact('referralHistory', 'referralHistoryto', 'result', 'totalReferrals'));
        } catch (Exception $e) {
            Log::error($e);
            return redirect()->back()->with(['error' => 'Something is wrong', 'error_msg' => $e->getMessage()]);
        }
    }


    public function UserRefundedHistories($token)
    {
        $userId = decrypt($token);
        try {

            $result = [];
            $colorArray = [];
            $refundedHistory = RefundedHistory::with('user') // Eager load the 'user' relationship
                ->where('user_id', $userId)
                ->where('is_deleted', '0')
                ->get();
            $totalRefunded = $refundedHistory->count();
            return view('admin.admin_users.user-refunded-histories', compact('refundedHistory', 'result', 'totalRefunded'));
        } catch (Exception $e) {
            Log::error($e);
            return redirect()->back()->with(['error' => 'Something is wrong', 'error_msg' => $e->getMessage()]);
        }
    }


    public function RefundedApprovalStatus($modelId = 0, $status = 0)
    {
        if ($status == 1) {
            $statusMessage = trans("Refunded  Approved");
        } else {
            $statusMessage = trans("Refunded has been disapproved successfully");
        }
        $refunded = RefundedHistory::find($modelId);
        if ($refunded) {
            $currentStatus = $refunded->is_active;
            $user_id =  $refunded->user_id;
            if (isset($currentStatus) && $currentStatus == 0) {
                $NewStatus = 1;
            } else {
                $NewStatus = 0;
            }
            $refunded->is_active = $NewStatus;
            $refunded->save();

            $UserDetails = User::where('id', $user_id)->first();
            if (!empty($UserDetails)) {
                $UserDetails->refund_wallet += $refunded->amount;
                $UserDetails->wallet_avl_balance += $refunded->amount;
                $UserDetails->save();
            }
        }
        Session()->flash('flash_notice', $statusMessage);
        return back();
    }


    public function RefundedApprovalEdit(Request $request, $token = null)
    {
        try {
            $refundId = '';
            if (!empty($token)) {
                $refundId = base64_decode($token);
                $refund = RefundedHistory::find($refundId);
                $user = User::where('is_deleted', 0)->where('is_active', 1)->select('id', 'name')->get();
                return View("admin.admin_users.refundedapprovaledit", compact('refund', 'user'));
            }
        } catch (Exception $e) {
            Log::error($e);
            return redirect()->back()->with(['error' => 'Something is wrong', 'error_msg' => $e->getMessage()]);
        }
    }

    public function updateRefundedApproval(Request $request, $encodedRefundId, $encodedUserId)
    {
        try {
            // Decode IDs and find the review
            $refundId = base64_decode($encodedRefundId);
            $userId = base64_decode($encodedUserId);
            $refund = RefundedHistory::find($refundId);

            // Validate the incoming request
            // $request->validate([
            //     'rating' => 'required|integer|between:1,5',
            //     'review' => 'required|string|max:1000',
            // ]);

            // Update and save the review

            $refund->amount = $request->input('amount');
            $refund->save();

            session()->flash('flash_notice', trans("Refund Amount updated successfully."));
            return redirect()->route('admin-admin_users.user-refunded-histories', ['token' => encrypt($userId)]);
        } catch (Exception $e) {
            Log::error($e);
            return redirect()->back()->with(['error' => 'Something went wrong: ' . $e->getMessage()]);
        }
    }

    public function syncOldUserRole()
    {
        $roleMap = Role::all()->keyBy('id');
        $users = User::whereNotNull('user_role_id')->get();
        foreach ($users as $user) {
            $oldRoleId = $user->user_role_id;
            if (isset($roleMap[$oldRoleId])) {
                $roleName = $roleMap[$oldRoleId]->name;
                $user->syncRoles($roleName);
            }
        }
        return redirect()->back()->with('success', 'Users Role has been synced!');
    }
    public function toggleStatus($id)
    {
        $result = Role::findOrFail($id);
        $result->status = $result->status == 1 ? 0 : 1;
        $result->save();

        return response()->json(['status' => $result->status]);
    }
}
